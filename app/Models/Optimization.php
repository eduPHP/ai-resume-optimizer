<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 */
class Optimization extends Model
{
    /** @use HasFactory<\Database\Factories\OptimizationFactory> */
    use HasFactory;
    use HasUlids;


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
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
            'status' => 'string',
            'ai_response' => 'json',
        ];
    }
}
