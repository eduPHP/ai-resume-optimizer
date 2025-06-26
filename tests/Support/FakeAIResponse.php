<?php
namespace Tests\Support;


use App\DTO\Contracts\AIResponseDTO;
use Illuminate\Contracts\Support\Arrayable;

class FakeAIResponse implements AIResponseDTO, Arrayable {

    public function getResume(): string
    {
        return "All looks great for me, no need to change anything at all.";
    }

    public function getReasoning(): string
    {
        return 'Beer drinking is such a great skill to have on a job interview.';
    }

    public function getCompatibilityScore(): string
    {
        return "";
    }

    public function getProfessionalSummary(): string
    {
        return "";
    }

    public function getStrongAlignments(): array
    {
        return [];
    }

    public function getModerateGaps(): array
    {
        return [];
    }

    public function getMissingRequirements(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [];
    }

    public function getTopChoiceMessage(): string
    {
        return '';
    }
}
