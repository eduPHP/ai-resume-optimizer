<?php

use App\Models\Optimization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('returns all optimizations when score level is set to all', function () {
    // Create a user with some optimizations
    actingAs($user = User::factory()->create());
    $optimizations = Optimization::factory()->count(5)->create([
        'user_id' => $user->id,
        'status' => 'complete',
    ]);

    // Apply the filter
    $filtered = $user->optimizations()->filterByScoreLevel('all')->get();

    // Assert that all optimizations are returned
    expect($filtered)->toHaveCount(5);
});

it('filters optimizations by top score level', function () {
    // Create a user with AI settings that define score levels
    actingAs($user = User::factory()->create([
        'ai_settings' => [
            'compatibilityScoreLevels' => [
                'top' => 90,
                'high' => 80,
                'medium' => 70,
                'low' => 60,
            ]
        ]
    ]));

    // Create optimizations with different scores
    $highScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    $mediumScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 70],
    ]);

    $lowScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 65],
    ]);

    // Apply the filter for the top score
    $filtered = $user->optimizations()->filterByScoreLevel('top')->get();

    // Assert that only the high score optimization is returned
    expect($filtered)->toHaveCount(1)
        ->and($filtered->first()->id)->toBe($highScore->id);
});

it('filters optimizations by high score level', function () {
    // Create a user with AI settings
    actingAs($user = User::factory()->create([
        'ai_settings' => [
            'compatibilityScoreLevels' => [
                'top' => 90,
                'high' => 80,
                'medium' => 70,
                'low' => 60,
            ]
        ]
    ]));

    // Create optimizations with different scores
    $veryHighScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    $highScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 85],
    ]);

    $lowScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 65],
    ]);

    // Apply the filter for high score
    $filtered = $user->optimizations()->filterByScoreLevel('high')->get();

    // Assert that only optimizations with score >= 80 are returned
    expect($filtered)->toHaveCount(2)
        ->and($filtered->pluck('id')->toArray())->toContain($veryHighScore->id, $highScore->id);
});

it('filters optimizations by medium score level', function () {
    // Create a user with AI settings
    actingAs($user = User::factory()->create([
        'ai_settings' => [
            'compatibilityScoreLevels' => [
                'top' => 90,
                'high' => 80,
                'medium' => 70,
                'low' => 60,
            ]
        ]
    ]));

    // Create optimizations with different scores
    $highScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    $mediumScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 75],
    ]);

    $lowScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 65],
    ]);

    // Apply the filter for medium score
    $filtered = $user->optimizations()->filterByScoreLevel('medium')->get();

    // Assert that only the medium score optimization is returned (70 <= score < 80)
    expect($filtered)->toHaveCount(1)
        ->and($filtered->first()->id)->toBe($mediumScore->id);
});

it('filters optimizations by low score level', function () {
    // Create a user with AI settings
    actingAs($user = User::factory()->create([
        'ai_settings' => [
            'compatibilityScoreLevels' => [
                'top' => 90,
                'high' => 80,
                'medium' => 70,
                'low' => 60,
            ]
        ]
    ]));

    // Create optimizations with different scores
    $highScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    $mediumScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 75],
    ]);

    $lowScore = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 65],
    ]);

    // Apply the filter for low score
    $filtered = $user->optimizations()->filterByScoreLevel('low')->get();

    // Assert that only the low score optimization is returned (60 <= score < 70)
    expect($filtered)->toHaveCount(1)
        ->and($filtered->first()->id)->toBe($lowScore->id);
});

it('only includes optimizations with complete status', function () {
    // Create a user with AI settings
    actingAs($user = User::factory()->create([
        'ai_settings' => [
            'compatibilityScoreLevels' => [
                'top' => 90,
                'high' => 80,
                'medium' => 70,
                'low' => 60,
            ]
        ]
    ]));

    // Create one complete and one draft optimization with high scores
    $complete = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    $draft = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'draft',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    // Apply the filter for high score
    $filtered = $user->optimizations()->filterByScoreLevel('high')->get();

    // Assert that only the complete optimization is returned
    expect($filtered)->toHaveCount(1)
        ->and($filtered->first()->id)->toBe($complete->id);
});

it('handles missing score levels in user settings', function () {
    // Create a user with empty AI settings
    actingAs($user = User::factory()->create([
        'ai_settings' => []
    ]));

    // Create an optimization
    $optimization = Optimization::factory()->create([
        'user_id' => $user->id,
        'status' => 'complete',
        'ai_response' => ['compatibility_score' => 95],
    ]);

    // Apply the filter
    $filtered = $user->optimizations()->filterByScoreLevel('top')->get();

    // Assert that no optimizations are returned (since there are no score levels)
    expect($filtered)->toHaveCount(1);
});
