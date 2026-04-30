<?php
namespace App\Services\User;

class LocationService
{
    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula. Returns distance in meters.
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
          
        return $angle * $earthRadius;
    }

    /**
     * Checks if a given coordinate is within the allowed radius of a place.
     */
    public function isInsideRadius($userLat, $userLng, $placeLat, $placeLng, $radius)
    {
        if (!$placeLat || !$placeLng) {
            // If the place has no GPS data, allow fallback logic or deny.
            return true; // fallback for development
        }
        $distance = $this->calculateDistance($userLat, $userLng, $placeLat, $placeLng);
        return $distance <= $radius;
    }

    /**
     * Look up location details by pincode.
     */
    public function lookupPincode($pincode, $country = 'IN')
    {
        // Sanitize pincode (remove hyphens and spaces)
        $pincode = str_replace([' ', '-'], '', $pincode);
        
        try {
            // Using Nominatim for better global coverage (including Japan)
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'CrowdSenseSaaS/1.0'
            ])->get("https://nominatim.openstreetmap.org/search", [
                'postalcode' => $pincode,
                'country' => $country,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                $address = $data['address'];
                
                return [
                    'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['suburb'] ?? '',
                    'state' => $address['state'] ?? $address['province'] ?? '',
                    'country' => $address['country'] ?? $country,
                    'lat' => $data['lat'],
                    'lng' => $data['lon'],
                    'pincode' => $pincode,
                    'country_code' => $country
                ];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Global Pincode lookup failed: " . $e->getMessage());
        }
        return null;
    }
}
