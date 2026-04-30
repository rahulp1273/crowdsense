<?php
namespace App\Services\User;

use App\Models\CheckIn;
use App\Models\Place;
use App\Services\User\LocationService;

class CheckInService
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function checkIn($user, $placeId, $lat, $lng)
    {
        $place = Place::findOrFail($placeId);

        // Validate GPS location
        $distance = $this->locationService->calculateDistance($lat, $lng, $place->lat, $place->lng);
        if ($distance > $place->radius) {
            $distanceM = round($distance);
            $error = new \Exception("You are too far from this place.");
            $error->details = [
                'distance' => $distanceM,
                'radius' => $place->radius
            ];
            throw $error;
        }

        // Check duplicate active session
        $activeCheckIn = $this->getActiveCheckIn($user->id);
        if ($activeCheckIn) {
            if ($activeCheckIn->place_id == $placeId) {
                throw new \Exception("You are already checked in here.");
            } else {
                throw new \Exception("You have an active check-in at another place. Please checkout first.");
            }
        }

        // Apply weight logic and create session
        $weight = $user->weight ?? 1;
        $checkIn = CheckIn::create([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'is_active' => true,
            'expires_at' => now()->addHours(3),
            'weight_applied' => $weight
        ]);

        $place->increment('current_crowd_count', $weight);

        // Fire event to broadcast real-time UI updates
        event(new \App\Events\CrowdUpdated($place->id, $place->current_crowd_count));

        return $checkIn;
    }

    public function checkOut($user)
    {
        $activeCheckIn = $this->getActiveCheckIn($user->id);
        if (!$activeCheckIn) {
            throw new \Exception("No active check-in found.");
        }

        $activeCheckIn->update(['is_active' => false]);
        
        $place = Place::find($activeCheckIn->place_id);
        if ($place) {
            // Ensure count doesn't drop below zero
            if ($place->current_crowd_count >= $activeCheckIn->weight_applied) {
                $place->decrement('current_crowd_count', $activeCheckIn->weight_applied);
            } else {
                $place->update(['current_crowd_count' => 0]);
            }
            event(new \App\Events\CrowdUpdated($place->id, $place->current_crowd_count));
        }

        return $activeCheckIn;
    }

    public function getActiveCheckIn($userId)
    {
        return CheckIn::where('user_id', $userId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function getActivePlaceDetails($userId)
    {
        $activeCheckIn = $this->getActiveCheckIn($userId);
        if (!$activeCheckIn) return null;

        return Place::find($activeCheckIn->place_id);
    }
}
