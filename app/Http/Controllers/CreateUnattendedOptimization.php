<?php

namespace App\Http\Controllers;


use App\Jobs\OptimizeResume;
use App\Models\Resume;
use Illuminate\Support\Collection;

trait CreateUnattendedOptimization
{
    protected function createOptimizationFor(Resume $resume, Collection $data)
    {
        $data = [
            'role_name' => $data->get('name'),
            'role_url' => $data->get('url'),
            'role_company' => $data->get('company'),
            'role_description' => $data->get('description'),
            'role_location' => $data->get('location'),
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

        return $optimization;
    }

    protected function relocationRequired()
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
