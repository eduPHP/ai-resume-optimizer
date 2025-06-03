<?php

namespace App\Http\Controllers;

use App\DTO\Contracts\AIAgentPrompter;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptimizeResumeController
{
    public function __invoke(Request $request, AIAgentPrompter $service, Resume $resume): JsonResponse
    {
        $content = $resume->detected_content;
        $role_details = $request->input('role_details');

        $prompt = "
Please improve the following resume, following the USA pattern and best practices for a higher employee selection rate
Role:
{$role_details}

Resume:
{$content}
";

        $agentResponse = $service->handle($prompt);

        $resume->update([
            'role_details' => $role_details,
            'optimized_result' => $agentResponse->getResume(),
            'reasoning' => $agentResponse->getReasoning(),
        ]);

        return response()->json([
            'prompt' => $prompt,
            'response' => $agentResponse->getResume(),
            'reasoning' => $agentResponse->getReasoning(),
        ]);
    }
}
