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
        if (!$replyTo = MessageDTO::fromCache('message-' . $event->optimization->id)) {
            return;
        }

        $reply = new MessageDTO(
            body: $this->getMessage($event->optimization),
            phone: $replyTo->phone,
            replyTo: $replyTo,
        );
        \Log::debug('reply', ['reply' => $reply->toArray()]);

        app(Messenger::class)->text($reply);
    }

    private function getMessage(Optimization $optimization): string
    {
        if ($optimization->status === OptimizationStatuses::Complete) {
            return "✅ *Completo!*\n*Compatibilidade*: {$optimization->ai_response['compatibility_score']}%\n".route('optimizations.show', $optimization);
        }

        return '*⚠️ Resultado*: '.$optimization->status->title();
    }
}
