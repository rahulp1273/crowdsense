<?php
namespace App\Services\User;

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

        if (\Illuminate\Support\Facades\Schema::hasColumn('inquiries', 'priority')) {
            $inquiry->update(['priority' => $data['priority'] ?? 'low']);
        }

        event(new \App\Events\InquiryCreated($inquiry));
        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');

        return $inquiry;
    }

    public function getUserInquiries($userId)
    {
        return Inquiry::with('place:id,name')->where('user_id', $userId)->latest()->get();
    }
}
