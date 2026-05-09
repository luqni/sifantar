@extends('layouts.app')
@section('title', 'Riwayat - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center justify-between border-b border-gray-50">
    <div class="flex items-center gap-3">
        <a href="{{ auth()->user()->isCourier() ? route('courier.dashboard') : route('home') }}" class="p-1">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-black text-gray-800">Riwayat Pengantaran</h1>
    </div>
    <a href="{{ route('chat') }}" class="p-2 text-primary-orange hover:bg-orange-50 rounded-full transition-colors">
        <i data-lucide="message-square" class="w-6 h-6"></i>
    </a>
</header>

<div class="px-6 py-6 space-y-6 pb-24">
    @forelse($deliveries as $delivery)
    <div class="card-sifantar flex flex-col group active:border-primary-green transition-all relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $delivery->status === 'completed' ? 'bg-primary-green' : 'bg-primary-orange' }}"></div>
        
        <div class="flex justify-between items-start mb-4">
            <div>
                <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block {{ $delivery->status === 'completed' ? 'bg-green-100 text-primary-green' : 'bg-orange-100 text-primary-orange' }}">
                    {{ $delivery->status === 'completed' ? 'Selesai' : (
                        $delivery->status === 'delivering' ? 'Sedang Diantar' : (
                            $delivery->status === 'ready' || $delivery->status === 'awaiting_courier' ? 'Siap Dikirim' : 'Diproses'
                        )
                    ) }}
                </span>
                <h4 class="text-gray-800 font-black text-base">#{{ $delivery->tracking_number }}</h4>
                @if(auth()->user()->isCourier())
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pasien: {{ $delivery->patient->name }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $delivery->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="space-y-3 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300">
                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium truncate">{{ $delivery->delivery_address }}</p>
            </div>
            
            <div class="mt-4 p-4 bg-gray-50 rounded-2xl">
                <p class="text-[9px] text-gray-400 font-black uppercase mb-2 tracking-widest">Daftar Obat:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($delivery->items as $item)
                    <span class="text-[10px] bg-white border border-gray-100 px-2 py-1 rounded-lg font-bold text-gray-600">
                        {{ $item->medicine ? $item->medicine->name : $item->medicine_name }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('tracking.show', $delivery->id) }}" class="bg-blue-100 text-blue-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-center shadow-sm active:scale-95 transition-transform">Lacak</a>
            <button class="bg-gray-100 text-gray-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm active:scale-95 transition-transform">Beli Lagi</button>
        </div>
    </div>
    @empty
    <div class="text-center py-20">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="clipboard-list" class="w-10 h-10 text-gray-200"></i>
        </div>
        <p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Belum ada riwayat</p>
        <p class="text-[10px] text-gray-300 mt-1">Pesanan Anda akan muncul di sini</p>
    </div>
    @endforelse
</div>
@endsection
