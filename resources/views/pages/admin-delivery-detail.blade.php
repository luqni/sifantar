@extends('layouts.app')
@section('title', 'Detail Pesanan - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-4 sticky top-0 z-30 border-b border-gray-100 flex items-center gap-4">
    <a href="{{ route('admin.dashboard') }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Detail Pesanan</h2>
</div>

<div class="px-6 py-6 pb-24 space-y-6">
    <!-- Patient Info -->
    <div class="card-sifantar">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-primary-green/10 rounded-full flex items-center justify-center text-primary-green">
                <i data-lucide="user" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Nama Pasien</p>
                <h3 class="font-black text-gray-800">{{ $delivery->patient->name }}</h3>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-primary-orange">
                <i data-lucide="hash" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Nomor Pesanan</p>
                <h3 class="font-black text-gray-800">#{{ $delivery->tracking_number }}</h3>
            </div>
        </div>
    </div>

    <!-- Medicine List -->
    <div class="card-sifantar">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Daftar Obat (Resep)</h3>
        <div class="space-y-4">
            @foreach($delivery->items as $item)
            <div class="flex items-start justify-between border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500">
                        <i data-lucide="pill" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $item->medicine ? $item->medicine->name : $item->medicine_name }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">Kuantitas: {{ $item->quantity }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Address -->
    <div class="card-sifantar">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Pengiriman</h3>
        <div class="flex gap-3 text-gray-700 bg-gray-50 p-4 rounded-2xl">
            <i data-lucide="map-pin" class="w-5 h-5 text-red-400 shrink-0"></i>
            <p class="text-xs font-medium leading-relaxed">{{ $delivery->delivery_address }}</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-3">
        @if($delivery->status === 'pending')
            <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="w-full bg-primary-orange text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-orange-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                    Terima & Siapkan Obat
                </button>
            </form>
        @elseif($delivery->status === 'ready')
            <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="delivering">
                <button type="submit" class="w-full bg-blue-500 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-blue-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Panggil Kurir
                </button>
            </form>
        @endif
    </div>
</div>

@endsection
