<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\PlaceService;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    protected $placeService;

    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    public function index(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        return response()->json($this->placeService->listPlacesWithDistance($lat, $lng));
    }

    public function showCrowd($id)
    {
        return response()->json($this->placeService->getCrowdStatus($id));
    }
}
