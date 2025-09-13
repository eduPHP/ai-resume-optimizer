<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Modules\OpenAI\OpenAIServiceProvider::class,
    Modules\LocalAgent\LocalAgentServiceProvider::class,
    App\Providers\AIAgentServiceProvider::class,
    \Modules\WAConnect\WAConnectServiceProvider::class,
];
