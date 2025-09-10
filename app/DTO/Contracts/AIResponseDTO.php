<?php

namespace App\DTO\Contracts;


interface AIResponseDTO
{
    public function getResume(): ?string;
    public function getCompatibilityScore(): int;
    public function getProfessionalSummary(): ?string;
    public function getFindings(): array;
    public function getReasoning(): ?string;
    public function getTopChoiceMessage(): ?string;
}
