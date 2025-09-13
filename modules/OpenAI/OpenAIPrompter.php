<?php

namespace Modules\OpenAI;

use App\DTO\Contracts\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\DTO\Contracts\AIResponseDTO;
use App\Exceptions\RequestException;
use Illuminate\Support\Facades\Http;

class OpenAIPrompter implements AIAgentPrompter
{
    public function handle(AIInputOptions $options): AIResponseDTO
    {
        // OpenAI streaming request
        $response = Http::withToken(config('openai.openai_api_key'))
            ->withOptions(['timeout' => 300])
            ->post(config('openai.endpoint'), [
                'model' => config('openai.model'),
                'store' => true,
                'metadata' => $options->metadata(),
                'input' => [
                    ...array_map(fn($instruction) => ['role' => 'system', 'content' => $instruction], $options->system()),
                    ...array_map(fn($instruction) => ['role' => 'user', 'content' => $instruction], $options->user()),
                ],
                ...$this->getSchemaSettings($options)
            ]);

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response->json('error.message'), $response->getStatusCode());
        }

        return new OpenAiResponse(
            ...$this->cleanup($response->json('output.0.content.0.text')),
            usage: $response->json('usage.total_tokens'),
            id: $response->json('id'),
        );
    }

    private function cleanup(string $response): array
    {
        return json_decode($response, true);
    }

    private function getSchemaSettings(AIInputOptions $options): array
    {
        $textFormat = ['gpt-4.1', 'gpt-4o-mini'];

        if (in_array(config('openai.model'), $textFormat)) {
            return [
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        ...$options->schema(),
                    ],
                ],
            ];
        }

        return [
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $options->schema(),
            ]
        ];
    }
}
