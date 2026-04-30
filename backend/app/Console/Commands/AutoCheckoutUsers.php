<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CheckIn;
use App\Models\Place;
use App\Events\CrowdUpdated;
use Carbon\Carbon;

class AutoCheckoutUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-checkout-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout users whose sessions have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCheckIns = CheckIn::where('is_active', true)
            ->where('expires_at', '<', Carbon::now())
            ->get();

        if ($expiredCheckIns->isEmpty()) {
            $this->info('No expired check-ins found.');
            return;
        }

        foreach ($expiredCheckIns as $checkIn) {
            $checkIn->update(['is_active' => false]);
            
            $place = Place::find($checkIn->place_id);
            if ($place) {
                $decrementAmount = $checkIn->weight_applied ?? 1;
                
                if ($place->current_crowd_count >= $decrementAmount) {
                    $place->decrement('current_crowd_count', $decrementAmount);
                } else {
                    $place->update(['current_crowd_count' => 0]);
                }
                
                event(new CrowdUpdated($place->id, $place->current_crowd_count));
            }
            
            $this->info("Auto-checked out user ID: {$checkIn->user_id} from place ID: {$checkIn->place_id}");
        }

        $this->info('Auto-checkout process completed.');
    }
}
