@extends('layouts.app')
@section('title', 'Notifikasi - SIFANTAR')
@section('content')
<!-- Header -->
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center justify-between border-b border-gray-50">
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="p-1">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-black text-gray-800 tracking-tight">Notifikasi Terbaru</h1>
    </div>
</header>

<div class="px-5 py-6 pb-24 space-y-4">
    @forelse($notifications as $notification)
    <div class="bg-white rounded-[32px] p-5 shadow-sm border border-gray-100 flex gap-4 relative overflow-hidden">
        @if(!$notification->is_read)
            <div class="absolute top-4 right-4 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
        @endif
        
        <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center 
            {{ $notification->type === 'success' ? 'bg-green-100 text-primary-green' : 
               ($notification->type === 'delivery' ? 'bg-orange-100 text-primary-orange' : 'bg-blue-100 text-blue-500') }}">
            @if($notification->type === 'success')
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            @elseif($notification->type === 'delivery')
                <i data-lucide="truck" class="w-6 h-6"></i>
            @else
                <i data-lucide="clock" class="w-6 h-6"></i>
            @endif
        </div>
        
        <div class="flex-1">
            <h4 class="font-black text-gray-800 text-sm mb-1">{{ $notification->title }}</h4>
            <p class="text-gray-500 text-[11px] font-medium leading-relaxed mb-3">
                {{ $notification->message }}
            </p>
            <span class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">{{ $notification->created_at->format('H:i') }} WIB</span>
        </div>
    </div>
    @empty
    <div class="text-center py-20 bg-gray-50 rounded-[40px] border border-dashed border-gray-200">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i data-lucide="bell-off" class="w-8 h-8 text-gray-200"></i>
        </div>
        <p class="text-gray-400 font-black uppercase tracking-widest text-xs">Belum ada notifikasi</p>
        <p class="text-[10px] text-gray-300 mt-1">Kami akan mengabari Anda jika ada update pesanan.</p>
    </div>
    @endforelse
</div>
@endsection
