@extends('layouts.app')
@section('title', 'Detail Pengantaran - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center gap-3 border-b border-gray-50">
    <a href="{{ route('history') }}" class="p-1">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <h1 class="text-lg font-black text-gray-800">Detail Pengantaran Obat</h1>
</header>

<div class="px-6 py-6">
    <!-- Tabs -->
    <div class="bg-green-100 rounded-[28px] p-2 flex gap-2 mb-8 shadow-inner">
        <a href="{{ route('delivery.show', $delivery->id) }}" class="flex-1 py-3.5 px-4 rounded-[22px] text-xs font-black transition-all text-center bg-primary-green text-white shadow-lg shadow-green-100">Penerimaan</a>
        <a href="{{ route('chat') }}" class="flex-1 py-3.5 px-4 rounded-[22px] text-xs font-black transition-all text-center text-gray-500 hover:bg-white/50">Chat Farmasi</a>
    </div>

    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <!-- Delivery Status Card -->
        <div class="card-sifantar border-primary-green bg-gradient-to-br from-white to-green-50/30">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-cyan-700 rounded-3xl flex items-center justify-center text-white text-2xl shadow-xl shadow-cyan-100 rotate-3">
                    <span>💊</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        @if(in_array($delivery->status, ['delivering', 'pending', 'ready', 'awaiting_courier']))
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                        @endif
                        <h4 class="font-black text-gray-800 text-lg">#{{ $delivery->tracking_number }}</h4>
                    </div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">
                        @foreach($delivery->items as $item)
                            {{ $item->medicine ? $item->medicine->name : $item->medicine_name }}{{ !$loop->last ? ' • ' : '' }}
                        @endforeach
                    </p>
                    <div class="bg-green-100 text-[10px] font-black text-green-700 px-3 py-1 rounded-full mt-3 inline-block uppercase tracking-widest">
                        {{ strtoupper($delivery->status) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Courier Info -->
        @if($delivery->courier)
        <div class="card-sifantar flex flex-col gap-6">
            <h3 class="text-gray-400 font-extrabold text-[10px] uppercase tracking-[0.3em]">Informasi Logistik</h3>
            <div class="space-y-5">
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-check" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kurir</span>
                    </div>
                    <span class="text-sm font-black text-gray-800">{{ $delivery->courier->name }}</span>
                </div>
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <i data-lucide="phone-call" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kontak</span>
                    </div>
                    <span class="text-sm font-black text-gray-800">{{ $delivery->courier->phone ?? '08XXXXXXXXXX' }}</span>
                </div>
            </div>
        </div>
        @else
        <div class="card-sifantar text-center py-6">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Menunggu Kurir</p>
        </div>
        @endif

        <!-- Confirmation Action -->
        @if($delivery->status !== 'completed')
        <div class="pt-4">
            <a href="{{ route('tracking.show', $delivery->id) }}" class="w-full bg-primary-green text-white py-6 rounded-3xl font-black text-lg flex items-center justify-between px-8 shadow-2xl shadow-green-200 active:scale-95 transition-all group overflow-hidden relative mb-4">
                <span class="relative z-10">Lacak Pengiriman</span>
                <div class="bg-white p-2.5 rounded-full text-primary-green relative z-10">
                    <i data-lucide="map" class="w-7 h-7"></i>
                </div>
            </a>
            <p class="text-[10px] text-gray-400 text-center mt-2 font-bold uppercase tracking-widest italic">Silakan lacak posisi kurir Anda secara real-time.</p>
        </div>
        @else
        <div class="pt-4">
            <div class="w-full bg-gray-100 text-gray-400 py-6 rounded-3xl font-black text-lg flex items-center justify-center px-8">
                <span>Pesanan Selesai</span>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
