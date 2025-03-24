<?php

namespace App\Contracts;


interface AIAgentPrompter
{
    public function handle(string $prompt): AIResponseDTO;

}
