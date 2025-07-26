<?php

namespace Tests\Support;


use App\DTO\Contracts\AIAgentPrompter;

class AiAgentPrompterStub implements AiAgentPrompter {

    public function handle(\App\DTO\AIInputOptions $options): \App\DTO\Contracts\AIResponseDTO
    {
        return new FakeAIResponse;
    }
}
