<?php

namespace App\Jobs;

use App\Events\OptimizationComplete;
use App\Http\Controllers\PromptsAIAgent;
use App\Models\Optimization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class OptimizeResume implements ShouldQueue
{
    use Queueable;
    use PromptsAIAgent;
    use InteractsWithQueue;

    public function attempts(): int
    {
        return 2;
    }

    public function __construct(public Optimization $optimization)
    {
        //
    }

    public function handle(): void
    {

        Log::info('OptimizeResume job ran for optimization: ' . $this->optimization->id);
        // $result = $this->agentQuery($optimization);
        $this->optimization->update(['status' => 'processing']);

        // $optimization->update([
        //     'status' => 'complete',
        //     'optimized_result' => $result['resume'],
        //     'ai_response' => $result['response'],
        //     'reasoning' => $result['reasoning'],
        // ]);

        event(new OptimizationComplete($this->optimization));
    }
}
