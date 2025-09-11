<?php

namespace App\Jobs;

use App\Enums\OptimizationStatuses;
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
        try {
            $result = $this->agentQuery($this->optimization);
            $this->optimization->update([
                'status' => OptimizationStatuses::Complete,
                'optimized_result' => $result['resume'],
                'ai_response' => $result['response'],
                'reasoning' => $result['reasoning'],
                'usage_tokens' => $result['usage'],
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::error('Error trying to optimize', [
                'message' => $message,
                'optimization' => $this->optimization->id,
            ]);
            $this->optimization->update([
                'status' => 'failed',
                'reasoning' => $message,
            ]);

        }

        event(new OptimizationComplete($this->optimization));
    }
}
