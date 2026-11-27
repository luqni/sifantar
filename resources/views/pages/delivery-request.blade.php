@extends('layouts.app')
@section('title', 'Request Delivery Obat - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-4 sticky top-0 z-30 border-b border-gray-100 flex items-center gap-4">
    <a href="{{ route('chat') }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </a>
    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Request Obat</h2>
</div>

<div class="px-6 py-6 pb-24">
    @if(isset($repeat) && $repeat)
    <div class="mb-5 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-start gap-3">
        <i data-lucide="rotate-ccw" class="w-4 h-4 text-primary-green mt-0.5 shrink-0"></i>
        <div>
            <p class="text-[10px] font-black text-primary-green uppercase tracking-widest">Beli Lagi</p>
            <p class="text-[11px] text-gray-600 font-medium mt-1 leading-relaxed">
                Alamat pengantaran diisi otomatis dari pesanan <span class="font-black text-gray-800">#{{ $repeat->tracking_number }}</span>.
            </p>
        </div>
    </div>
    @endif

    <form action="{{ route('delivery-request.store') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="lat_input">
        <input type="hidden" name="longitude" id="lng_input">
        
        <!-- Informasi Penanganan Resep oleh Apoteker -->
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3.5 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-primary-green/10 text-primary-green flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="clipboard-check" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] font-black text-primary-green uppercase tracking-wider">Verifikasi Obat oleh Apoteker</p>
                <p class="text-xs text-gray-600 font-medium mt-1 leading-relaxed">
                    Daftar dan rincian obat akan diperiksa serta diinput langsung oleh petugas farmasi/apoteker kami setelah permintaan pengantaran ini diterima.
                </p>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Alamat Pengantaran</h3>
                <span id="location_status" class="text-[9px] bg-blue-100 text-blue-600 font-bold px-2.5 py-0.5 rounded-full transition-colors">Mencari GPS...</span>
            </div>
            <div class="bg-white border border-orange-100 rounded-2xl p-4 shadow-sm focus-within:border-primary-orange transition-colors">
                <textarea name="address" rows="3" required placeholder="Masukkan alamat lengkap pengantaran..." class="w-full bg-transparent outline-none text-sm font-medium text-gray-800 placeholder:text-gray-400 resize-none">{{ old('address', $repeat->delivery_address ?? (auth()->user()->address ?? '')) }}</textarea>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary-green text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-green-100 active:scale-95 transition-all flex items-center justify-center gap-3">
            <i data-lucide="send" class="w-5 h-5"></i>
            Kirim Permintaan
        </button>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('lat_input').value = position.coords.latitude;
                document.getElementById('lng_input').value = position.coords.longitude;
                
                const status = document.getElementById('location_status');
                status.innerText = 'GPS Terkunci';
                status.classList.remove('bg-blue-100', 'text-blue-600');
                status.classList.add('bg-green-100', 'text-green-600');
            }, function(error) {
                const status = document.getElementById('location_status');
                status.innerText = 'GPS Gagal';
                status.classList.remove('bg-blue-100', 'text-blue-600');
                status.classList.add('bg-red-100', 'text-red-600');
            });
        }
    });
</script>
@endpush
