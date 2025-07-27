<?php

namespace App\Http\Controllers;

use App\DTO\AIInputOptions;
use App\DTO\Contracts\AIAgentPrompter;
use App\Models\Optimization;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class OptimizationController
{
    public function index(): JsonResponse
    {
        $perPage = request()->integer('per_page', 10);

        $optimizations = request()->user()->optimizations()
            ->latest('created_at')
            ->paginate($perPage)
            ->through(fn (Optimization $optimization) => [
                'id' => $optimization->id,
                'href' => route('optimizations.show', $optimization),
                'title' => ($optimization->status === 'draft' ? '[draft] ' : '') . $optimization->role_company,
                'score' => $this->getCompatibilityScore($optimization),
                'status' => $optimization->status,
                'tooltip' => $optimization->role_name,
                'created' => $optimization->created_at
                    ->utcOffset(request()->header('X-Timezone-Offset') ?? 0)
                    ->toDateTimeString(),
            ]);

        return response()->json([
            'data' => $optimizations->items(),
            'next_page_url' => $optimizations->nextPageUrl(),
        ]);
    }

    public function show(Optimization $optimization): \Inertia\Response
    {
        return Inertia::render('Optimization', [
            'optimization' => $optimization,
        ]);
    }

    public function update(Optimization $optimization): \Illuminate\Http\JsonResponse
    {
        // current step
        $step = (int) request()->header('X-CurrentStep');

        // sleep(2);
        $handlers = [
            0 => 'handleRoleInformation',
            1 => 'handleResume',
            2 => 'handleAdditionalInformation',
            3 => 'handleCompletion',
        ];

        $optimization = $this->{$handlers[$step]}($optimization);

        return response()->json([
            'step' => $step,
            'optimization' => $optimization,
        ]);
    }

    public function store(): \Illuminate\Http\JsonResponse
    {
        // current step
        $step = (int) request()->header('X-CurrentStep');

        // sleep(2);
        $handlers = [
            0 => 'handleRoleInformation',
            1 => 'handleResume',
            2 => 'handleAdditionalInformation',
            3 => 'handleCompletion',
        ];

        $optimization = $this->{$handlers[$step]}();

        return response()->json([
            'step' => $step,
            'optimization' => $optimization,
            'created' => $optimization->wasRecentlyCreated,
        ]);
    }

    public function handleRoleInformation(Optimization $optimization = null): Optimization
    {
        request()->validate([
            'name' => 'required',
            'company' => 'required',
            'description' => 'required',
            'url' => 'required|url',
        ]);

        $data = [
            'role_name' => request()->input('name'),
            'role_url' => request()->input('url'),
            'role_company' => request()->input('company'),
            'role_description' => request()->input('description'),
            'current_step' => 1,
            'generate_cover_letter' => true,
            'status' => 'draft',
        ];
        if ($optimization) {
            $optimization->update($data);
        } else {
            $optimization = request()->user()->optimizations()->create($data);
        }

        return $optimization;
    }

    private function handleResume(Optimization $optimization): Optimization
    {
        request()->validate([
            'id' => 'required',
        ], ['id.required' => 'Please select or upload a resume to optimize']);

        $optimization->update([
            'current_step' => 2,
            'resume_id' => request()->input('id'),
        ]);

        return $optimization->fresh();
    }

    private function handleAdditionalInformation(Optimization $optimization): Optimization
    {
        request()->validate([
            'makeGrammaticalCorrections' => 'boolean',
            'changeProfessionalSummary' => 'boolean',
            'generateCoverLetter' => 'boolean',
            'changeTargetRole' => 'boolean',
            'mentionRelocationAvailability' => 'boolean',
            'targetCountry' => 'nullable|string|max:30',
        ]);

        $optimization->update([
            'current_step' => 3,
            'make_grammatical_corrections' => request()->input('makeGrammaticalCorrections'),
            'change_professional_summary' => request()->input('changeProfessionalSummary'),
            'generate_cover_letter' => request()->input('generateCoverLetter'),
            'change_target_role' => request()->input('changeTargetRole'),
            'mention_relocation_availability' => request()->input('mentionRelocationAvailability'),
            'role_location' => request()->input('targetCountry'),
        ]);

        return $optimization->fresh();
    }

    private function handleCompletion(Optimization $optimization): Optimization
    {
        $optimization->update([
            'status' => 'processing',
        ]);
        // send to AI agent
        $result = $this->agentQuery($optimization);

        // show processing page
        $optimization->update([
            'status' => 'complete',
            'optimized_result' => $result['resume'],
            'ai_response' => $result['response'],
            'reasoning' => $result['reasoning'],
        ]);

        return $optimization->fresh();
    }

    public function agentQuery(Optimization $optimization): array
    {
        $content = $optimization->resume->detected_content;

        /** @var AIAgentPrompter $prompter */
        $prompter = app()->make(AIAgentPrompter::class);

        $agentResponse = $prompter->handle(new AIInputOptions(
            resume: $content,
            makeGrammaticalCorrections: $optimization->make_grammatical_corrections,
            changeProfessionalSummary: $optimization->change_professional_summary,
            generateCoverLetter: $optimization->generate_cover_letter,
            changeTargetRole: $optimization->change_target_role,
            mentionRelocationAvailability: $optimization->mention_relocation_availability,
            roleName: $optimization->role_name,
            roleDescription: $optimization->role_description,
            roleLocation: $optimization->role_location,
            roleCompany: $optimization->role_company,
        ));

        return [
            'response' => $agentResponse->toArray(),
            'resume' => $agentResponse->getResume(),
            'reasoning' => $agentResponse->getReasoning(),
        ];
    }

    public function destroy(Optimization $optimization): \Illuminate\Http\JsonResponse
    {
        abort_unless($optimization->user->is(request()->user()), 403);

        $optimization->delete();

        return response()->json([], 201);
    }

    public function cancel(Optimization $optimization): \Illuminate\Http\JsonResponse
    {
        abort_unless($optimization->user->is(request()->user()), 403);

        $optimization->update([
            'status' => 'complete',
            'current_step' => 3,
        ]);

        return response()->json([
            'optimization' => $optimization->fresh(),
        ]);
    }

    private function getCompatibilityScore(Optimization $optimization): int
    {
        if ($optimization->status !== 'complete') {
            return 0;
        }

        return $optimization->ai_response['compatibility_score'];
    }
}
