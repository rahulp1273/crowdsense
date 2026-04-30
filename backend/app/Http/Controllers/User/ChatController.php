<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($placeId)
    {
        return response()->json(ChatMessage::with('user:id,name')->where('place_id', $placeId)->latest()->take(50)->get());
    }

    public function store(Request $request, $placeId)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $message = ChatMessage::create([
            'place_id' => $placeId,
            'user_id' => $request->user()->id,
            'message' => $request->message
        ]);

        $message->load('user:id,name');

        event(new MessageSent($message));

        return response()->json($message, 201);
    }
}
