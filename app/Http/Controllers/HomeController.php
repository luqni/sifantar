<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Article;
use App\Models\Notification;
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
            ->whereIn('status', ['pending', 'preparing', 'ready', 'awaiting_courier', 'delivering'])
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
        $query = Delivery::with(['patient', 'courier', 'items.medicine']);

        if (Auth::user()->isAdmin()) {
            // Admin dapat melacak seluruh pengantaran
        } elseif (Auth::user()->isCourier()) {
            // Kurir dapat melihat tugas miliknya atau yang siap diambil
            $query->where(function ($q) {
                $q->where('courier_id', Auth::id())
                  ->orWhere('status', 'awaiting_courier');
            });
        } else {
            // Pasien hanya dapat melihat pengantarannya sendiri
            $query->where('patient_id', Auth::id());
        }

        $delivery = $query->findOrFail($id);
        return view('pages.tracking', compact('delivery'));
    }

    public function trackingApi($id)
    {
        $query = Delivery::with(['patient', 'courier']);

        if (Auth::user()->isAdmin()) {
            // Admin dapat mengakses data live tracking
        } elseif (Auth::user()->isCourier()) {
            $query->where(function ($q) {
                $q->where('courier_id', Auth::id())
                  ->orWhere('status', 'awaiting_courier');
            });
        } else {
            $query->where('patient_id', Auth::id());
        }

        $delivery = $query->findOrFail($id);
        $courier = $delivery->courier;
        $eta = null;
        $distanceFormatted = null;

        // Hitung jarak dan estimasi waktu tiba (ETA)
        $destLat = $delivery->latitude;
        $destLng = $delivery->longitude;

        if ($destLat && $destLng) {
            $startLat = config('app.pharmacy_lat', -6.2120);
            $startLng = config('app.pharmacy_lng', 106.8420);

            if ($delivery->status === 'delivering' && $courier && $courier->latitude) {
                $startLat = (float) $courier->latitude;
                $startLng = (float) $courier->longitude;
            }

            $distance = $this->calculateDistance($startLat, $startLng, (float)$destLat, (float)$destLng);
            $distanceFormatted = number_format($distance, 1) . ' km';

            $prepTime = ($delivery->status === 'delivering') ? 0 : 15;
            $minutes = round(($distance / 30) * 60) + $prepTime;
            if ($minutes < 5) $minutes = 5;

            $arrival = now()->addMinutes($minutes);
            $eta = $arrival->format('H:i');

            // Simpan estimasi kedatangan terbaru
            $delivery->update(['estimation_arrival' => $arrival]);
        } elseif ($delivery->estimation_arrival) {
            $eta = $delivery->estimation_arrival->format('H:i');
        }

        return response()->json([
            'status' => $delivery->status,
            'eta' => $eta,
            'distance' => $distanceFormatted,
            'courier' => $courier ? [
                'name' => $courier->name,
                'phone' => $courier->phone,
                'lat' => $courier->latitude ? (float) $courier->latitude : null,
                'lng' => $courier->longitude ? (float) $courier->longitude : null,
            ] : null,
            'delivery' => [
                'lat' => $delivery->latitude ? (float) $delivery->latitude : null,
                'lng' => $delivery->longitude ? (float) $delivery->longitude : null,
                'address' => $delivery->delivery_address,
            ]
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

    public function articles()
    {
        $articles = Article::orderBy('created_at', 'desc')->get();
        return view('pages.articles', compact('articles'));
    }

    public function articleDetail($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return view('pages.article-detail', compact('article'));
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('pages.notifications', compact('notifications'));
    }
}
