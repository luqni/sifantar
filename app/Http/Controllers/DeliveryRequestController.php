<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryRequestController extends Controller
{
    public function create(Request $request)
    {
        // Prefill "Beli Lagi": bawa alamat dari pesanan lama bila ada order_id yang valid
        $repeat = null;
        $medicines = '';

        if ($request->filled('from')) {
            $repeat = Delivery::with('items')
                ->where('id', $request->integer('from'))
                ->where('patient_id', Auth::id())
                ->first();

            if ($repeat) {
                $medicines = $repeat->items
                    ->map(fn ($item) => $item->medicine ? $item->medicine->name : $item->medicine_name)
                    ->filter()
                    ->implode("\n");
            }
        }

        return view('pages.delivery-request', compact('repeat', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_list' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $delivery = Delivery::create([
            'patient_id' => Auth::id(),
            'status' => 'pending',
            'tracking_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            'delivery_address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'total_price' => 0, // Will be updated by admin later
        ]);

        // Split the medicine list by new lines
        $medicines = explode("\n", str_replace("\r", "", $request->medicine_list));
        $medSummary = [];
        
        foreach ($medicines as $name) {
            $name = trim($name);
            if (!empty($name)) {
                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'medicine_name' => $name,
                    'quantity' => 1,
                    'price_at_time' => 0,
                ]);
                $medSummary[] = $name;
            }
        }

        // Auto-send chat to Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $summaryText = implode(", ", $medSummary);
            Chat::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'message' => "Halo Admin, saya baru saja membuat pesanan baru (#{$delivery->tracking_number}) dengan rincian obat: {$summaryText}. Mohon segera diproses ya. Terima kasih!",
                'is_read' => false
            ]);
        }

        return redirect()->route('page', 'home')->with('success', 'Permintaan obat berhasil dikirim! Silakan tunggu admin memprosesnya.');
    }
}
