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
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4.1',
                'store' => true,
                'input' => [
                    ...array_map(fn($o) => ['role' => 'system', 'content' => $o], $options->system()),
                    ...array_map(fn($o) => ['role' => 'user', 'content' => $o], $options->user()),
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => $options->schema()
                ]
            ]);

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response->json('error.message'), $response->getStatusCode());
        }

        return new OpenAiResponse(
            ...$this->cleanup($response->json('output.0.content.0.text'))
        );
    }

    private function cleanup(string $response): array
    {
        return json_decode($response, true);
    }
}
