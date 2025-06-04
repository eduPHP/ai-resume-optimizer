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
