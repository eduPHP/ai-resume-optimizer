<?php

namespace Modules\LocalAgent;

use App\DTO\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\DTO\Contracts\AIResponseDTO;
use App\Exceptions\RequestException;
use Illuminate\Support\Facades\Http;
use Modules\OpenAI\OpenAiResponse;

class LocalAgentPrompter implements AiAgentPrompter
{

    public function __construct()
    {
    }

    public function handle(AIInputOptions $options): AIResponseDTO
    {
        // dd([
        //     'model' => config('local-agent.model'),
        //     'store' => true,
        //     'input' => [
        //         ...array_map(fn($instruction) => ['role' => 'system', 'content' => $instruction], $options->system()),
        //         ...array_map(fn($instruction) => ['role' => 'user', 'content' => $instruction], $options->user()),
        //     ],
        //     'text' => [
        //         'format' => [
        //             'type' => 'json_schema',
        //             ...$options->schema()
        //         ],
        //     ]
        // ]);
        $response = Http::withToken(config('local-agent.api_key'))
            ->withOptions(['timeout' => 300])
            ->post(config('local-agent.endpoint'), [
                'model' => config('local-agent.model'),
                'store' => true,
                'input' => [
                    ...array_map(fn($instruction) => ['role' => 'system', 'content' => $instruction], $options->system()),
                    ...array_map(fn($instruction) => ['role' => 'user', 'content' => $instruction], $options->user()),
                ],
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        ...$options->schema()
                    ],
                ]
            ]);

        // dd($response->getBody()->getContents());

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response->json('error.message'), $response->getStatusCode());
        }

        return new OpenAiResponse(
            ...$response->json()
        );
    }
}
