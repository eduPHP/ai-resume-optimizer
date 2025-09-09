<?php

namespace App\Http\Controllers;

use App\Jobs\OptimizeResume;
use App\Models\Optimization;
use Illuminate\Http\Request;

class UnattendedOptimizationController
{
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

        $optimization = request()->user()->optimizations()->create($data);

        OptimizeResume::dispatch($optimization)->onQueue('long-jobs');

        return [
            'created' => true,
            'optimization' => $optimization,
        ];
    }

    public function show(Optimization $optimization)
    {
        return response()->json([
            'optimization' => $optimization,
        ]);
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
