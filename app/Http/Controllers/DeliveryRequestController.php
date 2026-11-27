<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryRequestController extends Controller
{
    public function create(Request $request)
    {
        $repeat = null;

        if ($request->filled('from')) {
            $repeat = Delivery::where('id', $request->integer('from'))
                ->where('patient_id', Auth::id())
                ->first();
        }

        return view('pages.delivery-request', compact('repeat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:1000',
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
            'total_price' => 0,
        ]);

        // Auto-send chat to Admin / Tim Farmasi
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Chat::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'message' => "Halo Admin/Apoteker, saya baru saja membuat permintaan pengantaran obat baru (#{$delivery->tracking_number}) ke alamat: {$delivery->delivery_address}. Mohon dibantu verifikasi resep dan input daftar obatnya ya. Terima kasih!",
                'is_read' => false
            ]);
        }

        return redirect()->route('page', 'home')->with('success', 'Permintaan pengantaran berhasil dikirim! Apoteker kami akan memverifikasi resep dan menyiapkan obat Anda.');
    }
}
