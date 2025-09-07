<?php

namespace App\Jobs;

use App\Events\OptimizationComplete;
use App\Http\Controllers\PromptsAIAgent;
use App\Models\Optimization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class OptimizeResume implements ShouldQueue
{
    use Queueable;
    use PromptsAIAgent;

    public function __construct()
    {
        Log::info('Instantiate job');
    }

    public function handle(Optimization $optimization): void
    {
        Log::info('OptimizeResume job ran for user: ' . $optimization->user_id);
        // $result = $this->agentQuery($optimization);
        $optimization->update(['status' => 'processing']);

        // $optimization->update([
        //     'status' => 'complete',
        //     'optimized_result' => $result['resume'],
        //     'ai_response' => $result['response'],
        //     'reasoning' => $result['reasoning'],
        // ]);

        event(new OptimizationComplete($optimization));
    }
}
