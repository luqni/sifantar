<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Medicine;
use App\Models\Article;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $deliveries = Delivery::with(['patient', 'courier', 'items.medicine'])
            ->whereIn('status', ['pending', 'preparing', 'ready', 'awaiting_courier', 'delivering'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'pending' => Delivery::where('status', 'pending')->count(),
            'preparing' => Delivery::where('status', 'preparing')->count(),
            'ready' => Delivery::where('status', 'ready')->count(),
            'delivering' => Delivery::whereIn('status', ['awaiting_courier', 'delivering'])->count(),
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
        $delivery = Delivery::with(['patient', 'courier', 'items.medicine'])->findOrFail($id);
        $availableMedicines = Medicine::orderBy('name')->get();
        return view('pages.admin-delivery-detail', compact('delivery', 'availableMedicines'));
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
            $message = "Apoteker kami telah memverifikasi resep & sedang menyiapkan obat untuk pesanan #{$delivery->tracking_number}.";
        } elseif ($request->status === 'ready') {
            $title = "📦 Obat Selesai Disiapkan";
            $message = "Pesanan #{$delivery->tracking_number} telah selesai disiapkan oleh farmasi.";
        } elseif ($request->status === 'awaiting_courier') {
            $title = "🛵 Menunggu Kurir";
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

    public function addDeliveryItem(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);

        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price_at_time' => 'nullable|numeric|min:0',
        ]);

        $matchedMedicine = Medicine::whereRaw('LOWER(name) = ?', [strtolower(trim($request->medicine_name))])->first();

        $price = $request->filled('price_at_time') && $request->price_at_time !== null
            ? (float) $request->price_at_time
            : ($matchedMedicine ? (float) $matchedMedicine->price : 0);

        DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'medicine_id' => $matchedMedicine ? $matchedMedicine->id : null,
            'medicine_name' => trim($request->medicine_name),
            'quantity' => (int) $request->quantity,
            'price_at_time' => $price,
        ]);

        // Recalculate total_price
        $total = $delivery->items()->sum(DB::raw('quantity * price_at_time'));
        $delivery->update(['total_price' => $total]);

        return back()->with('success', 'Obat berhasil ditambahkan ke pesanan.');
    }

    public function deleteDeliveryItem($id, $itemId)
    {
        $delivery = Delivery::findOrFail($id);
        $item = DeliveryItem::where('delivery_id', $delivery->id)->findOrFail($itemId);
        $item->delete();

        // Recalculate total_price
        $total = $delivery->items()->sum(DB::raw('quantity * price_at_time'));
        $delivery->update(['total_price' => $total]);

        return back()->with('success', 'Obat berhasil dihapus dari pesanan.');
    }

    public function updateDeliveryTotalPrice(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        $request->validate([
            'total_price' => 'required|numeric|min:0',
        ]);

        $delivery->update(['total_price' => $request->total_price]);

        return back()->with('success', 'Total harga pesanan berhasil diperbarui.');
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

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('pages.admin-users', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('pages.admin-user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:patient,courier,admin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Reset to default password
        $user->update([
            'password' => Hash::make('password123')
        ]);

        return back()->with('success', 'Password user berhasil direset menjadi: password123');
    }

    /**
     * Kirim tautan reset kata sandi ke email user (dipicu admin).
     * Berguna saat user lupa sandi dan minta bantuan admin apotek.
     */
    public function sendResetLink($id)
    {
        $user = User::findOrFail($id);

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', "Tautan reset kata sandi sudah dikirim ke {$user->email}.");
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with('error', 'Tautan baru saja dikirim. Tunggu sekitar satu menit sebelum mengirim ulang.');
        }

        return back()->with('error', "Gagal mengirim tautan reset ke {$user->email}.");
    }

    public function storeOrderFromChat(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medicine_list' => 'required|string',
            'total_price' => 'nullable|numeric',
            'delivery_address' => 'required|string',
        ]);

        $delivery = Delivery::create([
            'patient_id' => $request->patient_id,
            'status' => 'preparing', // Langsung diproses karena admin yang buat
            'tracking_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            'delivery_address' => $request->delivery_address,
            'total_price' => $request->total_price ?? 0,
        ]);

        // Split the medicine list by new lines
        $medicines = explode("\n", str_replace("\r", "", $request->medicine_list));
        
        foreach ($medicines as $name) {
            $name = trim($name);
            if (!empty($name)) {
                \App\Models\DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'medicine_name' => $name,
                    'quantity' => 1,
                    'price_at_time' => 0,
                ]);
            }
        }

        // Send auto chat to patient
        Chat::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->patient_id,
            'message' => "Pesanan Anda telah kami buat dengan nomor pelacakan #{$delivery->tracking_number}. Obat sedang kami siapkan.",
        ]);

        return back()->with('success', 'Pesanan berhasil dibuat untuk pasien.');
    }
}
