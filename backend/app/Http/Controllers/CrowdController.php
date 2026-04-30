<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\CheckIn;

class CrowdController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:places,id'
        ]);

        $place = Place::findOrFail($request->place_id);
        $place->increment('current_crowd_count');

        CheckIn::create([
            'place_id' => $place->id,
            'user_id' => $request->user()->id
        ]);

        return response()->json(['message' => 'Checked in successfully', 'current_crowd_count' => $place->current_crowd_count]);
    }
    
    public function checkOut(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:places,id'
        ]);

        $place = Place::findOrFail($request->place_id);
        if ($place->current_crowd_count > 0) {
            $place->decrement('current_crowd_count');
        }

        return response()->json(['message' => 'Checked out successfully', 'current_crowd_count' => $place->current_crowd_count]);
    }

    public function show($id)
    {
        $place = Place::findOrFail($id);
        return response()->json(['current_crowd_count' => $place->current_crowd_count]);
    }
}
