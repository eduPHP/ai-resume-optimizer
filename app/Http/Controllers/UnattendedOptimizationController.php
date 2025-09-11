<?php

namespace App\Http\Controllers;

use App\Enums\OptimizationStatuses;
use App\Jobs\OptimizeResume;
use App\Models\Optimization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnattendedOptimizationController
{
    use CreateUnattendedOptimization;

    public function store(Request $request): JsonResponse
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
                'message' => [config('setup.errors.resume_not_found')],
            ]], 422);
        }

        $optimization = $this->createOptimizationFor($resume, data: collect($request->all()));

        return response()->json([
            'created' => true,
            'optimization' => $optimization,
        ]);
    }

    public function update(Optimization $optimization)
    {
        try {
            $optimization->update([
                'status' => OptimizationStatuses::Processing,
            ]);

            OptimizeResume::dispatch($optimization)->onQueue('long-jobs');
        } catch (\Exception $exception) {
            $optimization->update([
                'status' => OptimizationStatuses::Failed,
                'reasoning' => $exception->getMessage(),
            ]);
        } finally {
            return response()->json([
                'updated' => true,
                'optimization' => $optimization,
            ]);
        }
    }

    public function show(Optimization $optimization)
    {
        return response()->json([
            'optimization' => $optimization,
        ]);
    }
}
