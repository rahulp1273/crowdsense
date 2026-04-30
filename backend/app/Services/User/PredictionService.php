<?php
namespace App\Services\User;

use App\Models\CheckIn;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    public function getForecast($placeId)
    {
        // Simple mock of prediction logic:
        // We look at the average check-ins at this hour over the last 7 days.
        $hour = Carbon::now()->hour;
        
        $avgPastCrowd = CheckIn::where('place_id', $placeId)
            ->whereRaw('strftime("%H", created_at) = ?', [sprintf("%02d", $hour)])
            ->count() / 7; // simplified average

        return [
            'predicted_crowd' => round($avgPastCrowd),
            'suggestion' => $avgPastCrowd > 50 ? 'Peak expected soon, avoid if possible.' : 'Good time to visit.'
        ];
    }
}
