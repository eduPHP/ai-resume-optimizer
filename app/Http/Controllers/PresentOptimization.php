<?php

namespace App\Http\Controllers;


use App\Enums\OptimizationStatuses;
use App\Models\Optimization;

trait PresentOptimization
{

    protected function getCompatibilityScore(Optimization $optimization): int
    {
        if ($optimization->status !== OptimizationStatuses::Complete) {
            return 0;
        }

        return $optimization->ai_response['compatibility_score'];
    }

    public function simplifiedAiResponse(array $aiResponse): array
    {
        return [
            'compatibility_score' => $aiResponse['compatibility_score'],
        ];
    }

    protected function presentForListing(Optimization $optimization)
    {
        return [
            'id' => $optimization->id,
            'href' => route('optimizations.show', $optimization),
            'title' => ($optimization->status === OptimizationStatuses::Draft ? '[draft] ' : '') . $optimization->role_company,
            'score' => $this->getCompatibilityScore($optimization),
            'status' => $optimization->status->value,
            'applied' => $optimization->applied,
            'tooltip' => $optimization->role_name,
            'created' => $optimization->created_at
                ->utcOffset(request()->header('X-Timezone-Offset') ?? 0)
                ->toDateTimeString(),
        ];
    }
}
