<?php
namespace App\Services\User;

use App\Models\Place;
use App\Models\CheckIn;
use App\Events\CrowdUpdated;
use App\Events\UserCheckedIn;
use App\Events\UserCheckedOut;
use Carbon\Carbon;

class CrowdService
{
    public function checkIn($placeId, $user)
    {
        $place = Place::findOrFail($placeId);

        // Prevent duplicate active check-ins
        $existing = CheckIn::where('place_id', $placeId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return ['error' => 'Already checked in'];
        }

        $weight = $user->weight ?? 1;

        $checkIn = CheckIn::create([
            'place_id' => $placeId,
            'user_id' => $user->id,
            'is_active' => true,
            'expires_at' => Carbon::now()->addHours(3),
            'weight_applied' => $weight
        ]);

        $place->increment('current_crowd_count', $weight);

        event(new UserCheckedIn($place, $user));
        event(new CrowdUpdated($place));

        return $place;
    }

    public function checkOut($placeId, $user)
    {
        $place = Place::findOrFail($placeId);

        $checkIn = CheckIn::where('place_id', $placeId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if ($checkIn) {
            $checkIn->update(['is_active' => false]);
            $place->decrement('current_crowd_count', $checkIn->weight_applied);

            event(new UserCheckedOut($place, $user));
            event(new CrowdUpdated($place));
        }

        return $place;
    }
}
