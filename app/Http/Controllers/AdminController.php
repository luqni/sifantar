<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Article;
use App\Models\Chat;
use App\Models\Notification;
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

        // Create Notification for Patient
        $title = "Update Pesanan";
        $message = "Status pesanan #{$delivery->tracking_number} telah diperbarui.";
        
        if ($request->status === 'preparing') {
            $title = "💊 Obat Sedang Disiapkan";
            $message = "Apoteker kami sedang menyiapkan obat untuk pesanan #{$delivery->tracking_number}.";
        } elseif ($request->status === 'ready' || $request->status === 'awaiting_courier') {
            $title = "📦 Obat Siap Dikirim";
            $message = "Pesanan #{$delivery->tracking_number} sudah siap dan sedang menunggu kurir penjemput.";
        }

        Notification::create([
            'user_id' => $delivery->patient_id,
            'title' => $title,
            'message' => $message,
            'type' => 'delivery'
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required',
            'image' => 'nullable|url'
        ]);

        Article::create([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . time(),
            'category' => $request->category,
            'content' => $request->content,
            'image' => $request->image ?? 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=500&h=300&fit=crop',
            'author_id' => auth()->id()
        ]);

        return back()->with('success', 'Artikel berhasil diterbitkan.');
    }

    public function deleteArticle($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        return view('pages.admin-article-edit', compact('article'));
    }

    public function updateArticle(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required',
            'image' => 'nullable|url'
        ]);

        $article = Article::findOrFail($id);
        $article->update([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . time(),
            'category' => $request->category,
            'content' => $request->content,
            'image' => $request->image ?? $article->image
        ]);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil diperbarui.');
    }
}
