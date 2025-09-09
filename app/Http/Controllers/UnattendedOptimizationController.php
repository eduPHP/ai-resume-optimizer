<?php

namespace App\Http\Controllers;

use App\Models\Optimization;
use Illuminate\Http\Request;

class UnattendedOptimizationController
{
    use CreateUnattendedOptimization;

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

        $optimization = $this->createOptimizationFor($resume, data: collect($request->all()));

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
}
