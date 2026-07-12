<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            abort(404, 'Admin tidak ditemukan');
        }

        $partner_id = Auth::id();
        
        if (Auth::user()->role === 'admin') {
            $partner_id = $request->query('user_id');
            if (!$partner_id) {
                return redirect()->route('admin.chat')->with('error', 'Pilih pasien terlebih dahulu.');
            }
        }

        $messages = Chat::where(function($query) use ($admin, $partner_id) {
            $query->where('sender_id', $partner_id)
                  ->where('receiver_id', $admin->id);
        })->orWhere(function($query) use ($admin, $partner_id) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $partner_id);
        })->orderBy('created_at', 'asc')->get();

        // Mark incoming messages as read
        Chat::where('sender_id', Auth::user()->role === 'admin' ? $partner_id : $admin->id)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => true]);

        $partner = User::find($partner_id);

        return view('pages.chat', compact('messages', 'admin', 'partner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'receiver_id' => 'required|exists:users,id'
        ]);

        if (!$request->message && !$request->hasFile('image')) {
            return back();
        }

        $sender_id = Auth::id();
        $receiver_id = $request->receiver_id;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

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
            'image_path' => $imagePath,
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
