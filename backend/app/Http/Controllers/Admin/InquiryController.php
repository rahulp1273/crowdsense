<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\InquiryService;
use Illuminate\Http\Request;
use App\Models\Admin;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    public function index(Request $request)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('read_inquiries')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to read inquiries.'], 403);
        }

        $filters = $request->only(['status', 'place_id']);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->inquiryService->getInquiries($filters, $perPage));
    }

    public function markSeen(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('read_inquiries')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($this->inquiryService->markSeen($id));
    }
}
