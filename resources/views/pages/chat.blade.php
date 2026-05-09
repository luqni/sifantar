@extends('layouts.app')
@php $hideNav = true; @endphp
@section('title', 'Chat dengan Admin - SIFANTAR')
@section('content')
<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-4 sticky top-0 z-30 border-b border-gray-100 flex items-center gap-4">
    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('page', 'home') }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-green flex items-center justify-center text-white font-black text-sm">
            AF
        </div>
        <div>
            <h2 class="text-sm font-black text-gray-800">Admin Farmasi</h2>
            <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Online</span>
            </div>
        </div>
    </div>
</div>

<!-- Chat Messages -->
<div class="px-6 py-6 flex flex-col gap-4 mb-24" id="chat-container">
    @forelse($messages as $msg)
        @if($msg->sender_id === auth()->id())
            <!-- Patient Message (Right) -->
            <div class="flex flex-col items-end gap-1 max-w-[85%] ml-auto">
                <div class="bg-primary-green text-white p-3 rounded-2xl rounded-tr-none shadow-md shadow-green-100">
                    <p class="text-xs font-medium">{{ $msg->message }}</p>
                </div>
                <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $msg->created_at->format('H:i') }}</span>
            </div>
        @else
            <!-- Admin Message (Left) -->
            <div class="flex flex-col items-start gap-1 max-w-[85%]">
                <div class="bg-white border border-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-none shadow-sm">
                    <p class="text-xs font-medium">{{ $msg->message }}</p>
                    
                    @if($msg->message === "selamat datang di sifantar, ada yang bisa kami bantu?")
                        <div class="mt-3 flex flex-col gap-2">
                            <a href="{{ route('delivery-request.create') }}" class="bg-primary-green/10 text-primary-green border border-primary-green/20 py-2 px-3 rounded-xl text-[10px] font-black uppercase text-left flex items-center justify-between group active:scale-95 transition-transform">
                                <span>Delivery Obat</span>
                                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            </a>
                            <button class="bg-primary-orange/10 text-primary-orange border border-primary-orange/20 py-2 px-3 rounded-xl text-[10px] font-black uppercase text-left flex items-center justify-between group active:scale-95 transition-transform">
                                <span>Lanjut Chat ke Admin Farmasi</span>
                                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $msg->created_at->format('H:i') }}</span>
            </div>
        @endif
    @empty
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="message-square" class="w-8 h-8 text-gray-300"></i>
            </div>
            <p class="text-gray-400 font-bold text-sm">Belum ada percakapan</p>
            <p class="text-[10px] text-gray-300 uppercase mt-1">Kirim pesan pertama Anda!</p>
        </div>
    @endforelse
</div>

<!-- Input Area -->
<div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-gray-100 z-[60]">
    <div class="flex gap-3 items-center max-w-md mx-auto">
        <button class="p-3 bg-gray-50 rounded-2xl text-gray-400 active:scale-95 transition-transform">
            <i data-lucide="paperclip" class="w-5 h-5"></i>
        </button>
        <form action="{{ route('chat.store') }}" method="POST" class="flex-1 flex gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $admin->id }}">
            <div class="flex-1 bg-gray-50 rounded-2xl px-4 py-3 flex items-center">
                <input type="text" name="message" required placeholder="Tulis obat yang Anda butuhkan..." class="bg-transparent outline-none text-xs font-medium text-gray-800 w-full placeholder:text-gray-400">
            </div>
            <button type="submit" class="p-3 bg-primary-green text-white rounded-2xl shadow-lg shadow-green-100 active:scale-95 transition-transform">
                <i data-lucide="send" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom on load
    window.scrollTo(0, document.body.scrollHeight);
</script>
@endpush
@endsection
