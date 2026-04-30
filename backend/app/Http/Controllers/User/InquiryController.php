<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\InquiryService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
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

    public function index(Request $request)
    {
        return response()->json($this->inquiryService->getUserInquiries($request->user()->id));
    }
}
