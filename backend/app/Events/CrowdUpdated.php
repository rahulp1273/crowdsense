<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrowdUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $placeId;
    public $crowdCount;

    /**
     * Create a new event instance.
     */
    public function __construct($placeId, $crowdCount)
    {
        $this->placeId = $placeId;
        $this->crowdCount = $crowdCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return ['crowd'];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'crowd.updated';
    }
}
