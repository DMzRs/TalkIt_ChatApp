<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id'         => $message->id,
            'content'    => $message->content,
            'created_at' => $message->created_at->toISOString(),
            'user'       => [
                'id'   => $message->user->id,
                'name' => $message->user->name,
            ],
        ]);
    }
}
