<?php

use App\Contracts\AIAgentPrompter;
use Illuminate\Support\Facades\Http;

class FakeAIResponse implements \App\Contracts\AIResponseDTO {

    public function getResponse(): string
    {
        return "All looks great for me, no need to change anything at all.";
    }

    public function getReasoning(): string
    {
        return 'Beer drinking is such a great skill to have on a job interview.';
    }
}

class AiAgentPrompterStub implements AiAgentPrompter {

    public function handle(string $prompt): \App\Contracts\AIResponseDTO
    {
        return new FakeAIResponse;
    }
}

test('it sends the resume content as a prompt to AI', function () {
    $resume = \App\Models\Resume::factory()->create([
        'role_details' => "we require a wizard unicorn ninja developer!",
    ]);

    $user = \App\Models\User::factory()->create();

    $prompt = "
Please improve the following resume, following the USA pattern and best practices for a higher employee selection rate
Role:
{$resume->role_details}

Resume:
{$resume->content}
";

    $this->instance(AIAgentPrompter::class, new AiAgentPrompterStub);

    $response = $this->withToken($user->api_token)->postJson("api/optimize/{$resume->id}}", [
        'role_details' => $resume->role_details,
    ]);

    $response->assertSuccessful();

    $this->assertStringContainsString($prompt, $response->json('prompt'));
    $this->assertStringContainsString((new FakeAIResponse)->getResponse(), $response->json('response'));
});
