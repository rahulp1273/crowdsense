<?php
namespace App\Events;

use App\Models\Place;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCheckedOut implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $place;
    public $user;

    public function __construct(Place $place, User $user)
    {
        $this->place = $place;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('place.' . $this->place->id);
    }
}
