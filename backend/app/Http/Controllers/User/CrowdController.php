<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\User\CrowdService;

class CrowdController extends Controller
{
    protected $crowdService;

    public function __construct(CrowdService $crowdService)
    {
        $this->crowdService = $crowdService;
    }

    public function checkIn(Request $request)
    {
        $request->validate(['place_id' => 'required|exists:places,id']);
        
        $result = $this->crowdService->checkIn($request->place_id, $request->user());
        
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 400);
        }

        return response()->json(['message' => 'Checked in successfully', 'current_crowd_count' => $result->current_crowd_count]);
    }

    public function checkOut(Request $request)
    {
        $request->validate(['place_id' => 'required|exists:places,id']);
        
        $result = $this->crowdService->checkOut($request->place_id, $request->user());

        return response()->json(['message' => 'Checked out successfully', 'current_crowd_count' => $result->current_crowd_count]);
    }
}
