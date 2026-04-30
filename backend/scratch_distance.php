<?php
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // meters

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius;
}

$placeLat = 37.9153309;
$placeLng = 139.0624934;
$userLat = 37.913844233630414;
$userLng = 139.06106414515767;

$distance = calculateDistance($placeLat, $placeLng, $userLat, $userLng);
echo "Distance: " . $distance . " meters\n";
