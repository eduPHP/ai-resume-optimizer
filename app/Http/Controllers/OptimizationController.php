<?php

namespace App\Http\Controllers;

use App\Models\Optimization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OptimizationController
{
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
            'current_step' => 1,
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
            'current_step' => 2,
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
        // send to AI agent
    }
}
