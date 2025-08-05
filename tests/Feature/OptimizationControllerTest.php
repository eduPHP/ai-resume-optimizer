<?php

use App\Models\Optimization;
use App\Models\Resume;
use App\Models\User;

test('it creates a new optimization when the role information is sent', function () {
    $response = $this->withHeader('X-CurrentStep', 0)
        ->actingAs($user = User::factory()->create())
        ->post('/optimizations/create', [
            'name' => 'Backend Engineer',
            'company' => 'Laravel',
            'description' => 'Lore Ipsum!',
            'url' => 'https://www.linkedin.com/jobs/view/4253350439/',
            'location' => 'Nairobi'
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseCount('optimizations', 1);
    $this->assertDatabaseHas('optimizations', [
        'role_name' => 'Backend Engineer',
        'role_company' => 'Laravel',
        'role_description' => 'Lore Ipsum!',
        'role_location' => 'Nairobi',
        'current_step' => '1',
        'user_id' => $user->id,
    ]);

    expect($response->json('step'))->toBe(0)
        ->and($response->json('optimization.role_name'))->toBe('Backend Engineer')
        ->and($response->json('optimization.role_location'))->toBe('Nairobi');
});

test('it sets an existing resume in the optimization', function () {
    $optimization = Optimization::factory()->create([
        'resume_id' => null,
    ]);
    $resume = Resume::factory()->create();
    $response = $this->withHeader('X-CurrentStep', 1)
        ->actingAs($optimization->user)
        ->put(route('optimizations.update', $optimization), [
            'id' => $resume->id,
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseCount('optimizations', 1);
    $this->assertDatabaseHas('optimizations', [
        'id' => $optimization->id,
        'current_step' => '2',
        'user_id' => $optimization->user_id,
        'resume_id' => $resume->id,
    ]);

    expect($optimization->fresh()->resume->is($resume))->toBeTrue();
});

test('an optimized resume can be downloaded', function () {
    $optimization = Optimization::factory()->create([
        'optimized_result' => '<h1>John doe</h1><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto cum doloribus necessitatibus praesentium quae quis sunt, voluptates. Cumque eos esse, ex facere, in maiores nobis obcaecati omnis placeat, recusandae veritatis!</p>',
    ]);

    $response = $this->withToken($optimization->user->api_token)->post(route('optimizations.download', $optimization));

    $response->assertSuccessful();

    expect($response->headers->get('content-type'))->toBe('application/pdf')
        ->and($response->headers->get('content-disposition'))->toBe('attachment; filename="' . $optimization->optimizedResumeFileName() . '"');
});

test('a cover letter can be downloaded', function () {
    $optimization = Optimization::factory()->create([
        'ai_response' => [
            'cover_letter' => [
                '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aliquam animi architecto at dicta distinctio, eius est eveniet labore magni nobis quisquam sapiente! Accusantium consequatur dicta fuga laudantium non rem.</p>',
            ],
        ],
    ]);

    $response = $this->withToken($optimization->user->api_token)->post(route('optimizations.download-cover', $optimization));

    $response->assertSuccessful();

    expect($response->headers->get('content-type'))->toBe('application/pdf')
        ->and($response->headers->get('content-disposition'))->toBe('attachment; filename="' . $optimization->coverLetterFileName() . '"');
});

test('it can cancel an edit and restore the optimization', function () {
    $optimization = Optimization::factory()->create([
        'status' => 'draft',
        'current_step' => 1,
        'ai_response' => ['compatibility_score' => 99],
    ]);

    $response = $this->actingAs($optimization->user)
        ->put(route('optimizations.cancel', $optimization));

    $response->assertSuccessful();

    $optimization->refresh();

    expect($optimization->status)->toBe('complete')
        ->and($optimization->current_step)->toBe(3);
});

test('it paginates the optimizations index', function () {
    $user = User::factory()->create();
    \App\Models\Optimization::factory()->count(15)->for($user)->create();

    $response = $this->withToken($user->api_token)->getJson(route('optimizations.index', ['page' => 1]));

    $response->assertSuccessful();
    $response->assertJsonCount(10, 'data');
    expect($response->json('next_page_url'))->not->toBeNull();

    $next = $response->json('next_page_url');
    $response = $this->withToken($user->api_token)->getJson($next);
    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
});

test('it filters optimizations by compatibility level', function (string $level, int $expectedScore) {
    $user = User::factory()->create();

    $scores = [
        'top' => 97,
        'high' => 92,
        'medium' => 85,
        'low' => 75,
    ];

    foreach ($scores as $name => $score) {
        Optimization::factory()->for($user)->create([
            'status' => 'complete',
            'ai_response' => ['compatibility_score' => $score],
        ]);
    }

    $response = $this->withToken($user->api_token)
        ->getJson(route('optimizations.index', ['compatibility' => $level]));

    $response->assertSuccessful();
    $response->assertJsonCount(1, 'data');
    expect($response->json('data.0.score'))->toBe($expectedScore);
})->with([
    ['top', 97],
    ['high', 92],
    ['medium', 85],
    ['low', 75],
]);

test('optimizations without ai response are ignored when filtering by compatibility', function () {
    $user = User::factory()->create();

    Optimization::factory()->for($user)->create([
        'status' => 'complete',
        'ai_response' => null,
    ]);

    Optimization::factory()->for($user)->create([
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 92],
    ]);

    $response = $this->withToken($user->api_token)
        ->getJson(route('optimizations.index', ['compatibility' => 'high']));

    $response->assertSuccessful();
    $response->assertJsonCount(1, 'data');
    expect($response->json('data.0.score'))->toBe(92);
});
