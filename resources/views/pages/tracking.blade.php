@extends('layouts.app')
@section('title', 'Live Tracking #' . $delivery->tracking_number . ' - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Sticky Header -->
<header class="sticky top-0 bg-white/90 backdrop-blur-md z-40 px-5 py-4 flex items-center justify-between border-b border-gray-100 shadow-sm">
    <div class="flex items-center gap-3">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isCourier() ? route('courier.dashboard') : route('home')) }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-xs font-black text-gray-800 uppercase tracking-widest">Live Tracking</h1>
            <p class="text-[10px] text-gray-400 font-bold">#{{ $delivery->tracking_number }}</p>
        </div>
    </div>

    <!-- Live Status Pill -->
    <div id="status-header-badge">
        @if($delivery->status === 'pending')
            <span class="bg-orange-100 text-primary-orange text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Menunggu</span>
        @elseif($delivery->status === 'preparing')
            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Disiapkan</span>
        @elseif($delivery->status === 'ready')
            <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Paket Siap</span>
        @elseif($delivery->status === 'awaiting_courier')
            <span class="bg-purple-100 text-purple-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Cari Kurir</span>
        @elseif($delivery->status === 'delivering')
            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span> Diantar
            </span>
        @elseif($delivery->status === 'completed')
            <span class="bg-green-100 text-green-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Selesai</span>
        @else
            <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">{{ strtoupper($delivery->status) }}</span>
        @endif
    </div>
</header>

<!-- Map Container -->
<div class="relative w-full h-[360px] bg-slate-100 overflow-hidden" id="map-wrapper" style="z-index: 10;">
    <div id="map" class="w-full h-full"></div>

    <!-- Floating status chip over map when preparing/pending -->
    <div id="prep-overlay-chip" class="{{ in_array($delivery->status, ['pending', 'preparing']) ? '' : 'hidden' }} absolute top-4 left-4 right-4 z-20 pointer-events-none">
        <div class="bg-white/95 backdrop-blur-md border border-amber-200/80 shadow-lg rounded-2xl p-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <i data-lucide="package-search" class="w-4 h-4 animate-bounce"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-amber-800 uppercase tracking-wider">Farmasi Sedang Menyiapkan Obat</p>
                <p class="text-[11px] text-gray-600 font-medium">Apoteker memeriksa resep & meracik pesanan.</p>
            </div>
        </div>
    </div>

    <!-- Searching Courier Overlay -->
    <div id="searching-overlay" class="{{ in_array($delivery->status, ['ready', 'awaiting_courier']) ? '' : 'hidden' }} absolute inset-0 z-20 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center text-white text-center px-6 transition-opacity duration-500">
        <div class="relative mb-5">
            <div class="w-20 h-20 border-4 border-primary-green/40 rounded-full animate-ping absolute inset-0"></div>
            <div class="w-20 h-20 bg-primary-green rounded-full flex items-center justify-center relative z-10 shadow-xl shadow-green-500/30">
                <i data-lucide="search" class="w-9 h-9 animate-bounce text-white"></i>
            </div>
        </div>
        <h3 class="text-lg font-black uppercase tracking-widest mb-1.5">Sedang Menghubungkan Kurir</h3>
        <p class="text-xs text-white/80 font-medium max-w-[240px] leading-relaxed">
            Obat telah siap di apotek. Kami sedang mencari kurir terdekat untuk menjemput pesanan Anda.
        </p>
    </div>
</div>

