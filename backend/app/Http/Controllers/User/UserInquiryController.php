<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Admin\InquiryService;
use Illuminate\Http\Request;

class UserInquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        // Re-using the InquiryService
        $this->inquiryService = $inquiryService;
    }

    public function store(Request $request, $placeId)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'nullable|string|in:bug,general,support',
            'priority' => 'nullable|string|in:low,medium,high'
        ]);

        return response()->json($this->inquiryService->createInquiry($request->user()->id, $placeId, $validated), 201);
    }
}
