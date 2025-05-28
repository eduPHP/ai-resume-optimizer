<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function casts(): array
    {
        return [
            'current_step' => 'int',
            'make_grammatical_corrections' => 'boolean',
            'change_professional_summary' => 'boolean',
            'change_target_role' => 'boolean',
            'mention_relocation_availability' => 'boolean',
            'status' => 'string',
        ];
    }
}
