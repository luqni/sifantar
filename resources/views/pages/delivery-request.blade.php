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
    <form action="{{ route('delivery-request.store') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude" id="lat_input">
        <input type="hidden" name="longitude" id="lng_input">
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Daftar Obat (Resep)</h3>
                <span id="location_status" class="text-[9px] bg-blue-100 text-blue-600 font-bold px-2 py-0.5 rounded-full">Mencari GPS...</span>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm focus-within:border-primary-green transition-colors">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Tuliskan nama obat (satu obat per baris):</p>
                <textarea name="medicine_list" rows="6" required placeholder="Contoh:&#10;Paracetamol 500mg (10 tablet)&#10;Amoxicillin (1 botol)&#10;Vitamin C..." class="w-full bg-transparent outline-none text-sm font-medium text-gray-800 placeholder:text-gray-400 resize-none"></textarea>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Alamat Pengantaran</h3>
            <div class="bg-white border border-orange-100 rounded-2xl p-4 shadow-sm focus-within:border-primary-orange transition-colors">
                <textarea name="address" rows="3" required placeholder="Masukkan alamat lengkap pengantaran..." class="w-full bg-transparent outline-none text-sm font-medium text-gray-800 placeholder:text-gray-400 resize-none"></textarea>
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
