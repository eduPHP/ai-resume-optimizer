<?php

namespace Modules\OpenAI;

use App\DTO\Contracts\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\DTO\Contracts\AIResponseDTO;
use Illuminate\Support\Facades\Http;

class OpenAIPrompter implements AIAgentPrompter
{
    public function handle(string $resume, AIInputOptions $options): AIResponseDTO
    {
        $response = Http::withToken(config('openai.openai_api_key'))
            ->timeout(300)
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4.1',
                'store' => true,
                'input' => [
                    ...array_map(fn($option) => ['role' => 'system', 'content' => $option], $options->system()),
                    ...array_map(fn($option) => ['role' => 'user', 'content' => $option], $options->user()),
                ]
            ]);

        return new OpenAiResponse(
            ...$this->cleanup($response->json('output.0.content.0.text'))
        );
    }

    private function cleanup(string $response): array
    {
        return json_decode($response, true);
    }
}
