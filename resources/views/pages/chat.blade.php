@extends('layouts.app')
@php $hideNav = true; @endphp
@section('title', 'Chat dengan Admin - SIFANTAR')
@section('content')
<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-4 sticky top-0 z-30 border-b border-gray-100 flex items-center gap-4">
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.chat') : route('page', 'home') }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-green flex items-center justify-center text-white font-black text-sm">
            @php
                $initials = auth()->user()->role === 'admin' 
                    ? strtoupper(substr($partner->name, 0, 1) . substr(explode(' ', $partner->name)[1] ?? '', 0, 1))
                    : 'AF';
                $name = auth()->user()->role === 'admin' ? $partner->name : 'Admin Farmasi';
            @endphp
            {{ $initials }}
        </div>
        <div>
            <h2 class="text-sm font-black text-gray-800">{{ $name }}</h2>
            <div class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Online</span>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <button onclick="document.getElementById('order-modal').classList.remove('hidden')" class="bg-red-500 text-white text-[10px] font-black uppercase px-3 py-2 rounded-xl shadow-md shadow-red-200 active:scale-95 transition-transform flex items-center gap-1 ml-auto shrink-0">
        <i data-lucide="shopping-cart" class="w-3 h-3"></i> Buat Pesanan
    </button>
    @endif
</div>

<!-- Chat Messages -->
<div class="px-6 py-6 flex flex-col gap-4 mb-24" id="chat-container">
    @forelse($messages as $msg)
        @if($msg->sender_id === auth()->id())
            <!-- Patient Message (Right) -->
            <div class="flex flex-col items-end gap-1 max-w-[85%] ml-auto">
                <div class="bg-primary-green text-white p-3 rounded-2xl rounded-tr-none shadow-md shadow-green-100">
                    @if($msg->image_path)
                        <img src="{{ asset('storage/' . $msg->image_path) }}" alt="Attachment" class="max-w-full rounded-xl mb-2 object-cover">
                    @endif
                    @if($msg->message)
                        <p class="text-xs font-medium">{{ $msg->message }}</p>
                    @endif
                </div>
                <span class="text-[9px] text-gray-400 font-bold uppercase">
                    {{ $msg->created_at->timezone('Asia/Jakarta')->isToday() ? $msg->created_at->timezone('Asia/Jakarta')->format('H:i') : $msg->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                </span>
            </div>
        @else
            <!-- Admin Message (Left) -->
            <div class="flex flex-col items-start gap-1 max-w-[85%]">
                <div class="bg-white border border-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-none shadow-sm">
                    @if($msg->image_path)
                        <img src="{{ asset('storage/' . $msg->image_path) }}" alt="Attachment" class="max-w-full rounded-xl mb-2 object-cover">
                    @endif
                    @if($msg->message)
                        <p class="text-xs font-medium">{{ $msg->message }}</p>
                    @endif
                    
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
                <span class="text-[9px] text-gray-400 font-bold uppercase">
                    {{ $msg->created_at->timezone('Asia/Jakarta')->isToday() ? $msg->created_at->timezone('Asia/Jakarta')->format('H:i') : $msg->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                </span>
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
    <div class="flex gap-3 items-center max-w-md mx-auto relative">
        <!-- Form spans entirely to contain both file input trigger and message input -->
        <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex gap-2 w-full">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ auth()->user()->role === 'admin' ? $partner->id : $admin->id }}">
            
            <label for="chat-image" class="p-3 bg-gray-50 rounded-2xl text-gray-400 cursor-pointer active:scale-95 transition-transform flex-shrink-0">
                <i data-lucide="paperclip" class="w-5 h-5"></i>
            </label>
            <input type="file" name="image" id="chat-image" class="hidden" accept="image/*" onchange="previewImage(this)">
            
            <div class="flex-1 bg-gray-50 rounded-2xl px-4 py-3 flex items-center relative">
                <!-- Image Preview Container -->
                <div id="image-preview-container" class="hidden absolute bottom-[120%] left-0 p-2 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                    <div class="relative">
                        <img id="image-preview" src="" alt="Preview" class="h-24 w-auto rounded-lg object-cover">
                        <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center shadow-sm">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <input type="text" name="message" id="chat-message" required placeholder="Tulis obat yang Anda butuhkan..." class="bg-transparent outline-none text-xs font-medium text-gray-800 w-full placeholder:text-gray-400">
            </div>
            
            <button type="submit" class="p-3 bg-primary-green text-white rounded-2xl shadow-lg shadow-green-100 active:scale-95 transition-transform flex-shrink-0">
                <i data-lucide="send" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom on load
    window.scrollTo(0, document.body.scrollHeight);

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').classList.remove('hidden');
                document.getElementById('chat-message').placeholder = "Tambahkan keterangan (opsional)...";
                document.getElementById('chat-message').removeAttribute('required');
                
                // Refresh lucide icons for dynamically shown close button
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        document.getElementById('chat-image').value = '';
        document.getElementById('image-preview-container').classList.add('hidden');
        document.getElementById('image-preview').src = '';
        document.getElementById('chat-message').placeholder = "Tulis obat yang Anda butuhkan...";
        document.getElementById('chat-message').setAttribute('required', 'required');
    }
</script>
@endpush

@if(auth()->user()->role === 'admin')
<!-- Order Modal -->
<div id="order-modal" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-black text-gray-800 text-lg">Buat Pesanan Baru</h3>
            <button onclick="document.getElementById('order-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-full shadow-sm">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
            <form action="{{ route('admin.chat.order') }}" method="POST" id="order-form">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $partner->id }}">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Daftar Obat</label>
                        <textarea name="medicine_list" rows="3" required placeholder="1. Paracetamol 500mg (1 strip)&#10;2. Amoxicillin 500mg (1 strip)" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium text-gray-800 outline-none focus:border-primary-green focus:ring-1 focus:ring-primary-green transition-all resize-none"></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">*Tulis tiap obat di baris baru</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Total Harga Obat (Rp)</label>
                        <input type="number" name="total_price" placeholder="Contoh: 50000" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium text-gray-800 outline-none focus:border-primary-green focus:ring-1 focus:ring-primary-green transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Alamat Pengiriman</label>
                        <textarea name="delivery_address" rows="3" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium text-gray-800 outline-none focus:border-primary-green focus:ring-1 focus:ring-primary-green transition-all resize-none">{{ $partner->address }}</textarea>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="p-6 border-t border-gray-100 bg-white">
            <button type="submit" form="order-form" class="w-full bg-primary-green text-white font-black py-4 rounded-2xl shadow-lg shadow-green-200 active:scale-95 transition-transform">
                Buat Pesanan Sekarang
            </button>
        </div>
    </div>
</div>
@endif

@endsection
