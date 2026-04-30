<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Admin;

class PlaceController extends Controller
{
    public function index()
    {
        return response()->json(Place::all());
    }

    public function store(Request $request)
    {
        if (!($request->user() instanceof Admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
        ]);

        $place = Place::create($request->all());
        return response()->json($place, 201);
    }

    public function update(Request $request, $id)
    {
        if (!($request->user() instanceof Admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $place = Place::findOrFail($id);
        $place->update($request->all());
        return response()->json($place);
    }

    public function destroy(Request $request, $id)
    {
        if (!($request->user() instanceof Admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $place = Place::findOrFail($id);
        $place->delete();
        return response()->json(['message' => 'Place deleted']);
    }
}
