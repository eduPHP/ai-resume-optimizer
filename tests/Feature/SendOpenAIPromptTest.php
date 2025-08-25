<?php

use Illuminate\Support\Facades\Http;
use \App\Services\Crawler\JobCrawler;

it('tries to get indeed data', function() {
    $crawl = new JobCrawler();

    dd($crawl->crawl('https://br.indeed.com/viewjob?jk=064fb3c87a9e2eea&tk=1j3fs5o3t20me02o&from=serp&vjs=3'));
})->only();


test('it sends the resume content as a prompt to OpenAI', function () {
    $optimization = \App\Models\Optimization::factory()->create([
        'role_description' => $desc = "we require a wizard unicorn ninja developer!",
        'resume_id' => \App\Models\Resume::factory()->create([
            'detected_content' => $resume = 'Hey, I\'m good',
        ])->id,
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'https://api.openai.com/v1/responses' => Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-sample.json'), true
        )),
    ]);

    $response = $this->actingAs($optimization->user)
        ->withHeader('X-CurrentStep', 3)
        ->put(route('optimizations.update', $optimization), []);

    $response->assertSuccessful();

    $this->assertStringContainsString('TDD', $response->json('optimization.optimized_result'));
    $this->assertStringContainsString(
        'Canadian employers will appreciate your readiness and impact!',
        $response->json('optimization.ai_response.reasoning')
    );
});
