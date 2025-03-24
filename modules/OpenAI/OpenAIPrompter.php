<?php

namespace Modules\OpenAI;

use App\Contracts\AIAgentPrompter;
use App\Contracts\AIResponseDTO;
use Illuminate\Support\Facades\Http;

class OpenAIPrompter implements AIAgentPrompter
{
    public function handle(string $prompt): AIResponseDTO
    {
        // $sample = json_decode(file_get_contents(base_path('tests/Fixtures/response-sample.json')), true);
        // $answer = $this->cleanup($sample['choices'][0]['message']['content']);
        // return new OpenAiResponse($answer['resume'], $answer['reasoning']);

        $response = Http::withToken(config('openai.openai_api_key'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'you are an experienced HR manager',
                ],
                [
                    'role' => 'system',
                    'content' => 'your answer will always consistently be in the following plain json format: {"resume": "html formatted resume, body content only, with basic tailwindcss like styling", "reasoning": "any addittion or commentary you want to express"}',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ]
            ]
        ]);

        $answer = $this->cleanup($response->json('choices.0.message.content'));

        return new OpenAiResponse($answer['resume'], $answer['reasoning']);
    }

    private function cleanup(string $response): array
    {
        $answer = str($response)
            ->replaceMatches('/^```json\n\{\n/', '{')
            ->replaceMatches('/\n```$/', '')
        ;

        return json_decode($answer->toString(), true);
    }
}
