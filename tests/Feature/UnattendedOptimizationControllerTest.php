<?php

use App\DTO\Contracts\AIAgentPrompter;
use App\Http\Controllers\PromptsAIAgent;
use App\Jobs\OptimizeResume;
use App\Models\Optimization;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class, PromptsAIAgent::class);

beforeEach(function () {
    $this->instance(AiAgentPrompter::class, app('agents.openai'));
    Http::preventStrayRequests();

    // Mocking the necessary external HTTP request
    Http::fake([
        'https://api.openai.com/v1/responses' => Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-sample.json'), true
        )),
    ]);
});

it('does not work if a resume is not uploaded yet', function () {
    $user = User::factory()->create();

    // Arrange: Creating required request data
    $data = [
        'name' => 'Backend Developer',
        'url' => 'https://example.com/job/backend-developer',
        'company' => 'Example Corp',
        'description' => 'We are looking for a skilled Backend Developer.',
        'location' => 'Remote',
    ];

    // Act: Send a POST request to the controller's store method
    $response = $this->withToken($user->api_token)->postJson(route('optimizations.unattended-store'), $data);

    $response->assertStatus(422);

    expect($response->json('errors.message'))->toContain('A resume must be uploaded before creating an unattended optimization. Try creating an optimization manually before trying to create an unattended one.');
});

it('stores an unattended optimization successfully', function () {
    // Arrange: Create a user and log in
    $user = User::factory()->create();
    Resume::factory()->create([
        'user_id' => $user->id,
        'detected_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus, aspernatur.'
    ]);

    // Arrange: Creating required request data
    $data = [
        'name' => 'Backend Developer',
        'url' => 'https://example.com/job/backend-developer',
        'company' => 'Example Corp',
        'description' => 'We are looking for a skilled Backend Developer.',
        'location' => 'Remote',
    ];

    // Act: Send a POST request to the controller's store method
    $response = $this->withToken($user->api_token)
        ->postJson(route('optimizations.unattended-store'), $data);

    $response->assertStatus(200);

    $this->assertDatabaseHas('optimizations', [
        'role_name' => $data['name'],
        'role_url' => $data['url'],
        'role_company' => $data['company'],
        'role_description' => $data['description'],
        'role_location' => $data['location'],
        'status' => \App\Enums\OptimizationStatuses::Complete,
    ]);
});

it('returns tokens usage [regression]', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $resume = Resume::factory()->create([
        'user_id' => $user->id,
        'detected_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus, aspernatur.'
    ]);
    $optimization = Optimization::factory()->create([
        'resume_id' => $resume->id,
        'user_id' => $user->id,
    ]);
    $response = $this->agentQuery($optimization);

    expect(array_key_exists('usage', $response))->toBeTrue('`usage` key is missing from the response');
});

it('dispatches a job for AI process', function () {
    Queue::fake();
    $optimization = Optimization::factory()->create();

    OptimizeResume::dispatch($optimization);

    Queue::assertPushed(OptimizeResume::class, function ($job) use ($optimization) {
        return $job->optimization->id === $optimization->id;
    });
});

it('dispatches an event when optimization is ready', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $resume = Resume::factory()->create([
        'user_id' => $user->id,
        'detected_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus, aspernatur.'
    ]);
    $optimization = Optimization::factory()->create([
        'resume_id' => $resume->id,
    ]);

    Event::fake();


    OptimizeResume::dispatch($optimization);

    Event::assertDispatched(\App\Events\OptimizationComplete::class, function ($job) use ($optimization) {
        return $job->optimization->id === $optimization->id;
    });
});
