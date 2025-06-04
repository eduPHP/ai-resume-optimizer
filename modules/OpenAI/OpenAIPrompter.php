<?php

namespace Modules\OpenAI;

use App\DTO\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\DTO\Contracts\AIResponseDTO;
use Illuminate\Support\Facades\Http;

class OpenAIPrompter implements AIAgentPrompter
{
    public function handle(string $resume, AIInputOptions $options): AIResponseDTO
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
                    'content' => 'The following resume is your source of truth and should be the base of your answer: '.$resume,
                ],
                [
                    'role' => 'system',
                    'content' => '
                        contact information should be kept in one like separated by a pipe symbol: |
                        sections should have a consistent format with the following template: <h2 class="section-title">Section Title</h2><div>Section Content</div>
                        title (name) should have a consistent format with the following template: <h1 class="name">Full Name</h1><h3 class"job-title">Job Title</h3>
                        experience should have a consistent format with the following template: <h3 class="exp-title">Title</h3><p class"exp-period">place and period</p><ul>Description</ul>',
                ],
                [
                    'role' => 'system',
                    'content' => 'your answer will always consistently and strictly be in the following plain json format: {
                      "resume": "html formatted resume, body content only, with basic styling",
                      "compatibility_score": "a compatibility percentage score from 0 to 100, i.e. 90",
                      "professional_summary": "same professional summary returned on the resume",
                      "cover_letter": "a cover letter, with casual phrasing, 3 paragraphs in the as an array, i.e. [\"paragraph 1\", \"paragraph 2\", \"paragraph 3\"]",
                      "strong_alignments": [
                        {
                            "title": "strong alignment title",
                            "description": "strong alignment description"
                        {
                            "title": "another strong alignment title",
                            "description": "another strong alignment description"
                        },
                        ...remaining strong alignments
                      ],
                      "moderate_gaps": [
                        {
                            "title": "moderate gap title",
                            "description": "moderate gap description"
                        },
                        ...remaining moderate gaps
                      ],
                      "missing_requirements": [
                        {
                            "title": "missing requirement title",
                            "description": "missing requirement description"
                        },
                        ...remaining missing requirements
                      ],
                      "reasoning": "any addition or commentary you want to express"
                    }',
                ],
                ...$options,
                [
                    'role' => 'user',
                    'content' => "Please improve the resume, following {$options->roleLocation}'s pattern and best practices for a higher employee selection rate, reorganize the sections according with the country's requirements and keep the title.",
                ],
            ]
        ]);

        return new OpenAiResponse(
            ...$this->cleanup($response->json('choices.0.message.content'))
        );
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
