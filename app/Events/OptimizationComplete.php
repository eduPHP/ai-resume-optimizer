<?php

namespace App\Events;

use App\Models\Optimization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimizationComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Optimization $optimization)
    {
        Log::info('OptimizationComplete event fired for user: ' . $this->optimization->user_id);
    }

    public function broadcastWith(): array
    {
        return [
            'optimization' => $this->optimization->only([
                'id',
                'role_company',
                'role_name',
            ]),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('optimizations.' . $this->optimization->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'optimization.complete';
    }
}
