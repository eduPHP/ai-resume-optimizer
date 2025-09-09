<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Http::preventStrayRequests();

    Http::fake([
        'https://api.firecrawl.dev/v2/scrape' => Http::response(
            file_get_contents(__DIR__ . '/../Fixtures/firecrawl-sample.json')
        ),
        'https://api.openai.com/v1/responses' => Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-sample.json'), true
        )),
    ]);
});

it('can parse url from wa-connect notification', function () {
    $payload = json_decode(file_get_contents(__DIR__ . '/../Fixtures/sample-wa-connect-inbound.json'), true);
    $user = User::factory()->create();
    Resume::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->withToken($user->api_token)
        ->post(route('api.optimizations.detect'), $payload);

    $response->assertStatus(200);
    expect($response->json('optimization.href'))->not->toBeNull()
        ->and($response->json('supported'))->toBeTrue();

});
