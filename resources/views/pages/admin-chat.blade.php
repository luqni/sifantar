@extends('layouts.app')
@section('title', 'Inbox Chat - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-6 sticky top-0 z-30 border-b border-gray-100">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl font-black text-gray-800">Inbox Pesan Pasien</h2>
        <span class="bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-full">{{ $chats->where('is_read', false)->count() }} Baru</span>
    </div>
    <div class="input-field mt-4 py-2">
        <i data-lucide="search" class="text-gray-400 w-4 h-4"></i>
        <input type="text" placeholder="Cari nama pasien..." class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 text-sm font-medium">
    </div>
</div>

<!-- Chat List -->
<div class="px-6 py-4 space-y-3 mb-24">
    @forelse($chats as $chat)
    <a href="{{ route('page', 'chat') }}" class="block {{ !$chat->is_read ? 'bg-orange-50 border-orange-100' : 'bg-white border-gray-100' }} rounded-2xl border shadow-sm p-4 relative">
        @if(!$chat->is_read)
        <div class="absolute top-4 right-4 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></div>
        @endif
        <div class="flex gap-4 items-center">
            <div class="w-12 h-12 rounded-full {{ !$chat->is_read ? 'bg-orange-200' : 'bg-gray-100' }} flex items-center justify-center shrink-0">
                <span class="font-black {{ !$chat->is_read ? 'text-primary-orange' : 'text-gray-400' }} text-lg">
                    {{ strtoupper(substr($chat->sender->name, 0, 1)) . strtoupper(substr(explode(' ', $chat->sender->name)[1] ?? '', 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-baseline mb-1">
                    <h3 class="font-black text-gray-800 text-sm truncate pr-4">{{ $chat->sender->name }}</h3>
                    <span class="text-[10px] text-gray-500 font-bold">{{ $chat->created_at->format('H:i') }}</span>
                </div>
                <p class="text-xs text-gray-600 font-medium truncate">{{ $chat->message }}</p>
            </div>
        </div>
    </a>
    @empty
    <div class="text-center py-12">
        <p class="text-gray-400 font-bold">Inbox kosong</p>
    </div>
    @endforelse
</div>

@endsection