<!-- Tracking Info Card Sheet -->
<div class="bg-white rounded-t-[36px] px-6 pt-6 pb-28 shadow-[0_-15px_40px_-10px_rgba(0,0,0,0.1)] relative z-30 -mt-8 border-t border-gray-100 flex flex-col">
    <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto mb-6"></div>

    <!-- ETA & Live Distance Banner -->
    <div id="eta-banner" class="flex items-center justify-between bg-primary-green/10 border border-primary-green/20 rounded-2xl p-4 mb-6 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-primary-green text-white flex items-center justify-center shrink-0 shadow-md shadow-green-200">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Perkiraan Waktu Tiba</p>
                <p id="eta-text" class="text-base font-black text-gray-800">
                    {{ $delivery->estimation_arrival ? $delivery->estimation_arrival->format('H:i') . ' WIB' : 'Sedang dihitung...' }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-[9px] font-black uppercase text-gray-400 block tracking-wider">Jarak Tempuh</span>
            <span id="distance-text" class="text-xs font-black text-primary-green">-</span>
        </div>
    </div>

    <!-- Tracking Timeline (Top-to-Bottom Sequential) -->
    <div class="mb-6">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-primary-green"></i>
            Status Pengiriman
        </h3>

        <div class="space-y-6 relative pl-2">
            <!-- Connecting vertical line -->
            <div class="absolute left-[21px] top-4 bottom-4 w-0.5 bg-gray-100"></div>

            <!-- Step 1: Penyiapan Obat -->
            <div id="step-prep" class="flex items-start gap-4 relative">
                <div id="icon-prep" class="w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 {{ in_array($delivery->status, ['ready', 'awaiting_courier', 'delivering', 'completed']) ? 'bg-primary-green text-white' : 'bg-primary-orange text-white scale-110 ring-2 ring-orange-100' }}">
                    @if(in_array($delivery->status, ['ready', 'awaiting_courier', 'delivering', 'completed']))
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                    @endif
                </div>
                <div class="flex-1 -mt-0.5">
                    <h4 id="title-prep" class="font-black text-xs uppercase tracking-wider {{ in_array($delivery->status, ['ready', 'awaiting_courier', 'delivering', 'completed']) ? 'text-gray-800' : 'text-primary-orange' }}">
                        1. Resep & Obat Disiapkan
                    </h4>
                    <p class="text-[11px] text-gray-500 font-medium mt-0.5 leading-relaxed">
                        Apoteker memverifikasi resep & meracik obat di apotek.
                    </p>
                </div>
            </div>

            <!-- Step 2: Siap & Panggilan Kurir -->
            <div id="step-ready" class="flex items-start gap-4 relative {{ in_array($delivery->status, ['ready', 'awaiting_courier', 'delivering', 'completed']) ? '' : 'opacity-40' }}">
                <div id="icon-ready" class="w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 {{ in_array($delivery->status, ['delivering', 'completed']) ? 'bg-primary-green text-white' : (in_array($delivery->status, ['ready', 'awaiting_courier']) ? 'bg-purple-600 text-white scale-110 ring-2 ring-purple-100' : 'bg-gray-300 text-white') }}">
                    @if(in_array($delivery->status, ['delivering', 'completed']))
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    @elseif(in_array($delivery->status, ['ready', 'awaiting_courier']))
                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    @endif
                </div>
                <div class="flex-1 -mt-0.5">
                    <h4 id="title-ready" class="font-black text-xs uppercase tracking-wider {{ in_array($delivery->status, ['ready', 'awaiting_courier']) ? 'text-purple-700' : (in_array($delivery->status, ['delivering', 'completed']) ? 'text-gray-800' : 'text-gray-400') }}">
                        2. Paket Siap di Apotek
                    </h4>
                    <p class="text-[11px] text-gray-500 font-medium mt-0.5 leading-relaxed">
                        Obat selesai dikemas dan siap dijemput oleh kurir.
                    </p>
                </div>
            </div>

            <!-- Step 3: Dalam Pengantaran -->
            <div id="step-delivering" class="flex items-start gap-4 relative {{ in_array($delivery->status, ['delivering', 'completed']) ? '' : 'opacity-40' }}">
                <div id="icon-delivering" class="w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 {{ $delivery->status === 'completed' ? 'bg-primary-green text-white' : ($delivery->status === 'delivering' ? 'bg-emerald-600 text-white scale-110 ring-2 ring-emerald-100' : 'bg-gray-300 text-white') }}">
                    @if($delivery->status === 'completed')
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    @elseif($delivery->status === 'delivering')
                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    @endif
                </div>
                <div class="flex-1 -mt-0.5">
                    <h4 id="title-delivering" class="font-black text-xs uppercase tracking-wider {{ $delivery->status === 'delivering' ? 'text-emerald-700' : ($delivery->status === 'completed' ? 'text-gray-800' : 'text-gray-400') }}">
                        3. Sedang Diantar Kurir
                    </h4>
                    <p id="text-delivering" class="text-[11px] text-gray-500 font-medium mt-0.5 leading-relaxed">
                        {{ $delivery->status === 'delivering' ? 'Kurir sedang dalam perjalanan menuju alamat Anda.' : 'Kurir akan mengantar obat segera setelah dijemput.' }}
                    </p>
                </div>
            </div>

            <!-- Step 4: Selesai -->
            <div id="step-completed" class="flex items-start gap-4 relative {{ $delivery->status === 'completed' ? '' : 'opacity-40' }}">
                <div id="icon-completed" class="w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 {{ $delivery->status === 'completed' ? 'bg-primary-green text-white scale-110 ring-2 ring-green-100' : 'bg-gray-300 text-white' }}">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                </div>
                <div class="flex-1 -mt-0.5">
                    <h4 id="title-completed" class="font-black text-xs uppercase tracking-wider {{ $delivery->status === 'completed' ? 'text-primary-green' : 'text-gray-400' }}">
                        4. Obat Telah Diterima
                    </h4>
                    <p class="text-[11px] text-gray-500 font-medium mt-0.5 leading-relaxed">
                        Paket obat sampai dengan selamat di tangan pasien.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courier Profile Card -->
    <div id="courier-card" class="{{ $delivery->courier ? '' : 'hidden' }} mb-6 pt-5 border-t border-gray-100 flex items-center justify-between bg-gray-50/80 p-4 rounded-2xl">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0">
                <h4 id="courier-name" class="font-black text-gray-800 text-sm truncate leading-tight">
                    {{ $delivery->courier->name ?? 'Kurir SIFANTAR' }}
                </h4>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Kurir Pengantar</p>
                </div>
            </div>
        </div>
        <div id="courier-phone-container">
            <a id="courier-phone-btn" href="tel:{{ $delivery->courier->phone ?? '' }}" class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center text-primary-green hover:bg-primary-green hover:text-white shadow-sm border border-gray-100 active:scale-95 transition-all {{ ($delivery->courier && $delivery->courier->phone) ? '' : 'hidden' }}" title="Hubungi Kurir">
                <i data-lucide="phone" class="w-5 h-5"></i>
            </a>
        </div>
    </div>

    <!-- Delivery Address Card -->
    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Tujuan</span>
            <span class="text-[10px] font-bold text-gray-600">{{ $delivery->patient->name }}</span>
        </div>
        <div class="flex items-start gap-2.5">
            <i data-lucide="map-pin" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
            <p class="text-xs text-gray-700 font-medium leading-relaxed">{{ $delivery->delivery_address }}</p>
        </div>
    </div>

    <!-- Success Confirmation -->
    <div id="success-confirmation" class="{{ $delivery->status === 'completed' ? '' : 'hidden' }} p-6 bg-green-50 rounded-[28px] border-2 border-green-200 flex flex-col items-center text-center animate-in zoom-in duration-500">
        <div class="w-14 h-14 bg-primary-green rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-200 mb-3">
            <i data-lucide="check-check" class="w-7 h-7"></i>
        </div>
        <h3 class="font-black text-gray-800 text-base mb-1">Obat Telah Selesai Diterima!</h3>
        <p class="text-[11px] text-gray-600 font-medium mb-5">Terima kasih telah mempercayakan pengantaran obat pada SIFANTAR. Semoga sehat selalu!</p>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isCourier() ? route('courier.dashboard') : route('home')) }}" class="w-full bg-primary-green text-white py-3.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-md shadow-green-100 active:scale-95 transition-all text-center">
            Kembali ke Beranda
        </a>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        const map = L.map('map', {
            zoom: 14,
            zoomControl: false,
            attributionControl: false
        });

        // Clean natural OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Pharmacy Position (From backend config or Jakarta center)
        const pharmacyPos = [{{ config('app.pharmacy_lat', -6.2120) }}, {{ config('app.pharmacy_lng', 106.8420) }}];
        map.setView(pharmacyPos, 14);

        const pharmacyIcon = L.divIcon({
            html: '<div class="w-9 h-9 bg-emerald-600 rounded-2xl flex items-center justify-center text-white border-2 border-white shadow-lg"><i data-lucide="building" class="w-4 h-4"></i></div>',
            className: 'custom-pharmacy-icon',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });
        const pharmacyMarker = L.marker(pharmacyPos, {icon: pharmacyIcon}).addTo(map);
        pharmacyMarker.bindPopup("<b>Apotek Farmasi SIFANTAR</b>");

        // Patient Destination Marker
        const patientIcon = L.divIcon({
            html: '<div class="w-9 h-9 bg-primary-orange rounded-2xl flex items-center justify-center text-white border-2 border-white shadow-lg"><i data-lucide="home" class="w-4 h-4"></i></div>',
            className: 'custom-patient-icon',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        let patientMarker = null;
        let courierMarker = null;

        function updateMapView(pos) {
            if (patientMarker) {
                patientMarker.setLatLng(pos);
            } else {
                patientMarker = L.marker(pos, {icon: patientIcon}).addTo(map);
                patientMarker.bindPopup("<b>Lokasi Pengantaran:</b><br>{{ addslashes($delivery->delivery_address) }}");
            }

            const points = [pharmacyPos, pos];
            if (courierMarker) {
                points.push(courierMarker.getLatLng());
            }

            const group = new L.featureGroup(points.map(p => L.marker(p)));
            map.fitBounds(group.getBounds().pad(0.25));
            lucide.createIcons();
        }

        // Set patient position from lat/lng or Nominatim fallback
        @if($delivery->latitude && $delivery->longitude)
            updateMapView([{{ $delivery->latitude }}, {{ $delivery->longitude }}]);
        @else
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent("{{ addslashes($delivery->delivery_address) }}")}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        updateMapView([parseFloat(data[0].lat), parseFloat(data[0].lon)]);
                    }
                })
                .catch(err => console.error("Geocoding err:", err));
        @endif

        // Fix map sizing
        setTimeout(() => { map.invalidateSize(); }, 400);

        // Helper to update status badge in header
        function updateStatusBadge(status) {
            const badgeEl = document.getElementById('status-header-badge');
            if (!badgeEl) return;

            let html = '';
            if (status === 'pending') {
                html = '<span class="bg-orange-100 text-primary-orange text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Menunggu</span>';
            } else if (status === 'preparing') {
                html = '<span class="bg-yellow-100 text-yellow-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Disiapkan</span>';
            } else if (status === 'ready') {
                html = '<span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Paket Siap</span>';
            } else if (status === 'awaiting_courier') {
                html = '<span class="bg-purple-100 text-purple-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Cari Kurir</span>';
            } else if (status === 'delivering') {
                html = '<span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span> Diantar</span>';
            } else if (status === 'completed') {
                html = '<span class="bg-green-100 text-green-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Selesai</span>';
            } else {
                html = `<span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">${status.toUpperCase()}</span>`;
            }
            badgeEl.innerHTML = html;
        }

        // Live Tracking Poll Function
        function updateTracking() {
            fetch("{{ route('tracking.api', $delivery->id) }}")
                .then(response => response.json())
                .then(data => {
                    const status = data.status;

                    // 1. Update Header Badge
                    updateStatusBadge(status);

                    // 2. Update ETA & Distance
                    if (data.eta) {
                        const etaEl = document.getElementById('eta-text');
                        if (etaEl) etaEl.innerText = data.eta + ' WIB';
                    }
                    if (data.distance) {
                        const distEl = document.getElementById('distance-text');
                        if (distEl) distEl.innerText = data.distance;
                    }

                    // 3. Update Map Overlays
                    const searchingOverlay = document.getElementById('searching-overlay');
                    const prepChip = document.getElementById('prep-overlay-chip');

                    if (['ready', 'awaiting_courier'].includes(status)) {
                        if (searchingOverlay) searchingOverlay.classList.remove('hidden');
                        if (prepChip) prepChip.classList.add('hidden');
                    } else if (['pending', 'preparing'].includes(status)) {
                        if (searchingOverlay) searchingOverlay.classList.add('hidden');
                        if (prepChip) prepChip.classList.remove('hidden');
                    } else {
                        if (searchingOverlay) searchingOverlay.classList.add('hidden');
                        if (prepChip) prepChip.classList.add('hidden');
                    }

                    // 4. Update Steps UI Dynamically
                    const stepPrep = document.getElementById('step-prep');
                    const iconPrep = document.getElementById('icon-prep');
                    const titlePrep = document.getElementById('title-prep');

                    const stepReady = document.getElementById('step-ready');
                    const iconReady = document.getElementById('icon-ready');
                    const titleReady = document.getElementById('title-ready');

                    const stepDelivering = document.getElementById('step-delivering');
                    const iconDelivering = document.getElementById('icon-delivering');
                    const titleDelivering = document.getElementById('title-delivering');

                    const stepCompleted = document.getElementById('step-completed');
                    const iconCompleted = document.getElementById('icon-completed');
                    const titleCompleted = document.getElementById('title-completed');

                    // If at least ready, step 1 is checkmarked
                    if (['ready', 'awaiting_courier', 'delivering', 'completed'].includes(status)) {
                        if (iconPrep) {
                            iconPrep.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-primary-green text-white';
                            iconPrep.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
                        }
                        if (titlePrep) titlePrep.className = 'font-black text-xs uppercase tracking-wider text-gray-800';
                    }

                    // Step 2: Ready / Awaiting courier
                    if (['ready', 'awaiting_courier'].includes(status)) {
                        if (stepReady) stepReady.classList.remove('opacity-40');
                        if (iconReady) {
                            iconReady.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-purple-600 text-white scale-110 ring-2 ring-purple-100';
                            iconReady.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>';
                        }
                        if (titleReady) titleReady.className = 'font-black text-xs uppercase tracking-wider text-purple-700';
                    } else if (['delivering', 'completed'].includes(status)) {
                        if (stepReady) stepReady.classList.remove('opacity-40');
                        if (iconReady) {
                            iconReady.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-primary-green text-white';
                            iconReady.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
                        }
                        if (titleReady) titleReady.className = 'font-black text-xs uppercase tracking-wider text-gray-800';
                    }

                    // Step 3: Delivering
                    if (status === 'delivering') {
                        if (stepDelivering) stepDelivering.classList.remove('opacity-40');
                        if (iconDelivering) {
                            iconDelivering.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-emerald-600 text-white scale-110 ring-2 ring-emerald-100';
                            iconDelivering.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>';
                        }
                        if (titleDelivering) titleDelivering.className = 'font-black text-xs uppercase tracking-wider text-emerald-700';
                    } else if (status === 'completed') {
                        if (stepDelivering) stepDelivering.classList.remove('opacity-40');
                        if (iconDelivering) {
                            iconDelivering.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-primary-green text-white';
                            iconDelivering.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
                        }
                        if (titleDelivering) titleDelivering.className = 'font-black text-xs uppercase tracking-wider text-gray-800';
                    }

                    // Step 4: Completed
                    if (status === 'completed') {
                        if (stepCompleted) stepCompleted.classList.remove('opacity-40');
                        if (iconCompleted) {
                            iconCompleted.className = 'w-7 h-7 rounded-full border-4 border-white flex items-center justify-center shadow-md relative z-10 bg-primary-green text-white scale-110 ring-2 ring-green-100';
                            iconCompleted.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
                        }
                        if (titleCompleted) titleCompleted.className = 'font-black text-xs uppercase tracking-wider text-primary-green';

                        // Show success confirmation card
                        const successCard = document.getElementById('success-confirmation');
                        if (successCard) successCard.classList.remove('hidden');

                        if (courierMarker) {
                            courierMarker.remove();
                            courierMarker = null;
                        }
                    }

                    // 5. Update Courier Card
                    if (data.courier && data.courier.name) {
                        const courierCard = document.getElementById('courier-card');
                        if (courierCard) courierCard.classList.remove('hidden');

                        const courierName = document.getElementById('courier-name');
                        if (courierName) courierName.innerText = data.courier.name;

                        const phoneBtn = document.getElementById('courier-phone-btn');
                        if (phoneBtn && data.courier.phone) {
                            phoneBtn.href = 'tel:' + data.courier.phone;
                            phoneBtn.classList.remove('hidden');
                        }
                    }

                    // 6. Update Courier Marker on Map
                    if (status === 'delivering' && data.courier && data.courier.lat && data.courier.lng) {
                        const courierPos = [parseFloat(data.courier.lat), parseFloat(data.courier.lng)];

                        if (courierMarker) {
                            courierMarker.setLatLng(courierPos);
                        } else {
                            const courierIcon = L.divIcon({
                                html: '<div class="relative"><div class="absolute inset-0 bg-primary-green opacity-25 rounded-full animate-ping scale-150"></div><div class="w-11 h-11 bg-primary-green rounded-2xl flex items-center justify-center text-white border-2 border-white shadow-xl rotate-6"><i data-lucide="truck" class="w-5 h-5"></i></div></div>',
                                className: 'custom-courier-icon',
                                iconSize: [44, 44],
                                iconAnchor: [22, 22]
                            });
                            courierMarker = L.marker(courierPos, {icon: courierIcon}).addTo(map);
                            courierMarker.bindPopup("<b>Kurir SIFANTAR:</b> " + (data.courier.name || 'Sedang Diantar'));
                        }

                        // Auto-center bounds if out of view
                        if (patientMarker) {
                            const group = new L.featureGroup([patientMarker, courierMarker]);
                            map.fitBounds(group.getBounds().pad(0.3));
                        }
                    }

                    lucide.createIcons();
                })
                .catch(err => console.error("Live tracking poll err:", err));
        }

        // Initial courier marker if status is delivering on load
        @if($delivery->status === 'delivering' && $delivery->courier && $delivery->courier->latitude)
            const initialCourierPos = [{{ $delivery->courier->latitude }}, {{ $delivery->courier->longitude }}];
            const initialCourierIcon = L.divIcon({
                html: '<div class="relative"><div class="absolute inset-0 bg-primary-green opacity-25 rounded-full animate-ping scale-150"></div><div class="w-11 h-11 bg-primary-green rounded-2xl flex items-center justify-center text-white border-2 border-white shadow-xl rotate-6"><i data-lucide="truck" class="w-5 h-5"></i></div></div>',
                className: 'custom-courier-icon',
                iconSize: [44, 44],
                iconAnchor: [22, 22]
            });
            courierMarker = L.marker(initialCourierPos, {icon: initialCourierIcon}).addTo(map);
            courierMarker.bindPopup("<b>Kurir SIFANTAR:</b> {{ addslashes($delivery->courier->name) }}");
        @endif

        // Trigger immediate poll then repeat every 4 seconds
        updateTracking();
        setInterval(updateTracking, 4000);

        lucide.createIcons();
    });
</script>
@endsection
