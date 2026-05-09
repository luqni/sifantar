<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            abort(404, 'Admin tidak ditemukan');
        }

        $messages = Chat::where(function($query) use ($admin) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $admin->id);
        })->orWhere(function($query) use ($admin) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        // Mark incoming messages as read
        Chat::where('sender_id', $admin->id)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => true]);

        return view('pages.chat', compact('messages', 'admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $sender_id = Auth::id();
        $receiver_id = $request->receiver_id;

        // Check if it's the first time chatting
        $exists = Chat::where(function($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);
        })->orWhere(function($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
        })->exists();

        // Save patient message
        Chat::create([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $request->message,
        ]);

        // Auto reply if first chat
        if (!$exists) {
            Chat::create([
                'sender_id' => $receiver_id,
                'receiver_id' => $sender_id,
                'message' => "selamat datang di sifantar, ada yang bisa kami bantu?",
            ]);
        }

        return back();
    }
}
