<?php

namespace App\DTO\Contracts;


interface AIResponseDTO
{
    public function getResponse(): string;
    public function getReasoning(): string;
}
