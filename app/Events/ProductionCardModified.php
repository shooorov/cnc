<?php

namespace App\Events;

use App\Models\Production;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionCardModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The production instance.
     *
     * @var \App\Models\Production
     */
    public $production;

    /**
     * Create a new event instance.
     */
    public function __construct(Production $production)
    {
        $this->production = $production->only([
            'id',
            'type',
            'status',
            'viewed_at',
            'accepted_at',
            'completed_at',
            'delivered_at',
            'order_id',
            'branch_id',
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen.branches.'.$this->production['branch_id']),
            // new PrivateChannel('channel-name'),
        ];
    }
}
