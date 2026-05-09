<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::user()->isCourier()) {
            return redirect()->route('courier.dashboard');
        }

        $activeDeliveries = Delivery::with(['courier'])->where('patient_id', Auth::id())
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivering'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate ETA for the most recent active delivery
        $firstActive = $activeDeliveries->first();
        if ($firstActive && in_array($firstActive->status, ['pending', 'preparing', 'ready', 'awaiting_courier', 'delivering']) && $firstActive->latitude) {
            
            $startLat = config('app.pharmacy_lat', -6.2120);
            $startLng = config('app.pharmacy_lng', 106.8420);

            // If courier is already delivering, use courier's real-time location instead
            if ($firstActive->status === 'delivering' && $firstActive->courier && $firstActive->courier->latitude) {
                $startLat = $firstActive->courier->latitude;
                $startLng = $firstActive->courier->longitude;
            }

            $distance = $this->calculateDistance($startLat, $startLng, $firstActive->latitude, $firstActive->longitude);
            
            // Assume 30km/h average speed + 10 mins prep time if not yet delivering
            $prepTime = ($firstActive->status === 'delivering') ? 0 : 15;
            $minutes = round(($distance / 30) * 60) + $prepTime;
            
            if ($minutes < 5) $minutes = 5; // Minimum 5 mins
            
            $firstActive->update(['estimation_arrival' => now()->addMinutes($minutes)]);
        }

        $stats = [
            'active_count' => $activeDeliveries->count(),
            'history_count' => Delivery::where('patient_id', Auth::id())->where('status', 'completed')->count(),
        ];

        return view('pages.home', compact('activeDeliveries', 'stats'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function tracking($id)
    {
        $delivery = Delivery::with(['patient', 'courier'])->where('patient_id', Auth::id())->findOrFail($id);
        return view('pages.tracking', compact('delivery'));
    }

    public function trackingApi($id)
    {
        $delivery = Delivery::with(['courier'])->where('patient_id', Auth::id())->findOrFail($id);
        $courier = $delivery->courier;
        $eta = null;

        if ($courier && $courier->latitude && $delivery->latitude) {
            // Simple Haversine distance calculation (km)
            $earthRadius = 6371;
            $latDelta = deg2rad($delivery->latitude - $courier->latitude);
            $lonDelta = deg2rad($delivery->longitude - $courier->longitude);
            $a = sin($latDelta / 2) * sin($latDelta / 2) +
                 cos(deg2rad($courier->latitude)) * cos(deg2rad($delivery->latitude)) *
                 sin($lonDelta / 2) * sin($lonDelta / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;

            // Assume average speed 30km/h for courier in city
            $minutes = round(($distance / 30) * 60);
            if ($minutes < 1) $minutes = 1;
            
            $arrival = now()->addMinutes($minutes);
            $eta = $arrival->format('H:i');
            
            // Update the record for the dashboard
            $delivery->update(['estimation_arrival' => $arrival]);
        }
        
        return response()->json([
            'status' => $delivery->status,
            'eta' => $eta,
            'courier' => $courier ? [
                'lat' => $courier->latitude,
                'lng' => $courier->longitude,
            ] : null
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $query = Delivery::with(['items.medicine', 'patient']);

        if ($user->isCourier()) {
            $query->where('courier_id', $user->id);
        } else {
            $query->where('patient_id', $user->id);
        }

        $deliveries = $query->orderBy('created_at', 'desc')->get();

        return view('pages.history', compact('deliveries'));
    }

    public function show($id)
    {
        $delivery = Delivery::with(['items.medicine', 'courier'])->findOrFail($id);
        
        if ($delivery->patient_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.delivery-detail', compact('delivery'));
    }
}
