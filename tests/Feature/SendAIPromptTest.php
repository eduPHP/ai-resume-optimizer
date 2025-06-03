<?php

use App\DTO\Contracts\AIAgentPrompter;

class FakeAIResponse implements \App\DTO\Contracts\AIResponseDTO, \Illuminate\Contracts\Support\Arrayable {

    public function getResume(): string
    {
        return "All looks great for me, no need to change anything at all.";
    }

    public function getReasoning(): string
    {
        return 'Beer drinking is such a great skill to have on a job interview.';
    }

    public function getCompatibilityScore(): string
    {
        return "";
    }

    public function getProfessionalSummary(): string
    {
        return "";
    }

    public function getStrongAlignments(): array
    {
        return [];
    }

    public function getModerateGaps(): array
    {
        return [];
    }

    public function getMissingRequirements(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [];
    }
}

class AiAgentPrompterStub implements AiAgentPrompter {

    public function handle(string $resume, \App\DTO\AIInputOptions $options): \App\DTO\Contracts\AIResponseDTO
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
    $this->assertStringContainsString((new FakeAIResponse)->getResume(), $response->json('response'));
});
