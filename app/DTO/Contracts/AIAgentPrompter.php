<?php

namespace App\DTO\Contracts;


use App\DTO\AIInputOptions;

interface AIAgentPrompter
{
    public function handle(AIInputOptions $options): AIResponseDTO;

}
