<?php

namespace Modules\OpenAI;


use App\DTO\Contracts\AIAgentPrompter;
use Illuminate\Support\ServiceProvider;

class OpenAIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/openai.php', 'openai');
        $this->app->bind(AiAgentPrompter::class, fn() => new OpenAIPrompter);
    }
}
