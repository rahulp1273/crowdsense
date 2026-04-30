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

    public function getInquiries($filters = [], $perPage = 15)
    {
        $query = Inquiry::with(['user:id,name', 'place:id,name'])->latest();

        if (!empty($filters['place_id'])) {
            $query->where('place_id', $filters['place_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function markSeen($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $inquiry->update(['status' => 'seen']);
        return $inquiry;
    }
}
