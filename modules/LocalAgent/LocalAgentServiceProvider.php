<?php

namespace Modules\LocalAgent;

use Illuminate\Support\ServiceProvider;

class LocalAgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'local-agent');
        $this->app->bind('agents.local', fn() => new LocalAgentPrompter);
    }
}
