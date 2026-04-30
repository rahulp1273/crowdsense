<?php
namespace App\Services\User;

use App\Models\Place;
use App\Services\User\LocationService;
use Illuminate\Support\Facades\Cache;

class PlaceService
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function listPlacesWithDistance($userLat, $userLng)
    {
        $places = Place::all();

        if ($userLat && $userLng) {
            // Map distances
            $places = $places->map(function ($place) use ($userLat, $userLng) {
                if ($place->lat && $place->lng) {
                    $place->distance = $this->locationService->calculateDistance($userLat, $userLng, $place->lat, $place->lng);
                } else {
                    $place->distance = 9999999; // unknown distance
                }
                return $place;
            });

            // Sort by distance (ASC) then crowd level (DESC)
            return $places->sortBy([
                ['distance', 'asc'],
                ['current_crowd_count', 'desc']
            ])->values();
        }

        // Fallback sort if no GPS provided
        return $places->sortByDesc('current_crowd_count')->values();
    }

    public function getCrowdStatus($placeId)
    {
        $place = Place::findOrFail($placeId);
        return [
            'place_id' => $place->id,
            'current_crowd_count' => $place->current_crowd_count,
            'max_capacity' => $place->max_capacity,
            'is_chat_enabled' => $place->is_chat_enabled
        ];
    }
}
