<?php

use Illuminate\Support\Facades\Http;

test('it sends the resume content as a prompt to OpenAI', function () {
    $content = "Hey, I'm good";
    $role_details = "we require a wizard unicorn ninja developer!";
    $user = \App\Models\User::factory()->create();

    $prompt = "
Please improve the following resume, following the USA pattern and best practices for a higher employee selection rate
Role:
{$role_details}

Resume:
{$content}
";

    Http::preventStrayRequests();
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-sample.json'), true
        )),
    ]);

    $response = $this->withToken($user->api_token)->postJson('api/optimize', [
        'content' => $content,
        'role_details' => $role_details,
    ]); // updated

    $response->assertSuccessful();

    $this->assertStringContainsString($prompt, $response->json('prompt'));
    $this->assertStringContainsString('TDD', $response->json('response'));
    $this->assertStringContainsString(
        'It features a professional summary introducing Eduardo succinctly',
        $response->json('reasoning')
    );
});
