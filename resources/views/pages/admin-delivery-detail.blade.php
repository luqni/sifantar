@extends('layouts.app')
@section('title', 'Detail Pesanan - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-4 sticky top-0 z-30 border-b border-gray-100 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="p-2 bg-gray-50 rounded-full text-gray-600 active:scale-95 transition-transform">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Detail Pesanan</h2>
            <p class="text-[10px] text-gray-400 font-bold">#{{ $delivery->tracking_number }}</p>
        </div>
    </div>
    
    <div>
        @if($delivery->status === 'pending')
            <span class="bg-orange-100 text-primary-orange text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Menunggu</span>
        @elseif($delivery->status === 'preparing')
            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Disiapkan</span>
        @elseif($delivery->status === 'ready')
            <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Siap</span>
        @elseif($delivery->status === 'awaiting_courier')
            <span class="bg-purple-100 text-purple-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Cari Kurir</span>
        @elseif($delivery->status === 'delivering')
            <span class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Diantar</span>
        @elseif($delivery->status === 'completed')
            <span class="bg-green-100 text-green-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Selesai</span>
        @else
            <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">{{ strtoupper($delivery->status) }}</span>
        @endif
    </div>
</div>

<div class="px-6 py-6 pb-28 space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3 text-green-700 text-xs font-semibold animate-in fade-in duration-300">
        <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-primary-green"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 text-red-700 text-xs font-semibold animate-in fade-in duration-300">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Patient Info -->
    <div class="card-sifantar">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-primary-green/10 rounded-2xl flex items-center justify-center text-primary-green shrink-0">
                <i data-lucide="user" class="w-6 h-6"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Informasi Pasien</p>
                <h3 class="font-black text-gray-800 text-base">{{ $delivery->patient->name }}</h3>
                <p class="text-xs text-gray-500 font-medium">{{ $delivery->patient->email }} {{ $delivery->patient->phone ? '• ' . $delivery->patient->phone : '' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-50 text-[11px]">
            <div>
                <span class="text-gray-400 font-bold block uppercase text-[9px] tracking-wider">Waktu Pemesanan</span>
                <span class="font-bold text-gray-700">{{ $delivery->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-400 font-bold block uppercase text-[9px] tracking-wider">Kurir Ditugaskan</span>
                <span class="font-bold text-gray-700">{{ $delivery->courier->name ?? 'Belum ada kurir' }}</span>
            </div>
        </div>
    </div>

    <!-- Medicine Management (Input Resep/Obat oleh Admin/Apoteker) -->
    <div class="card-sifantar">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Daftar Obat (Resep)</h3>
                <p class="text-[10px] text-gray-500 font-medium">Diinput & diverifikasi oleh tim Apoteker</p>
            </div>
            <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-2.5 py-1 rounded-full">{{ $delivery->items->count() }} Item</span>
        </div>

        <!-- Medicine List -->
        <div class="space-y-3 mb-4">
            @forelse($delivery->items as $item)
            <div class="flex items-center justify-between p-3.5 bg-gray-50/70 border border-gray-100 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="pill" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-gray-800">{{ $item->medicine ? $item->medicine->name : $item->medicine_name }}</p>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price_at_time, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-black text-gray-700">Rp {{ number_format($item->quantity * $item->price_at_time, 0, ',', '.') }}</span>
                    @if(in_array($delivery->status, ['pending', 'preparing', 'ready']))
                    <form action="{{ route('admin.delivery.items.destroy', [$delivery->id, $item->id]) }}" method="POST" onsubmit="return confirm('Hapus obat ini dari pesanan?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus obat">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-5 bg-amber-50/70 border border-amber-200 rounded-2xl text-center">
                <i data-lucide="clipboard-list" class="w-8 h-8 text-amber-500 mx-auto mb-2"></i>
                <p class="text-xs font-bold text-amber-800">Belum ada obat yang diinput</p>
                <p class="text-[11px] text-amber-700 mt-1 leading-relaxed">
                    Pasien membuat permintaan tanpa menulis obat. Silakan periksa resep dan inputkan obat pada formulir di bawah.
                </p>
            </div>
            @endforelse
        </div>

        <!-- Total Price Summary -->
        <div class="p-3.5 bg-green-50 border border-green-100 rounded-2xl flex justify-between items-center mb-5">
            <span class="text-xs font-black text-primary-green uppercase tracking-wider">Total Harga Obat</span>
            <span class="text-sm font-black text-primary-green">Rp {{ number_format($delivery->total_price, 0, ',', '.') }}</span>
        </div>

        <!-- Form Input Obat Baru (Apoteker / Admin) -->
        @if(in_array($delivery->status, ['pending', 'preparing', 'ready']))
        <div class="pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 mb-3">
                <i data-lucide="plus-circle" class="w-4 h-4 text-primary-green"></i>
                <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider">Input Obat / Resep</h4>
            </div>

            <form action="{{ route('admin.delivery.items.store', $delivery->id) }}" method="POST" class="space-y-3" id="add-medicine-form">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Obat / Resep</label>
                    <input type="text" name="medicine_name" id="med_name_input" list="medicines_datalist" required placeholder="Pilih dari master atau ketik nama obat..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 outline-none focus:border-primary-green focus:bg-white transition-all">
                    <datalist id="medicines_datalist">
                        @foreach($availableMedicines as $med)
                            <option value="{{ $med->name }}" data-price="{{ (int)$med->price }}">{{ $med->name }} (Rp {{ number_format($med->price, 0, ',', '.') }})</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jumlah (Qty)</label>
                        <input type="number" name="quantity" value="1" min="1" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 outline-none focus:border-primary-green focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Harga Satuan (Rp)</label>
                        <input type="number" name="price_at_time" id="med_price_input" min="0" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 outline-none focus:border-primary-green focus:bg-white transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-green text-white py-3 rounded-xl font-black uppercase text-[11px] tracking-wider shadow-md shadow-green-100 hover:bg-green-600 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Obat ke Pesanan
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Address -->
    <div class="card-sifantar">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Alamat Pengiriman</h3>
            @if($delivery->latitude && $delivery->longitude)
            <span class="text-[9px] bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                <i data-lucide="map-pin" class="w-3 h-3"></i> GPS Terdeteksi
            </span>
            @endif
        </div>
        <div class="flex gap-3 text-gray-700 bg-gray-50 p-4 rounded-2xl mb-3">
            <i data-lucide="map-pin" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
            <p class="text-xs font-medium leading-relaxed">{{ $delivery->delivery_address }}</p>
        </div>
        @if($delivery->latitude && $delivery->longitude)
        <a href="https://www.google.com/maps?q={{ $delivery->latitude }},{{ $delivery->longitude }}" target="_blank" class="text-[11px] text-blue-600 font-bold flex items-center gap-1 hover:underline">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Buka Titik GPS di Google Maps
        </a>
        @endif
    </div>

    <!-- Status Actions -->
    <div class="space-y-3">
        @if($delivery->status === 'pending')
            <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="preparing">
                <button type="submit" class="w-full bg-primary-orange text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-orange-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                    Terima & Siapkan Obat
                </button>
            </form>
        @elseif($delivery->status === 'preparing')
            <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="w-full bg-primary-green text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-green-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Selesai Disiapkan
                </button>
            </form>
        @elseif($delivery->status === 'ready')
            <form action="{{ route('admin.delivery.status', $delivery->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="awaiting_courier">
                <button type="submit" class="w-full bg-blue-500 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-blue-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Panggil Kurir
                </button>
            </form>
        @elseif($delivery->status === 'awaiting_courier')
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-2xl flex items-center gap-3 text-purple-700">
                <i data-lucide="clock" class="w-5 h-5 shrink-0"></i>
                <p class="text-xs font-semibold">Pesanan siap. Sedang menunggu kurir mengambil pesanan ini di dashboard kurir.</p>
            </div>
        @elseif($delivery->status === 'delivering')
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center gap-3 text-indigo-700">
                <i data-lucide="truck" class="w-5 h-5 shrink-0"></i>
                <p class="text-xs font-semibold">Sedang diantar oleh Kurir: <span class="font-black">{{ $delivery->courier->name ?? 'Kurir' }}</span>.</p>
            </div>
        @elseif($delivery->status === 'completed')
            <div class="p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3 text-green-700">
                <i data-lucide="check-check" class="w-5 h-5 shrink-0"></i>
                <p class="text-xs font-semibold">Pesanan telah selesai dan berhasil diterima oleh pasien.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const medInput = document.getElementById('med_name_input');
        const priceInput = document.getElementById('med_price_input');
        const datalist = document.getElementById('medicines_datalist');

        if (medInput && priceInput && datalist) {
            medInput.addEventListener('input', function() {
                const val = this.value.trim().toLowerCase();
                const options = datalist.querySelectorAll('option');
                for (const opt of options) {
                    if (opt.value.trim().toLowerCase() === val) {
                        const price = opt.getAttribute('data-price');
                        if (price && (!priceInput.value || priceInput.value === '0')) {
                            priceInput.value = price;
                        }
                        break;
                    }
                }
            });
        }
    });
</script>
@endpush
