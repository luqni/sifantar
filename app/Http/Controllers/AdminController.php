<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Article;
use App\Models\Chat;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $deliveries = Delivery::with(['patient', 'items.medicine'])
            ->whereIn('status', ['pending', 'ready', 'awaiting_courier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'pending' => Delivery::where('status', 'pending')->count(),
            'preparing' => Delivery::where('status', 'preparing')->count(),
            'ready' => Delivery::where('status', 'ready')->count(),
            'delivering' => Delivery::where('status', 'awaiting_courier')->count(),
        ];

        return view('pages.admin-dashboard', compact('deliveries', 'stats'));
    }

    public function articles()
    {
        $articles = Article::with('author')->orderBy('created_at', 'desc')->get();
        return view('pages.admin-articles', compact('articles'));
    }

    public function chat()
    {
        $chats = Chat::with('sender')
            ->where('receiver_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('pages.admin-chat', compact('chats'));
    }

    public function showDelivery($id)
    {
        $delivery = Delivery::with(['patient', 'items.medicine'])->findOrFail($id);
        return view('pages.admin-delivery-detail', compact('delivery'));
    }

    public function updateDeliveryStatus(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
