<?php
namespace App\Services\Admin;

use App\Models\Inquiry;

class InquiryService
{
    public function createInquiry($userId, $placeId, $data)
    {
        $inquiry = Inquiry::create([
            'user_id' => $userId,
            'place_id' => $placeId,
            'message' => $data['message'],
            'type' => $data['type'] ?? 'general',
            'status' => 'new'
        ]);
        
        // If the DB has priority column, save it. Otherwise ignore to prevent crash if not migrated yet.
        if (\Illuminate\Support\Facades\Schema::hasColumn('inquiries', 'priority')) {
            $inquiry->update(['priority' => $data['priority'] ?? 'low']);
        }

        event(new \App\Events\InquiryCreated($inquiry));
        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');

        return $inquiry;
    }

    public function getAllInquiries($perPage = 15)
    {
        return Inquiry::with(['user:id,name', 'place:id,name'])->latest()->paginate($perPage);
    }

    public function filterByPlace($placeId)
    {
        return Inquiry::with(['user:id,name', 'place:id,name'])->where('place_id', $placeId)->latest()->get();
    }

    public function markSeen($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $inquiry->update(['status' => 'seen']);
        return $inquiry;
    }
}
