<?php
namespace App\Services\Admin;

use App\Models\Place;
use App\Models\Inquiry;

class AnalyticsService
{
    public function getDashboardStats()
    {
        return \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', now()->addMinutes(10), function () {
            return [
                'total_places' => Place::count(),
                'active_places' => Place::where('current_crowd_count', '>', 0)->count(),
                'total_inquiries' => Inquiry::count(),
                'unread_inquiries' => Inquiry::where('status', 'new')->count(),
                'top_places' => Place::orderByDesc('current_crowd_count')->take(5)->get(['id', 'name', 'current_crowd_count'])
            ];
        });
    }
}
