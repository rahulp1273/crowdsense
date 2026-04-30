<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inquiry;
use App\Models\CheckIn;
use App\Models\User;
use App\Models\Place;
use Carbon\Carbon;

class AnalyticsSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $places = Place::all();
        
        if (!$user || $places->isEmpty()) return;

        // Clear existing to avoid bloat
        Inquiry::truncate();
        CheckIn::truncate();

        // Seed inquiries over the last 10 days
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 10))->subHours(rand(0, 23));
            $inquiry = new Inquiry([
                'user_id' => $user->id,
                'place_id' => $places->random()->id,
                'type' => collect(['report', 'suggestion', 'complaint'])->random(),
                'message' => 'Sample feedback message #' . $i,
                'status' => collect(['new', 'seen'])->random(),
            ]);
            // Force set the dates
            $inquiry->created_at = $date;
            $inquiry->updated_at = $date;
            $inquiry->save();
        }

        // Seed check-ins over various hours for the last 7 days
        for ($i = 0; $i < 200; $i++) {
            $date = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23));
            $checkin = new CheckIn([
                'user_id' => $user->id,
                'place_id' => $places->random()->id,
                'is_active' => false,
            ]);
            $checkin->created_at = $date;
            $checkin->updated_at = $date;
            $checkin->save();
        }
    }
}
