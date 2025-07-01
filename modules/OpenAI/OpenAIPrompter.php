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
        $response = Http::withToken(config('openai.openai_api_key'))
            ->timeout(300)
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4.1',
                'store' => true,
                'input' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an experienced HR manager, your role is to answer questions about a resume.
                                        No compliments, softening, or beating around the bush.
                                        If the resume is bad, just say it.
                                        If there is no change of getting the job, say so.
                                        '.($options->mentionRelocationAvailability ? 'Include in the missing_requirements output if the company has no history of sponsoring a visa.' : '').'
                                        When a compatibility score is below 80, present immediate or long term plans to improve.',
                    ],
                    [
                        'role' => 'system',
                        'content' => "The following resume is your source of truth and should be the base of your answer:\n\n$resume",
                    ],
                    [
                        'role' => 'system',
                        'content' => '
                          resume contact information should be kept in one like separated by a pipe symbol (|) and have the following template: <p class="contact-info">contact info</p>
                          sections should have a consistent format with the following template: <h2 class="section-title">Section Title</h2><div>Section Content</div>
                          title (name) should have a consistent format with the following template: <h1 class="name">Full Name</h1><h3 class"job-title">Job Title</h3>
                          experience should have a consistent format with the following template: <h3 class="exp-title">Title</h3><p class"exp-period">place and period</p><ul>Description</ul>',
                    ],
                    [
                        'role' => 'system',
                        'content' => 'You are an API that optimizes resumes. Always reply ONLY with a single, valid JSON object in this format: {
                            "resume": "html formatted resume, body content only, with basic styling",
                            "compatibility_score": "a REALISTIC compatibility percentage score based on the requirements from 0 to 100, i.e. 90",
                            "professional_summary": "same professional summary returned on the resume",
                            "cover_letter": "'.($options->generateCoverLetter ? 'a cover letter, with casual phrasing,
                                            3 paragraphs as an array, no introduction (i.e. dear hiring manager or so), no signature,
                                            i.e. [
                                                \"paragraph 1, why am I a good fit\",
                                                \"paragraph 2, emphasise accomplishments\",
                                                \"paragraph 3 emphasise strengths and the need to get in touch\"]' : 'an empty array: []').'",
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
                            "reasoning": "Would you recommend this candidate for the job? How can he improve his base resume to match the job description?",
                            "top_choice": "if compatibility_score is greater than 95,
                                           In first person, as if you are talking to a potential employer,
                                           Briefly describe (up to 400 chars) why this job is your top choice and why you’re a good fit.
                                           Otherwise the value is an empty string",
                        }
                        Do NOT include any other text.',
                    ],
                    ...$options,
                    ...(auth()->user()->ai_instructions ? [
                        [
                            'role' => 'system',
                            'content' => auth()->user()->ai_instructions,
                        ]
                    ] : []),
                    [
                        'role' => 'user',
                        'content' => "Please improve the resume, following {$options->roleLocation}'s pattern and best practices
                                     for a higher employee selection rate, reorganize the sections according with the country's
                                     requirements and keep the title.
                                     Role description is: {$options->roleDescription}",
                    ],
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
