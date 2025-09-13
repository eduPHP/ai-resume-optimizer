<?php

namespace Modules\WAConnect;

use Illuminate\Support\ServiceProvider;

class WAConnectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'wa-connect');
    }
}
