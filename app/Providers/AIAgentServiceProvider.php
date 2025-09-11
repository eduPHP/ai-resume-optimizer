<?php

namespace App\Providers;

use App\DTO\Contracts\AIAgentPrompter;
use Illuminate\Support\ServiceProvider;

class AIAgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // this sets which agent will handle the requests
        $this->app->bind(AIAgentPrompter::class, function ($app) {
            $key = config('ai.default', 'agents.local');
            return $app->make($key);
        });

    }
}
