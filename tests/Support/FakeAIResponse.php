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

    public function getCompatibilityScore(): int
    {
        return 0;
    }

    public function getProfessionalSummary(): string
    {
        return "";
    }

    public function getFindings(): array
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

    public function getUsage(): int
    {
        return 0;
    }

    public function getId(): ?string
    {
        return 'some-key';
    }
}
