<?php

namespace Modules\OpenAI;

use App\DTO\Contracts\AIResponseDTO;
use Illuminate\Contracts\Support\Arrayable;

class OpenAiResponse implements AIResponseDTO, Arrayable
{
    public function __construct(
        public ?string $resume = null,
        public int $compatibility_score = 0,
        public ?string $top_choice = null,
        public ?string $professional_summary = null,
        public array $cover_letter = [],
        public array $findings = [],
        public ?string $reasoning = null,
        public int $usage = 0,
        public ?string $id = null,
    ) {
    }

    public function getResume(): string
    {
        return $this->resume;
    }

    public function getCompatibilityScore(): int
    {
        return $this->compatibility_score;
    }

    public function getProfessionalSummary(): ?string
    {
        return $this->professional_summary;
    }

    public function getFindings(): array
    {
        return $this->findings;
    }

    public function getReasoning(): string
    {
        return $this->reasoning;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getTopChoiceMessage(): string
    {
        return $this->top_choice;
    }

    public function getUsage(): int
    {
        return $this->usage;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
