<?php

use Illuminate\Broadcasting\BroadcastServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    Modules\OpenAI\OpenAIServiceProvider::class,
    BroadcastServiceProvider::class,
];
