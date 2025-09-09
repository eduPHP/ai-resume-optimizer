<?php

namespace App\Models;

use App\Enums\OptimizationStatuses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property bool $make_grammatical_corrections
 * @property bool $change_professional_summary
 * @property bool $generate_cover_letter
 * @property bool $change_target_role
 * @property bool $mention_relocation_availability
 * @property string $id
 * @property string $role_name
 * @property string $role_location
 * @property string $role_company
 * @property string $role_description
 * @property array $ai_response;
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \App\Models\User $user
 * @property \App\Models\Resume $resume
 * @method Builder searchByRoleCompany(string $search)
 * @method Builder filterByScoreLevel(string $search)
 */
class Optimization extends Model
{
    /** @use HasFactory<\Database\Factories\OptimizationFactory> */
    use HasFactory;
    use HasUlids;

    public function scopeSearchByRoleCompany(Builder $query, $search): Builder
    {
        return $query->when($search, fn($query) =>
            $query->where('role_company', 'like', "%$search%")
        );
    }

    public function scopeFilterByScoreLevel(Builder $query, $score): Builder
    {
        if ($score === 'all') {
            return $query;
        }

        return $query->when($score, function(Builder $query) use ($score) {
            // Get the current user's compatibility score levels
            $user = auth()->user();
            if (!$user || !$user->ai_settings) {
                return $query;
            }

            /* @var Collection $scoreLevels */
            $scoreLevels = $user->ai_settings['compatibilityScoreLevels'] ?? [
                'top' => 95,
                'high' => 90,
                'medium' => 80,
                'low' => 70,
            ];

            if (! $scoreLevels->has($score)) {
                return $query;
            }

            $query->where('status', 'complete');

            return match ($score) {
                'top' => $query->where('ai_response->compatibility_score', '>=', $scoreLevels['top']),
                'high' => $query->where('ai_response->compatibility_score', '>=', $scoreLevels['high']),
                'medium' => $query->where('ai_response->compatibility_score', '<', $scoreLevels['high'])
                    ->where('ai_response->compatibility_score', '>=', $scoreLevels['medium']),
                'low' => $query->where('ai_response->compatibility_score', '<', $scoreLevels['medium'])
                    ->where('ai_response->compatibility_score', '>=', $scoreLevels['low']),
                default => $query,
            };
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class)->withTrashed();
    }

    public function optimizedResumeFileName(): string
    {
        $bits = collect([
            "Resume",
            $this->user->name,
            $this->role_name,
            "at",
            $this->role_company,
            // now()->format('YmdHi'),
        ]);

        return str($bits->join(" "))
            ->replace(['/', '\\'], '')
            ->append(".pdf")
            ->__toString();
    }

    public function coverLetterFileName(): string
    {
        $bits = collect([
            "Cover Letter",
            $this->user->name,
            "for",
            $this->role_company,
            // now()->format('YmdHi')
        ]);

        return str($bits->join(" "))
            ->replace(['/', '\\'], '')
            ->append(".pdf")
            ->__toString();
    }

    public function casts(): array
    {
        return [
            'current_step' => 'int',
            'make_grammatical_corrections' => 'boolean',
            'change_professional_summary' => 'boolean',
            'generate_cover_letter' => 'boolean',
            'change_target_role' => 'boolean',
            'mention_relocation_availability' => 'boolean',
            'status' => OptimizationStatuses::class,
            'ai_response' => 'json',
            'applied' => 'boolean',
        ];
    }
}
