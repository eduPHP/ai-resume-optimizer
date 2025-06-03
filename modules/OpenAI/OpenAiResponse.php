<?php

namespace Modules\OpenAI;

use App\DTO\Contracts\AIResponseDTO;
use Illuminate\Contracts\Support\Arrayable;

class OpenAiResponse implements AIResponseDTO, Arrayable
{
    public function __construct(
        public string $resume,
        public string $compatibility_score,
        public string $professional_summary,
        public array $strong_alignments = [],
        public array $moderate_gaps = [],
        public array $missing_requirements = [],
        public string $reasoning,
    ) {
    }

    public function getResume(): string
    {
        return $this->resume;
    }

    public function getCompatibilityScore(): string
    {
        return $this->compatibility_score;
    }

    public function getProfessionalSummary(): string
    {
        return $this->professional_summary;
    }

    public function getStrongAlignments(): array
    {
        return $this->strong_alignments;
    }

    public function getModerateGaps(): array
    {
        return $this->moderate_gaps;
    }

    public function getMissingRequirements(): array
    {
        return $this->missing_requirements;
    }

    public function getReasoning(): string
    {
        return $this->reasoning;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
