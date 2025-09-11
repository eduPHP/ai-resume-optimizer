<?php

use App\DTO\Contracts\AIAgentPrompter;
use App\Exceptions\RequestException;

beforeEach(function () {
    \Illuminate\Support\Facades\Http::preventStrayRequests();
    $this->instance(AiAgentPrompter::class, app('agents.local'));
});


test('it sends the resume content as a prompt to OpenAI', function () {
    $user = \App\Models\User::factory()->create();
    $optimization = \App\Models\Optimization::factory()->create([
        'user_id' => $user->id,
        'role_description' => $desc = "we require a wizard unicorn ninja developer!",
        'resume_id' => \App\Models\Resume::factory()->create([
            'user_id' => $user->id,
        ])->id,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        config('local-agent.endpoint') => \Illuminate\Support\Facades\Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-local-agent.json'), true
        )),
    ]);

    $response = $this->actingAs($optimization->user)
        ->withHeader('X-CurrentStep', 3)
        ->put(route('optimizations.update', $optimization), []);

    $response->assertSuccessful();

    $this->assertStringContainsString('TDD', $response->json('optimization.optimized_result'));
    $this->assertStringContainsString(
        'this candidate shows moderate potential for the role',
        $response->json('optimization.ai_response.reasoning')
    );
    $this->assertSame(70, $response->json('optimization.ai_response.compatibility_score'));
});

test('it throws exception from OpenAI bad response', function () {
    $optimization = \App\Models\Optimization::factory()->create([
        'role_description' => 'We need a good one',
        'resume_id' => \App\Models\Resume::factory()->create([
            'detected_content' => 'All right then, I\'m good :).',
        ])->id,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        config('local-agent.endpoint') => \Illuminate\Support\Facades\Http::response(json_decode(
            '{"error": {"message": "mocked"}}', true
        ), 400),
    ]);

    $response = $this->actingAs($optimization->user)
        ->withHeader('X-CurrentStep', 3)
        ->put(route('optimizations.update', $optimization), []);

    $this->assertEquals($response->json(), 'mocked');
})->throws(RequestException::class);
