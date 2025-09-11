<?php

namespace Modules\OpenAI;


use Illuminate\Support\ServiceProvider;

class OpenAIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/openai.php', 'openai');
        $this->app->bind('agents.openai', fn() => new OpenAIPrompter);
    }
}
