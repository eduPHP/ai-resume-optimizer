<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $attributes = [
        'ai_settings' => [
            'instructions' => '',
            'compatibilityScoreLevels' => [
                'top' => 95,
                'high' => 90,
                'medium' => 80,
                'low' => 70,
            ],
        ]
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ai_settings' => 'json',
        ];
    }

    public function resumes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resume::class);
    }

    public function optimizations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Optimization::class);
    }
}
