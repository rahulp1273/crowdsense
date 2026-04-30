<?php
namespace App\Services\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    public function getCoordinatesFromPincode($pincode)
    {
        if (!$pincode) return null;

        return Cache::remember('geo_pincode_' . $pincode, now()->addDays(30), function () use ($pincode) {
            $response = Http::withHeaders([
                'User-Agent' => 'CrowdSenseSaaS/1.0'
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'postalcode' => $pincode,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return [
                    'lat' => $data['lat'],
                    'lng' => $data['lon'],
                    'address' => $data['display_name'] ?? null
                ];
            }

            return null;
        });
    }
}
