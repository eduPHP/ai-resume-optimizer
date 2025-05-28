<?php

namespace App\Http\Controllers;

use App\Contracts\AIAgentPrompter;
use App\Models\Optimization;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OptimizationController
{
    public function index(Request $request): JsonResponse
    {
        $optimizations = $request->user()->optimizations()->latest('created_at')->get()->map(fn($optimization) => [
            'id' => $optimization->id,
            'href' => route('optimizations.show', $optimization),
            'title' => $optimization->role_company,
            'created' => $optimization->created_at->format('Y-m-d g:i A'),
        ]);

        if ($request->has('grouped')) {
            $optimizations = $optimizations->map(function ($resume) {
                return [
                    ...$resume,
                    'group' => $this->getDayGroup($resume['created']),
                ];
            })->groupBy('group');
        } else {
            $optimizations = $optimizations->values();
        }


        return response()->json($optimizations);
    }

    private function getDayGroup(string $date): string
    {
        if (Carbon::parse($date)->isToday()) {
            return 'Today';
        }

        if (Carbon::parse($date)->isYesterday()) {
            return 'Yesterday';
        }

        return 'Previous Days';
    }

    public function show(Request $request, Optimization $optimization): \Inertia\Response
    {
        return Inertia::render('ResumeWizard', [
            'optimization' => $optimization,
        ]);
    }

    public function update(Request $request, Optimization $optimization): \Illuminate\Http\JsonResponse
    {
        // current step
        $step = (int) $request->header('X-CurrentStep');

        // sleep(2);
        $handlers = [
            0 => 'handleRoleInformation',
            1 => 'handleResume',
            2 => 'handleAdditionalInformation',
            3 => 'handleCompletion',
        ];

        $optimization = $this->{$handlers[$step]}($request, $optimization);

        return response()->json([
            'step' => $step,
            'optimization' => $optimization,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // current step
        $step = (int) $request->header('X-CurrentStep');

        // sleep(2);
        $handlers = [
            0 => 'handleRoleInformation',
            1 => 'handleResume',
            2 => 'handleAdditionalInformation',
            3 => 'handleCompletion',
        ];

        $optimization = $this->{$handlers[$step]}($request);

        return response()->json([
            'step' => $step,
            'optimization' => $optimization,
            'created' => $optimization->wasRecentlyCreated,
        ]);
    }

    public function handleRoleInformation(Request $request, Optimization $optimization = null): Optimization
    {
        $request->validate([
            'name' => 'required',
            'company' => 'required',
            'description' => 'required'
        ]);

        $data = [
            'role_name' => $request->input('name'),
            'role_company' => $request->input('company'),
            'role_description' => $request->input('description'),
            'current_step' => 1,
        ];
        if ($optimization) {
            $optimization->update($data);
        } else {
            $optimization = $request->user()->optimizations()->create($data);
        }

        return $optimization;
    }

    private function handleResume(Request $request, Optimization $optimization): Optimization
    {
        $request->validate([
            'id' => 'required',
        ], ['id.required' => 'Please select or upload a resume to optimize']);

        $optimization->update([
            'current_step' => 2,
            'resume_id' => $request->input('id'),
        ]);

        return $optimization->fresh();
    }

    private function handleAdditionalInformation(Request $request, Optimization $optimization): Optimization
    {
        $request->validate([
            'makeGrammaticalCorrections' => 'boolean',
            'changeProfessionalSummary' => 'boolean',
            'changeTargetRole' => 'boolean',
            'mentionRelocationAvailability' => 'boolean',
            'targetCountry' => 'nullable|string|max:30',
        ]);

        $optimization->update([
            'current_step' => 3,
            'make_grammatical_corrections' => $request->input('makeGrammaticalCorrections'),
            'change_professional_summary' => $request->input('changeProfessionalSummary'),
            'change_target_role' => $request->input('changeTargetRole'),
            'mention_relocation_availability' => $request->input('mentionRelocationAvailability'),
            'role_location' => $request->input('targetCountry'),
        ]);

        return $optimization->fresh();
    }

    private function handleCompletion(Request $request, Optimization $optimization): Optimization
    {
        $optimization->update([
            'status' => 'processing',
        ]);
        // send to AI agent
        $result = $this->agentQuery($optimization);

        // show processing page
        $optimization->update([
            'status' => 'complete',
            'optimized_result' => $result['response'],
            'reasoning' => $result['reasoning'],
        ]);

        return $optimization->fresh();
    }

    public function agentQuery(Optimization $optimization): array
    {
        $content = $optimization->resume->detected_content;

        $prompt = "
Please improve the following resume, following the {$optimization->role_location} pattern and best practices for a higher employee selection rate,
";
        if ($optimization->make_grammatical_corrections) {
            $prompt .= "Make grammatical corrections,
";
        }
        if ($optimization->change_professional_summary) {
            $prompt .= "Replace the Professional Summary section with a role specific summary emphasizing the candidate's strengths (keep the title),
";
        }
        if ($optimization->change_target_role) {
            $prompt .= "Replace the Target Role with: {$optimization->role_name},
";
        }
        if ($optimization->mention_relocation_availability) {
            $prompt .= "Add \"Available for remote work or relocation to [country] through visa sponsorship\" where the country is: {$optimization->role_location},
";
        }
        $prompt .= "Company: {$optimization->role_company}
Role:
{$optimization->role_description}

Resume:
{$content}
";

        $agentResponse = app()->make(AIAgentPrompter::class)->handle($prompt);

        return [
            'prompt' => $prompt,
            'response' => $agentResponse->getResponse(),
            'reasoning' => $agentResponse->getReasoning(),
        ];
    }
}
