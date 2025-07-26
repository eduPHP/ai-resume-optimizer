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
        public string  $roleCompany = ''
    )
    {
        $this->settings = auth()->user()->ai_settings;
    }

    public function system(): array
    {
        $config = [
            'You are an experienced HR manager, your role is to answer questions about a resume.
            No compliments, softening, or beating around the bush.
            If the resume is bad, just say it.
            If there is no change of getting the job, say so.
            When a compatibility score is below '.$this->settings['compatibilityScoreLevels']['medium'].', present immediate or long term plans to improve.',
            "The following resume is your source of truth and should be the base of your answer:\n\n$this->resume",
            'resume contact information should be kept in one like separated by a pipe symbol (|) and have the following template: <p class="contact-info">contact info</p>
              sections should have a consistent format with the following template: <h2 class="section-title">Section Title</h2><div>Section Content</div>
              title (name) should have a consistent format with the following template: <h1 class="name">Full Name</h1><h3 class"job-title">Job Title</h3>
              experience should have a consistent format with the following template: <h3 class="exp-title">Title</h3><p class"exp-period">place and period</p><ul>Description</ul>',
            'You are an API that optimizes resumes. Always reply ONLY with a single, valid JSON object in this format: {
                "resume": "html formatted resume, body content only, with basic styling",
                "compatibility_score": "a REALISTIC compatibility percentage score based on the requirements from 0 to 100, i.e. 90",
                "professional_summary": "same professional summary returned on the resume",
                "cover_letter": "' . ($this->generateCoverLetter ? 'a cover letter, with casual phrasing,
                    3 paragraphs as an array, no introduction (i.e. dear hiring manager or so), no signature,
                    i.e. [
                        \"paragraph 1, why am I a good fit\",
                        \"paragraph 2, emphasise accomplishments\",
                        \"paragraph 3 emphasise strengths and the need to get in touch\"]' : 'an empty array: []') . '",
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
                "top_choice": "if compatibility_score is greater or equals '.($this->settings['compatibilityScoreLevels']['top'] ?? 95).',
                               In first person, as if you are talking to a potential employer,
                               Briefly describe (up to 400 chars) why this job is your top choice and why you’re a good fit.
                               Otherwise the value is an empty string",
            }
            Do NOT include any other text.',
            !empty($this->settings['instructions']) ? $this->settings['instructions'] : false,
            $this->changeProfessionalSummary ? 'should replace the Professional Summary section with a role specific summary emphasizing the candidate\'s strengths (keep the title)' : false,
            $this->changeTargetRole ? "should replace the \"Target Role\" (below the name on title) with: {$this->roleName}" : false,
            $this->mentionRelocationAvailability ? "at the bottom, using the class \"footer\" on the element, should add
                                                      \"Available for remote work or relocation to [country] through visa sponsorship\"
                                                      where the country/city is: {$this->roleLocation}.
                                                      If a city name is provided, use it's country name instead" : false,
            $this->makeGrammaticalCorrections ? 'should make grammatical corrections' : false,
            $this->mentionRelocationAvailability ? 'Include in the missing_requirements output if the company has no history of sponsoring a visa.' : false,
        ];

        return array_filter($config);
    }

    public function user(): array
    {
        return [
            "Please improve the resume, following {$this->roleLocation}'s pattern and best practices
             for a higher employee selection rate, reorganize the sections according with the country's
             requirements and keep the title.
             Role description is: {$this->roleDescription}"
        ];
    }
}
