<?php

return [
    'api_key' => env('LOCAL_AGENT_API_KEY'),
    'model' => env('LOCAL_AGENT_MODEL', 'llama-2-7b'),
    'endpoint' => env('LOCAL_AGENT_ENDPOINT', 'http://localhost:8060/optimize/openai'),
];
