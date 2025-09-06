<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    Http::preventStrayRequests();
});

it('does not work if a resume is not uploaded yet', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Arrange: Creating required request data
    $data = [
        'name' => 'Backend Developer',
        'url' => 'https://example.com/job/backend-developer',
        'company' => 'Example Corp',
        'description' => 'We are looking for a skilled Backend Developer.',
        'location' => 'Remote',
    ];

    // Act: Send a POST request to the controller's store method
    $response = $this->postJson(route('optimizations.unattended-store'), $data);

    $response->assertStatus(422);

    expect($response->json('errors.resume'))->toContain('A resume must be uploaded before creating an unattended optimization. Try creating an optimization manually before trying to create an unattended one.');
});

it('stores an unattended optimization successfully', function () {
    // Arrange: Create a user and log in
    $user = User::factory()->create();
    $this->actingAs($user);
    \App\Models\Resume::factory()->create([
        'user_id' => $user->id,
        'detected_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus, aspernatur.'
    ]);

    // Mocking the necessary external HTTP request
    Http::fake([
        'https://api.openai.com/v1/responses' => Http::response(json_decode(
            file_get_contents(__DIR__.'/../Fixtures/response-sample.json'), true
        )),
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
    $response = $this->postJson(route('optimizations.unattended-store'), $data);

    $response->assertStatus(200);

    $this->assertDatabaseHas('optimizations', [
        'role_name' => $data['name'],
        'role_url' => $data['url'],
        'role_company' => $data['company'],
        'role_description' => $data['description'],
        'role_location' => $data['location'],
        'status' => 'complete',
    ]);
});
