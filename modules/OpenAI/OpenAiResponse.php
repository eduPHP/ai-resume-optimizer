<?php

namespace Modules\OpenAI;

use App\Contracts\AIResponseDTO;

class OpenAiResponse implements AIResponseDTO
{
    public function __construct(public string $response, public string $reasoning)
    {
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getReasoning(): string
    {
        return $this->reasoning;
    }
}
