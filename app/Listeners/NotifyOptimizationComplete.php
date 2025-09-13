<?php

namespace App\Listeners;

use App\Enums\OptimizationStatuses;
use App\Events\OptimizationComplete;
use App\Models\Optimization;
use Modules\WAConnect\MessageDTO;
use Modules\WAConnect\Messenger;

class NotifyOptimizationComplete
{
    /**
     * Handle the event.
     */
    public function handle(OptimizationComplete $event): void
    {
        if (!$replyTo = MessageDTO::fromCache($key = 'message-' . $event->optimization->id)) {
            return;
        }

        $reply = new MessageDTO(
            body: $this->getMessage($event->optimization),
            phone: $replyTo->phone,
            replyTo: $replyTo,
        );
        \Log::debug('reply', ['reply' => $reply->toArray()]);

        app(Messenger::class)->text($reply);

        if ($event->optimization->status === OptimizationStatuses::Complete) {
            // cache()->forget($key);
        }
    }

    private function getMessage(Optimization $optimization): string
    {
        if ($optimization->status === OptimizationStatuses::Complete) {
            return "*Completed!*\n*Score*: {$optimization->ai_response['compatibility_score']}\n".route('optimizations.show', $optimization);
        }

        return '*Result*: '.$optimization->status->title();
    }
}
