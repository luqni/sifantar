@extends('layouts.app')
@section('title', 'Admin Dashboard - SIFANTAR')
@section('content')
<!-- Header/Hero Section -->
<div class="bg-gradient-to-br from-primary-green to-green-600 rounded-b-[40px] px-6 pt-12 pb-24 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="flex justify-between items-start relative z-10">
        <div class="text-white">
            <p class="text-sm opacity-90 mb-1 font-medium">Panel Kendali Farmasi</p>
            <h2 class="text-2xl font-black">{{ auth()->user()->name ?? 'Admin Farmasi' }}</h2>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('page', 'notifications') }}" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full">
                <i data-lucide="bell" class="w-6 h-6"></i>
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full active:scale-95 transition-transform">
                    <i data-lucide="log-out" class="w-6 h-6"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-2 mt-8 relative z-10">
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="inbox" class="mb-1 w-4 h-4"></i>
            <span class="text-[8px] opacity-80 uppercase font-bold text-center">Masuk</span>
            <span class="font-black text-sm">{{ $stats['pending'] }}</span>
        </div>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="package" class="mb-1 w-4 h-4"></i>
            <span class="text-[8px] opacity-80 uppercase font-bold text-center">Proses</span>
            <span class="font-black text-sm">{{ $stats['preparing'] }}</span>
        </div>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="check-circle" class="mb-1 w-4 h-4"></i>
            <span class="text-[8px] opacity-80 uppercase font-bold text-center">Siap</span>
            <span class="font-black text-sm">{{ $stats['ready'] }}</span>
        </div>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="truck" class="mb-1 w-4 h-4"></i>
            <span class="text-[8px] opacity-80 uppercase font-bold text-center">Kirim</span>
            <span class="font-black text-sm">{{ $stats['delivering'] }}</span>
        </div>
    </div>

    <div class="mt-6 relative z-10">
        <a href="{{ route('admin.users') }}" class="w-full bg-white/20 backdrop-blur-md rounded-2xl p-4 flex items-center justify-between text-white border border-white/20 hover:bg-white/30 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm">Manajemen User</h3>
                    <p class="text-[10px] opacity-80 uppercase tracking-widest font-bold">Kelola Data Pengguna</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </a>
    </div>
</div>

<!-- Order Management -->
<div class="px-6 -mt-16 relative z-20 mb-24">
    <div class="card-sifantar mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-800 font-black uppercase text-xs tracking-widest">Pesanan Aktif</h3>
            <span class="bg-green-100 text-green-600 text-[10px] font-black px-2 py-1 rounded-full uppercase">{{ $deliveries->count() }} Total</span>
        </div>
        
        <div class="space-y-4">
            @forelse($deliveries as $delivery)
            <div class="border border-gray-100 rounded-2xl p-4 active:bg-gray-50 transition-colors relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $delivery->status === 'pending' ? 'bg-red-500' : 'bg-blue-500' }}"></div>
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-black text-gray-800">#{{ $delivery->tracking_number }}</p>
                            <a href="{{ route('admin.delivery.show', $delivery->id) }}" class="text-[9px] bg-gray-100 text-gray-500 font-bold px-1.5 py-0.5 rounded-md hover:bg-gray-200 transition-colors uppercase">Detail</a>
                        </div>
                        <p class="text-[10px] text-gray-500 font-medium">{{ $delivery->patient->name }} • {{ $delivery->created_at->diffForHumans() }}</p>
                    </div>
                    @if($delivery->status === 'pending')
                        <span class="bg-orange-100 text-primary-orange text-[10px] font-black px-2 py-1 rounded-lg uppercase">Menunggu</span>
                    @else
                        <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2 py-1 rounded-lg uppercase">Siap</span>
                    @endif
                </div>
                
                <div class="flex gap-2 text-xs font-medium text-gray-600 mb-3 bg-gray-50 p-2 rounded-lg">
                    <i data-lucide="pill" class="w-4 h-4 text-gray-400"></i>
                    <span class="truncate">
                        @foreach($delivery->items as $item)
                            {{ $item->medicine ? $item->medicine->name : $item->medicine_name }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </span>
                </div>

                @if($delivery->status === 'pending')
                    <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="ready">
                        <button type="submit" class="w-full bg-primary-orange text-white py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-orange-100/50 active:scale-95 transition-transform">
                            <i data-lucide="package-check" class="w-4 h-4"></i>
                            Terima & Siapkan Obat
                        </button>
                    </form>
                @elseif($delivery->status === 'ready')
                    <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="awaiting_courier">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-100/50 active:scale-95 transition-transform">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                            Panggil Kurir
                        </button>
                    </form>
                @endif
            </div>
            @empty
            <div class="text-center py-8">
                <i data-lucide="check-circle" class="w-12 h-12 text-gray-200 mx-auto mb-2"></i>
                <p class="text-gray-400 font-bold text-sm">Tidak ada pesanan aktif</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
