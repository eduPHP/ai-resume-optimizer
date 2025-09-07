<?php

namespace App\Http\Controllers;


use App\DTO\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\Models\Optimization;

trait PromptsAIAgent
{
    public function agentQuery(Optimization $optimization): array
    {
        $content = $optimization->resume->detected_content;

        /** @var AIAgentPrompter $prompter */
        $prompter = app()->make(AIAgentPrompter::class);

        $agentResponse = $prompter->handle(new AIInputOptions(
            resume: $content,
            makeGrammaticalCorrections: $optimization->make_grammatical_corrections,
            changeProfessionalSummary: $optimization->change_professional_summary,
            generateCoverLetter: $optimization->generate_cover_letter,
            changeTargetRole: $optimization->change_target_role,
            mentionRelocationAvailability: $optimization->mention_relocation_availability,
            roleName: $optimization->role_name,
            roleDescription: $optimization->role_description,
            roleLocation: $optimization->role_location,
            roleCompany: $optimization->role_company,
        ));

        return [
            'response' => $agentResponse->toArray(),
            'resume' => $agentResponse->getResume(),
            'reasoning' => $agentResponse->getReasoning(),
        ];
    }
}
