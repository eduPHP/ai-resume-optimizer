<?php

use App\DTO\Contracts\AIAgentPrompter;
use Tests\Support\FakeAIResponse;
use Tests\Support\AiAgentPrompterStub;

test('it sends the resume content as a prompt to AI', function () {
    $optimization = \App\Models\Optimization::factory()->create([
        'role_description' => $desc = "we require a wizard unicorn ninja developer!",
        'resume_id' => \App\Models\Resume::factory()->create([
            'detected_content' => $resume = 'All looks great for me, no need to change anything at all.'
        ])->id,
    ]);

    $this->instance(AIAgentPrompter::class, new AiAgentPrompterStub);

    $response = $this->actingAs($optimization->user)
        ->withHeader('X-CurrentStep', 3)
        ->put(route('optimizations.update', $optimization), []);

    $response->assertSuccessful();

    $this->assertStringContainsString($desc, $response->json('optimization.role_description'));
    $this->assertStringContainsString((new FakeAIResponse)->getResume(), $response->json('optimization.optimized_result'));
});
