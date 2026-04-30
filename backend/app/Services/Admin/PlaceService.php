<?php
namespace App\Services\Admin;

use App\Models\Place;
use App\Services\Admin\GeolocationService;

class PlaceService
{
    protected $geoService;

    public function __construct(GeolocationService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function getAllPlaces()
    {
        return \Illuminate\Support\Facades\Cache::remember('all_places', now()->addHours(1), function () {
            return Place::all();
        });
    }

    public function createPlace($data)
    {
        if (!empty($data['pincode']) && (empty($data['lat']) || empty($data['lng']))) {
            $geoData = $this->geoService->getCoordinatesFromPincode($data['pincode']);
            if ($geoData) {
                $data['lat'] = $data['lat'] ?? $geoData['lat'];
                $data['lng'] = $data['lng'] ?? $geoData['lng'];
                $data['address'] = $data['address'] ?? $geoData['address'];
            }
        }
        $place = Place::create($data);
        event(new \App\Events\PlaceCreated($place));
        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
        \Illuminate\Support\Facades\Cache::forget('all_places');
        return $place;
    }

    public function updatePlace($id, $data)
    {
        $place = Place::findOrFail($id);

        if (!empty($data['pincode']) && $data['pincode'] !== $place->pincode) {
            $geoData = $this->geoService->getCoordinatesFromPincode($data['pincode']);
            if ($geoData) {
                $data['lat'] = $geoData['lat'];
                $data['lng'] = $geoData['lng'];
                $data['address'] = $geoData['address'];
            }
        }

        $place->update($data);
        \Illuminate\Support\Facades\Cache::forget('all_places');
        return $place;
    }

    public function deletePlace($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();
        \Illuminate\Support\Facades\Cache::forget('all_places');
        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
        return true;
    }
}
