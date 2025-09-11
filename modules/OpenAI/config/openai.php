<?php

return [
    'openai_api_key' => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    'endpoint' => env('OPENAI_ENDPOINT', 'https://api.openai.com/v1/responses'),
];
