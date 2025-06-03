<?php

namespace App\DTO\Contracts;


interface AIResponseDTO
{
    public function getResume(): string;
    public function getCompatibilityScore(): string;
    public function getProfessionalSummary(): string;
    public function getStrongAlignments(): array;
    public function getModerateGaps(): array;
    public function getMissingRequirements(): array;
    public function getReasoning(): string;
}
