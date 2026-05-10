<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    public function dashboard()
    {
        // Available jobs for all couriers
        $availableJobs = Delivery::with(['patient', 'items'])
            ->where('status', 'awaiting_courier')
            ->orderBy('created_at', 'desc')
            ->get();

        // Jobs currently being handled by this courier
        $myJobs = Delivery::with(['patient', 'items'])
            ->where('courier_id', Auth::id())
            ->where('status', 'delivering')
            ->get();

        return view('pages.courier-dashboard', compact('availableJobs', 'myJobs'));
    }

    public function acceptJob($id)
    {
        $delivery = Delivery::findOrFail($id);
        
        if ($delivery->status !== 'awaiting_courier') {
            return back()->with('error', 'Pesanan sudah diambil oleh kurir lain.');
        }

        $delivery->update([
            'courier_id' => Auth::id(),
            'status' => 'delivering'
        ]);

        Notification::create([
            'user_id' => $delivery->patient_id,
            'title' => "🚚 Obat Sedang Dikirim",
            'message' => "Pesanan obat #{$delivery->tracking_number} sedang dalam perjalanan menuju alamat Anda.",
            'type' => 'delivery'
        ]);

        return back()->with('success', 'Pesanan berhasil Anda ambil. Selamat bertugas!');
    }

    public function completeJob($id)
    {
        $delivery = Delivery::where('id', $id)
            ->where('courier_id', Auth::id())
            ->firstOrFail();

        $delivery->update(['status' => 'completed']);

        Notification::create([
            'user_id' => $delivery->patient_id,
            'title' => "✅ Obat Telah Sampai",
            'message' => "Pesanan #{$delivery->tracking_number} telah sampai di tujuan. Silakan konfirmasi jika sudah diterima.",
            'type' => 'success'
        ]);

        return back()->with('success', 'Pesanan telah berhasil diantarkan!');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Auth::user()->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['status' => 'success']);
    }
}
