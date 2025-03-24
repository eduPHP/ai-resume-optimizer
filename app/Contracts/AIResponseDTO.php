<?php

namespace App\Contracts;


interface AIResponseDTO
{
    public function getResponse(): string;
    public function getReasoning(): string;
}
