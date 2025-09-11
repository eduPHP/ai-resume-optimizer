<?php

namespace App\DTO;

use App\DTO\Contracts\AIInputOptions as AIInputOptionsInterface;
use Illuminate\Support\Collection;

class AIInputOptions implements AIInputOptionsInterface
{
    public Collection $settings;

    public function __construct(
        public string  $resume = "",
        public bool    $makeGrammaticalCorrections = false,
        public bool    $changeProfessionalSummary = false,
        public bool    $generateCoverLetter = false,
        public bool    $changeTargetRole = false,
        public bool    $mentionRelocationAvailability = false,
        public string  $roleName = '',
        public string  $roleDescription = '',
        public ?string $roleLocation = '',
        public string  $roleCompany = '',
        Collection     $options = null,
    )
    {
        $this->settings = $options ?? collect();
    }

    public function system(): array
    {
        $config = [
            // Role + tone
            'You are an experienced HR manager. Answer ONLY in JSON. Be direct: no compliments, no softening.
            If the resume is bad, say so. If there is no chance of getting the job, state it directly.',

            // Resume context
            "The following resume is your single source of truth:\n\n{$this->resume}",

            // Resume formatting rules
            'Resume formatting rules:
             - Contact info → <p class="contact-info">text | text | text</p>
             - Section titles → <h2 class="section-title">Section Title</h2><div>Content</div>
             - Name → <h1 class="name">Full Name</h1><h3 class="job-title">Job Title</h3>
             - Experience → <h3 class="exp-title">Title</h3><p class="exp-period">place and period</p><ul>Description</ul>',

            // Resume improvement instructions
            $this->changeProfessionalSummary
                ? "Replace the Professional Summary with a role-specific one, emphasizing candidate strengths (keep the title)."
                : false,

            $this->changeTargetRole
                ? "Replace the Target Role (below the name) with: {$this->roleName}."
                : false,

            $this->mentionRelocationAvailability
                ? "At the bottom (class=\"footer\"), add:
               'Available for remote work or relocation to {$this->roleLocation} through visa sponsorship'.
               If only a city is given, replace it with its country."
                : false,

            $this->mentionRelocationAvailability
                ? "If the company does not sponsor visas, include this fact in the 'findings' output list as a missing_requirement."
                : false,

            $this->makeGrammaticalCorrections
                ? "Correct grammar across the resume."
                : false,

            // Scoring logic
            "When compatibility_score < {$this->settings['compatibilityScoreLevels']['medium']},
         provide short-term and long-term improvement plans.",

            // API enforcement
            'Always reply ONLY with a valid JSON object that matches the schema.
         Do not add explanations, prefaces, or comments outside JSON.',
        ];

        return array_filter($config);
    }

    public function user(): array
    {
        return [
            "Please optimize the resume for the {$this->roleLocation} market.
             - Reorganize sections to match {$this->roleLocation}'s best practices.
             - Improve content for higher selection rate.
             - Keep the title intact.
             - Role description is: {$this->roleDescription}
             - Translate the output resume to its language!"
        ];
    }

    public function schema(): array
    {
        return [
            "name" => "candidate_evaluation",
            "schema" => [
                "type" => "object",
                "additionalProperties" => false,
                "properties" => [
                    "resume" => [
                        "type" => "string",
                        "description" => "HTML resume, body content only, with basic styling"
                    ],
                    "compatibility_score" => [
                        "type" => "integer",
                        "description" => "REALISTIC 0-100 score"
                    ],
                    "top_choice" => [
                        "type" => "string",
                        "maxLength" => 400,
                        "description" => "If compatibility_score >= {$this->settings['compatibilityScoreLevels']['top']}, first-person pitch explaining why this is a top choice. Otherwise empty string."
                    ],
                    "professional_summary" => [
                        "type" => "string",
                        "description" => "Same professional summary included in the resume"
                    ],
                    "cover_letter" => [
                        "type" => "array",
                        "description" => $this->generateCoverLetter
                            ? "Return 3 paragraphs with casual phrasing, no intro or signature."
                            : "Empty array.",
                        "items" => ["type" => "string"]
                    ],
                    "findings" => [
                        "type" => "array",
                        "description" => "Consolidated evaluation items: strong alignments, moderate gaps, missing requirements, or issues.",
                        "items" => [
                            "type" => "object",
                            "additionalProperties" => false,
                            "properties" => [
                                "group" => [
                                    "type" => "string",
                                    "enum" => ["strong_alignment", "moderate_gap", "missing_requirement", "issue"],
                                    "description" => "Classification of the finding."
                                ],
                                "title" => ["type" => "string"],
                                "description" => ["type" => "string"]
                            ],
                            "required" => ["group", "title", "description"]
                        ]
                    ],
                    "reasoning" => [
                        "type" => "string",
                        "description" => "Would you recommend this candidate? How can they improve their resume to match the job description?"
                    ],
                ],
                "required" => [
                    "resume",
                    "compatibility_score",
                    "professional_summary",
                    "cover_letter",
                    "findings",
                    "reasoning",
                    "top_choice"
                ]
            ]
        ];
    }
}
