<?php
namespace App\Events;

use App\Models\Place;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaceCreated
{
    use Dispatchable, SerializesModels;

    public $place;

    public function __construct(Place $place)
    {
        $this->place = $place;
    }
}
