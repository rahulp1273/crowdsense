<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PlaceService;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Admin;

class PlaceController extends Controller
{
    protected $placeService;
    protected $locationService;

    public function __construct(PlaceService $placeService, \App\Services\User\LocationService $locationService)
    {
        $this->placeService = $placeService;
        $this->locationService = $locationService;
    }

    public function lookupPincode(Request $request, $pincode)
    {
        $country = $request->get('country', 'IN');
        $details = $this->locationService->lookupPincode($pincode, $country);
        if (!$details) {
            return response()->json(['message' => 'Pincode not found or invalid for the selected country'], 404);
        }
        return response()->json($details);
    }

    public function index(Request $request)
    {
        // Permission check
        if (!($request->user() instanceof Admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            return response()->json($this->placeService->getAllPlaces());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to fetch places: " . $e->getMessage());
            return response()->json(['message' => 'Database error.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_places')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to manage places.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'radius' => 'nullable|integer',
            'max_capacity' => 'nullable|integer',
            'is_chat_enabled' => 'nullable|boolean',
            'pincode' => 'nullable|string',
            'address' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'street' => 'nullable|string',
            'mansion' => 'nullable|string'
        ]);

        return response()->json($this->placeService->createPlace($validated), 201);
    }

    public function update(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_places')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to manage places.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|string',
            'location' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'radius' => 'nullable|integer',
            'max_capacity' => 'nullable|integer',
            'is_chat_enabled' => 'nullable|boolean'
        ]);

        return response()->json($this->placeService->updatePlace($id, $validated));
    }

    public function destroy(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_places')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to manage places.'], 403);
        }

        $this->placeService->deletePlace($id);
        return response()->json(['message' => 'Place deleted']);
    }
}
