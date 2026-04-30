<?php
namespace App\Services\Admin;

use App\Models\Place;
use App\Models\Inquiry;

class AnalyticsService
{
    public function getDashboardStats()
    {
        return [
            'total_places' => Place::count(),
            'active_places' => Place::where('current_crowd_count', '>', 0)->count(),
            'total_inquiries' => Inquiry::count(),
            'unread_inquiries' => Inquiry::where('status', 'new')->count(),
            'top_places' => Place::orderByDesc('current_crowd_count')->take(5)->get(['id', 'name', 'current_crowd_count'])
        ];
    }

    public function getAnalyticsTrend()
    {
        // SQLite compatible hour extraction
        $peakHours = \App\Models\CheckIn::selectRaw("strftime('%H', created_at) as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // SQLite compatible date extraction
        $inquiryTrends = Inquiry::selectRaw("strftime('%Y-%m-%d', created_at) as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->reverse()
            ->values(); // Reset keys to ensure it's a JSON array

        return [
            'peak_hours' => $peakHours,
            'inquiry_trends' => $inquiryTrends,
            'stats' => $this->getDashboardStats()
        ];
    }
}
