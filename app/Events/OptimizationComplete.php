<?php

namespace App\Events;

use App\Http\Controllers\PresentOptimization;
use App\Models\Optimization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OptimizationComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, PresentOptimization;

    public function __construct(public Optimization $optimization)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'optimization' => $this->presentForListing($this->optimization),
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
