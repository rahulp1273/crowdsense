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

        $query = \App\Models\Inquiry::with(['user:id,name', 'place:id,name'])->latest();

        if ($request->filled('place_id')) {
            $query->where('place_id', $request->place_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function markSeen(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('read_inquiries')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($this->inquiryService->markSeen($id));
    }
}
