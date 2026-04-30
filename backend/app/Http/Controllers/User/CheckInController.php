<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\CheckInService;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    protected $checkInService;

    public function __construct(CheckInService $checkInService)
    {
        $this->checkInService = $checkInService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:places,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        try {
            $checkIn = $this->checkInService->checkIn(
                $request->user(), 
                $request->place_id, 
                $request->lat, 
                $request->lng
            );
            return response()->json(['message' => 'Checked in successfully', 'check_in' => $checkIn], 201);
        } catch (\Exception $e) {
            $activeCheckIn = $this->checkInService->getActiveCheckIn($request->user()->id);
            $activePlace = $activeCheckIn ? \App\Models\Place::find($activeCheckIn->place_id) : null;
            
            return response()->json([
                'error' => $e->getMessage(),
                'details' => property_exists($e, 'details') ? $e->details : null,
                'active_place' => $activePlace
            ], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $checkOut = $this->checkInService->checkOut($request->user());
            return response()->json(['message' => 'Checked out successfully', 'check_out' => $checkOut]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function active(Request $request)
    {
        $activeCheckIn = $this->checkInService->getActiveCheckIn($request->user()->id);
        return response()->json($activeCheckIn);
    }
}
