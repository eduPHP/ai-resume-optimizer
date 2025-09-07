<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnattendedOptimizationController
{
    use PromptsAIAgent;

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'company' => 'required',
            'description' => 'required',
            'url' => 'required|url',
            'location' => 'nullable',
        ]);

        if (! $resume = auth()->user()->resumes()->latest()->first()) {
            return response()->json(['errors' => [
                'resume' => [config('setup.errors.resume_not_found')],
            ]], 422);
        }

        $data = [
            'role_name' => request()->input('name'),
            'role_url' => request()->input('url'),
            'role_company' => request()->input('company'),
            'role_description' => request()->input('description'),
            'role_location' => request()->input('location'),
            'resume_id' => $resume->id,
            'current_step' => 3,
            'generate_cover_letter' => true,
            'status' => 'processing',
            'applied' => false,
            'make_grammatical_corrections' => true,
            'change_professional_summary' => true,
            'change_target_role' => true,
            'mention_relocation_availability' => $this->relocationRequired(),
        ];

        return DB::transaction(function () use ($data) {

            $optimization = request()->user()->optimizations()->create($data);

            $result = $this->agentQuery($optimization);

            $optimization->update([
                'status' => 'complete',
                'optimized_result' => $result['resume'],
                'ai_response' => $result['response'],
                'reasoning' => $result['reasoning'],
            ]);

            return [
                'created' => true,
                'optimization' => $optimization->fresh(),
            ];
        }, attempts: 2);
    }

    private function relocationRequired()
    {
        $location = strtolower(request()->input('location', config('setup.default_location')));

        if (! $location) {
            return true;
        }

        $remoteLocations = collect(config('setup.remote_locations'));
        $brazil = collect(['brazil', 'brasil']);

        return $remoteLocations->contains($location) || $brazil->contains($location);
    }
}
