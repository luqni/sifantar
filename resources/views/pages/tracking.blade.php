@extends('layouts.app')
@section('title', 'Tracking Pesanan #' . $delivery->tracking_number . ' - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center gap-3 border-b border-gray-50">
    <a href="{{ route('home') }}" class="p-1">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <h1 class="text-sm font-black text-gray-800 uppercase tracking-widest">Lacak #{{ $delivery->tracking_number }}</h1>
</header>

<div class="flex-1 min-h-[400px] bg-slate-100 relative overflow-hidden" id="map" style="z-index: 10;">
    @if(in_array($delivery->status, ['ready', 'awaiting_courier']))
    <div class="absolute inset-0 z-20 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center text-white text-center px-6">
        <div class="relative mb-6">
            <div class="w-20 h-20 border-4 border-primary-green/30 rounded-full animate-ping absolute inset-0"></div>
            <div class="w-20 h-20 bg-primary-green rounded-full flex items-center justify-center relative z-10 shadow-lg shadow-green-500/20">
                <i data-lucide="search" class="w-10 h-10 animate-bounce text-white"></i>
            </div>
        </div>
        <h3 class="text-xl font-black uppercase tracking-widest mb-2">Sedang Mencari Kurir</h3>
        <p class="text-xs opacity-70 font-medium max-w-[200px]">Mohon tunggu sebentar, kami sedang menghubungkan dengan kurir terdekat...</p>
    </div>
    @endif
</div>

<!-- Tracking Info Card -->
<div class="bg-white rounded-t-[48px] px-8 pt-8 pb-12 shadow-[0_-20px_50px_-15px_rgba(0,0,0,0.15)] relative z-30 -mt-12 border-t border-gray-50 flex flex-col">
     <div class="w-14 h-1.5 bg-gray-100 rounded-full mx-auto mb-8"></div>
     <h2 class="text-xl font-black text-gray-800 mb-8 tracking-tight flex items-center gap-3">
         <i data-lucide="activity" class="text-primary-green w-6 h-6"></i>
         Status Pengiriman
     </h2>

     <div class="space-y-8 relative">
        <div class="absolute left-[11px] top-4 bottom-4 w-0.5 bg-gray-100"></div>
        
        <!-- Delivering -->
        <div id="step-delivering" class="flex items-start gap-6 relative {{ in_array($delivery->status, ['delivering', 'completed']) ? '' : 'opacity-30' }}">
            <div id="icon-delivering" class="w-6 h-6 rounded-full border-4 border-white flex items-center justify-center shadow-lg relative z-10 {{ $delivery->status === 'delivering' ? 'bg-primary-green scale-110 ring-2 ring-green-100' : ($delivery->status === 'completed' ? 'bg-black' : 'bg-gray-300') }}">
                @if($delivery->status === 'completed')
                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                @else
                    <div class="w-1.5 h-1.5 rounded-full bg-white {{ $delivery->status === 'delivering' ? 'animate-pulse' : '' }}"></div>
                @endif
            </div>
            <div class="flex-1 -mt-0.5">
                <h4 id="title-delivering" class="font-black text-sm uppercase tracking-widest text-gray-800">{{ $delivery->status === 'completed' ? 'Sudah Diterima' : 'Sedang Diantar' }}</h4>
                <p id="text-delivering" class="text-[10px] text-gray-400 font-bold leading-tight mt-1">
                    @if($delivery->status === 'delivering')
                        Kurir sedang menuju lokasi Anda.
                    @elseif($delivery->status === 'completed')
                        Obat telah diterima.
                    @else
                        Sedang mencari kurir...
                    @endif
                </p>
            </div>
        </div>

        <!-- Awaiting Courier / Ready -->
        <div id="step-ready" class="flex items-start gap-6 relative {{ in_array($delivery->status, ['ready', 'awaiting_courier', 'delivering', 'completed']) ? '' : 'opacity-30' }}">
            <div id="icon-ready" class="w-6 h-6 rounded-full border-4 border-white flex items-center justify-center shadow-lg relative z-10 {{ in_array($delivery->status, ['ready', 'awaiting_courier']) ? 'bg-primary-orange scale-110 ring-2 ring-orange-100' : (in_array($delivery->status, ['delivering', 'completed']) ? 'bg-black' : 'bg-gray-300') }}">
                 @if(in_array($delivery->status, ['delivering', 'completed']))
                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                 @else
                    <div class="w-1.5 h-1.5 rounded-full bg-white {{ in_array($delivery->status, ['ready', 'awaiting_courier']) ? 'animate-pulse' : '' }}"></div>
                 @endif
            </div>
            <div class="flex-1 -mt-0.5">
                <h4 class="font-black text-sm uppercase tracking-widest text-gray-800">Paket Siap</h4>
                <p class="text-[10px] text-gray-400 font-bold leading-tight mt-1">Obat selesai disiapkan oleh Farmasi.</p>
            </div>
        </div>
        
        <!-- Pending / Preparing -->
        <div id="step-pending" class="flex items-start gap-6 relative">
            <div id="icon-pending" class="w-6 h-6 rounded-full border-4 border-white flex items-center justify-center shadow-lg relative z-10 {{ in_array($delivery->status, ['pending', 'preparing']) ? 'bg-blue-500 scale-110 ring-2 ring-blue-100' : 'bg-black' }}">
                @if(!in_array($delivery->status, ['pending', 'preparing']))
                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                @else
                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                @endif
            </div>
            <div class="flex-1 -mt-0.5">
                <h4 class="font-black text-sm uppercase tracking-widest text-gray-800">Pesanan Diproses</h4>
                <p class="text-[10px] text-gray-400 font-bold leading-tight mt-1">Admin sedang memverifikasi resep Anda.</p>
            </div>
        </div>
     </div>

     <!-- Courier Profile -->
     @if($delivery->courier)
     <div class="mt-12 pt-8 border-t border-gray-100 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-white shadow-xl shadow-gray-100 rotate-6 shrink-0 bg-blue-50 flex items-center justify-center text-blue-500">
            <i data-lucide="user" class="w-8 h-8"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-black text-gray-800 text-base leading-tight">{{ $delivery->courier->name }}</h4>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.1em]">Kurir Terverifikasi</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="tel:08123456789" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-800 active:scale-90 transition-all hover:bg-primary-green hover:text-white border border-gray-100">
                <i data-lucide="phone" class="w-6 h-6"></i>
            </a>
        </div>
     </div>
     @endif

     <!-- Success Confirmation (Hidden by default) -->
     <div id="success-confirmation" class="{{ $delivery->status === 'completed' ? '' : 'hidden' }} mt-8 p-6 bg-green-50 rounded-[32px] border-2 border-green-100 flex flex-col items-center text-center animate-in zoom-in duration-500">
        <div class="w-16 h-16 bg-primary-green rounded-full flex items-center justify-center text-white shadow-lg shadow-green-200 mb-4">
            <i data-lucide="check-circle" class="w-10 h-10"></i>
        </div>
        <h3 class="font-black text-gray-800 text-lg mb-1">Obat Telah Diterima!</h3>
        <p class="text-[11px] text-gray-500 font-bold mb-6">Terima kasih telah menggunakan SIFANTAR. Semoga lekas sembuh!</p>
        <a href="{{ route('home') }}" class="w-full bg-white text-primary-green py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest border-2 border-primary-green active:scale-95 transition-all">Kembali ke Beranda</a>
     </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple map simulation
        var map = L.map('map', {
            zoom: 13,
            zoomControl: false,
            attributionControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Pharmacy location (Dynamic from backend or fallback to Jakarta)
        var pharmacyPos = [{{ config('app.pharmacy_lat', -6.2120) }}, {{ config('app.pharmacy_lng', 106.8420) }}];
        map.setView(pharmacyPos, 13);
        
        var pharmacyIcon = L.divIcon({
            html: '<div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-white border-4 border-white shadow-lg"><i data-lucide="factory" class="w-5 h-5"></i></div>',
            className: 'custom-div-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });
        L.marker(pharmacyPos, {icon: pharmacyIcon}).addTo(map);

        // Patient location
        var patientIcon = L.divIcon({
            html: '<div class="w-10 h-10 bg-black rounded-full flex items-center justify-center text-white border-4 border-white shadow-lg"><i data-lucide="map-pin" class="w-5 h-5"></i></div>',
            className: 'custom-div-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        var patientMarker = null;

        function updateMapView(pos) {
            console.log("Updating map view to:", pos);
            if (patientMarker) {
                patientMarker.setLatLng(pos);
            } else {
                patientMarker = L.marker(pos, {icon: patientIcon}).addTo(map);
            }
            
            // Adjust view
            var group = new L.featureGroup([L.marker(pharmacyPos), L.marker(pos)]);
            map.fitBounds(group.getBounds().pad(0.3));
            lucide.createIcons();
        }

        function geocodeAddress(address) {
            console.log("Geocoding address:", address);
            @if($delivery->latitude && $delivery->longitude)
                console.log("Using database coordinates:", [{{ $delivery->latitude }}, {{ $delivery->longitude }}]);
                updateMapView([{{ $delivery->latitude }}, {{ $delivery->longitude }}]);
            @else
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            console.log("Geocoding success:", data[0]);
                            updateMapView([data[0].lat, data[0].lon]);
                        } else {
                            console.warn("Geocoding failed for address:", address);
                        }
                    })
                    .catch(error => console.error("Geocoding error:", error));
            @endif
            
            // Fix map size issues
            setTimeout(() => { map.invalidateSize(); }, 500);
        }

        geocodeAddress("{{ $delivery->delivery_address }}");

        // Courier marker variable
        var courierMarker = null;

        function updateTracking() {
            fetch("{{ route('tracking.api', $delivery->id) }}")
                .then(response => response.json())
                .then(data => {
                    console.log("Live Tracking Data:", data);
                    
                    // Update status overlay visibility
                    const overlay = document.querySelector('.bg-black\\/60');
                    if (data.status === 'delivering' && overlay) {
                        overlay.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => overlay.remove(), 500);
                    }

                    // Update UI if Completed
                    if (data.status === 'completed') {
                        document.getElementById('step-delivering').classList.remove('opacity-30');
                        document.getElementById('icon-delivering').className = 'w-6 h-6 rounded-full border-4 border-white flex items-center justify-center shadow-lg relative z-10 bg-black';
                        document.getElementById('icon-delivering').innerHTML = '<i data-lucide="check" class="w-3 h-3 text-white"></i>';
                        document.getElementById('title-delivering').innerText = 'Sudah Diterima';
                        document.getElementById('text-delivering').innerText = 'Obat telah diterima.';
                        
                        // Hide courier marker when done
                        if (courierMarker) courierMarker.remove();
                        
                        // Show success confirmation card
                        const successCard = document.getElementById('success-confirmation');
                        if (successCard) successCard.classList.remove('hidden');
                        
                        lucide.createIcons();
                        console.log("Delivery Completed!");
                    }

                    // Update courier location on map
                    if (data.status === 'delivering' && data.courier && data.courier.lat && data.courier.lng) {
                        var courierPos = [parseFloat(data.courier.lat), parseFloat(data.courier.lng)];
                        console.log("Updating Courier Marker to:", courierPos);
                        
                        if (courierMarker) {
                            courierMarker.setLatLng(courierPos);
                        } else {
                            var courierIcon = L.divIcon({
                                html: '<div class="relative"><div class="absolute inset-0 bg-primary-green opacity-20 rounded-full animate-ping scale-150"></div><div class="w-12 h-12 bg-primary-green rounded-2xl flex items-center justify-center text-white border-4 border-white shadow-xl rotate-12 transition-all duration-1000"><i data-lucide="truck" class="w-6 h-6"></i></div></div>',
                                className: 'custom-div-icon',
                                iconSize: [48, 48],
                                iconAnchor: [24, 24]
                            });
                            courierMarker = L.marker(courierPos, {icon: courierIcon}).addTo(map);
                            lucide.createIcons();
                        }
                        
                        // Optionally follow the courier if they move out of view
                        if (!map.getBounds().contains(courierPos)) {
                            map.panTo(courierPos);
                        }
                    }
                })
                .catch(error => console.error("Error fetching live tracking:", error));
        }

        // Poll every 5 seconds for smoother tracking
        updateTracking();
        setInterval(updateTracking, 5000);

        // Initial courier location if already available
        @if($delivery->status === 'delivering' && $delivery->courier && $delivery->courier->latitude)
        var initialPos = [{{ $delivery->courier->latitude }}, {{ $delivery->courier->longitude }}];
        var courierIcon = L.divIcon({
            html: '<div class="relative"><div class="absolute inset-0 bg-primary-green opacity-20 rounded-full animate-ping scale-150"></div><div class="w-12 h-12 bg-primary-green rounded-2xl flex items-center justify-center text-white border-4 border-white shadow-xl rotate-12"><i data-lucide="truck" class="w-6 h-6"></i></div></div>',
            className: 'custom-div-icon',
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });
        courierMarker = L.marker(initialPos, {icon: courierIcon}).addTo(map);
        @endif

        // Re-initialize Lucide icons
        lucide.createIcons();
    });
</script>

<style>
    .leaflet-tile {
        filter: grayscale(100%) invert(100%) contrast(90%);
    }
</style>
@endsection
