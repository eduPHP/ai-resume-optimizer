<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optimization extends Model
{
    /** @use HasFactory<\Database\Factories\OptimizationFactory> */
    use HasFactory;
    use HasUlids;

}
