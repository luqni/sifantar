@extends('layouts.app')
@section('title', 'Dashboard - SIFANTAR')
@section('content')
<!-- Header/Hero Section -->
    <div class="bg-gradient-to-br from-primary-orange to-orange-400 rounded-b-[40px] px-6 pt-12 pb-24 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
        <div class="flex justify-between items-start relative z-10">
            <div class="text-white">
                <p class="text-sm opacity-90 mb-1 font-medium">Selamat Datang, {{ ucfirst(auth()->user()->role ?? 'Guest') }}</p>
                <h2 class="text-2xl font-black">{{ auth()->user()->name ?? 'Budi Santoso' }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('page', 'notifications') }}" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full">
                    <i data-lucide="bell" class="w-6 h-6"></i>
                    <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-primary-orange animate-pulse"></span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full active:scale-95 transition-transform">
                        <i data-lucide="log-out" class="w-6 h-6"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mt-8 relative z-10">
            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-3 flex flex-col items-center text-white border border-white/20">
                <i data-lucide="clock" class="mb-2 w-5 h-5"></i>
                <span class="text-[10px] opacity-80 uppercase tracking-wider font-bold">Aktif</span>
                <span class="font-black mt-1">{{ $stats['active_count'] }}</span>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-3 flex flex-col items-center text-white border border-white/20">
                <i data-lucide="map-pin" class="mb-2 w-5 h-5"></i>
                <span class="text-[10px] opacity-80 uppercase tracking-wider font-bold">Estimasi</span>
                <span class="font-black mt-1 text-[11px]">
                    @php 
                        $first = $activeDeliveries->first();
                    @endphp
                    @if($first)
                        @if($first->estimation_arrival)
                            {{ $first->estimation_arrival->format('H:i') }}
                        @elseif(!$first->latitude)
                            SET GPS
                        @else
                            Mencari...
                        @endif
                    @else
                        --:--
                    @endif
                </span>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-3 flex flex-col items-center text-white border border-white/20">
                <i data-lucide="clipboard-list" class="mb-2 w-5 h-5"></i>
                <span class="text-[10px] opacity-80 uppercase tracking-wider font-bold">Riwayat</span>
                <span class="font-black mt-1">{{ $stats['history_count'] }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Menu Card -->
    <div class="px-6 -mt-16 relative z-20">
        <div class="card-sifantar">
            <h3 class="text-gray-800 font-black mb-4 uppercase text-xs tracking-widest">Menu Cepat</h3>
            <div class="grid grid-cols-2 gap-6">
                <a href="{{ route('page', 'articles') }}" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-yellow-400 group-active:scale-90 transition-transform">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-700">Daftar Artikel</span>
                </a>
                <a href="{{ $activeDeliveries->count() > 0 ? route('delivery.show', $activeDeliveries->first()->id) : route('history') }}" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-blue-500 group-active:scale-90 transition-transform">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-700">Info Antar</span>
                </a>
                <a href="{{ route('page', 'history') }}" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-pink-500 group-active:scale-90 transition-transform">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-700">Riwayat</span>
                </a>
                <a href="{{ route('chat') }}" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-cyan-500 group-active:scale-90 transition-transform">
                        <i data-lucide="message-circle" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-700">Chat Farmasi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Active Delivery -->
    <div class="mt-8 px-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-800 font-black uppercase text-xs tracking-widest">Pengantaran Aktif</h3>
            <span class="text-[10px] bg-green-100 text-green-600 font-black px-2 py-0.5 rounded-full uppercase">{{ $activeDeliveries->count() }} Aktif</span>
        </div>
        
        @forelse($activeDeliveries as $delivery)
        <div class="card-sifantar mb-4 !p-4">
            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-4 min-w-[200px] shadow-sm">
                    <div class="bg-orange-50 p-3 rounded-xl text-primary-orange">
                        <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Status: {{ strtoupper($delivery->status) }}</p>
                        <p class="font-black text-gray-800 text-sm">#{{ $delivery->tracking_number }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 flex items-center gap-4 min-w-[200px] shadow-sm">
                    <div class="bg-blue-50 p-3 rounded-xl text-blue-500">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Kurir</p>
                        <p class="font-black text-gray-800 text-sm">{{ $delivery->courier->name ?? 'Mencari Kurir...' }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('tracking.show', $delivery->id) }}" class="w-full bg-primary-green text-white py-3 rounded-xl font-black text-sm mt-4 flex items-center justify-center gap-2 shadow-lg shadow-green-100/50 active:scale-95 transition-transform">
                <i data-lucide="map" class="w-4 h-4"></i>
                Lacak Pengiriman Real-time
            </a>
        </div>
        @empty
        <div class="card-sifantar text-center py-10">
            <i data-lucide="truck" class="w-12 h-12 text-gray-200 mx-auto mb-3"></i>
            <p class="text-gray-400 font-bold text-sm">Belum ada pengiriman aktif</p>
            <p class="text-[10px] text-gray-300 uppercase mt-1">Request obat Anda melalui menu chat!</p>
        </div>
        @endforelse
    </div>

    <!-- Recent Notification -->
    <div class="px-6 mt-8 mb-8">
        <h3 class="text-gray-800 font-black mb-4 uppercase text-xs tracking-widest">Notifikasi Terbaru</h3>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-start gap-4 active:bg-gray-50 transition-colors">
            <div class="bg-yellow-400 p-2.5 rounded-full text-white shadow-lg shadow-yellow-100">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg">🚚</span>
                    <span class="text-sm font-black text-gray-800">Obat Sedang Dikirim</span>
                </div>
                <p class="text-xs text-gray-500 leading-snug font-medium italic">Pesanan obat #SFT-2026-0215 sedang dalam perjalanan menuju alamat Anda.</p>
                <div class="flex justify-between items-center mt-3">
                    <p class="text-[10px] text-gray-400 font-black">10:15 WIB</p>
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
@endsection
