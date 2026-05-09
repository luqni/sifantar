@extends('layouts.app')
@section('title', 'Notifikasi - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center gap-3 border-b border-gray-50">
        <a href="{{ route('page', 'home') }}" class="p-1">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-black text-gray-800">Notifikasi</h1>
    </header>

    <div class="px-6 pt-6 space-y-8">
        <!-- New Notifications -->
        <div>
            <h3 class="text-gray-400 font-black mb-4 uppercase text-[10px] tracking-widest">Notifikasi Terbaru</h3>
            <div class="card-sifantar py-4 px-0 divide-y divide-gray-50 overflow-hidden">
                <div class="px-5 py-4 flex items-start gap-4 active:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-yellow-400 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-yellow-100">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-800 flex items-center gap-2 mb-1">
                            <span>🚚</span> Obat Sedang Dikirim
                        </p>
                        <p class="text-xs text-gray-500 leading-relaxed mb-2 font-medium">Pesanan obat #SFT-2026-0215 sedang dalam perjalanan menuju alamat Anda.</p>
                        <p class="text-[10px] text-gray-400 font-black">10:15 WIB</p>
                    </div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"></div>
                </div>

                <div class="px-5 py-4 flex items-start gap-4 active:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-cyan-500 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-cyan-100">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-800 flex items-center gap-2 mb-1">
                            <span>💬</span> Pesan dari Farmasi
                        </p>
                        <p class="text-xs text-gray-500 leading-relaxed mb-2 font-medium italic">"Mohon pastikan nomor HP aktif agar kurir mudah menghubungi."</p>
                        <p class="text-[10px] text-gray-400 font-black">09:40 WIB</p>
                    </div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"></div>
                </div>
            </div>
        </div>

        <!-- Yesterday -->
        <div>
            <h3 class="text-gray-400 font-black mb-4 uppercase text-[10px] tracking-widest">Kemarin</h3>
            <div class="card-sifantar py-4 px-0 divide-y divide-gray-50 overflow-hidden opacity-90">
                <div class="px-5 py-4 flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary-green rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-green-100">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-800 flex items-center gap-2 mb-1">
                            <span>✅</span> Pembayaran Berhasil
                        </p>
                        <p class="text-xs text-gray-500 leading-relaxed mb-2 font-medium">Pembayaran untuk pesanan #SFT-2026-0214 telah dikonfirmasi.</p>
                        <p class="text-[10px] text-gray-400 font-black">17:22 WIB</p>
                    </div>
                </div>

                <div class="px-5 py-4 flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary-orange rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-orange-100">
                        <i data-lucide="recipe" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-800 flex items-center gap-2 mb-1">
                            <span>🧾</span> Resep Diterima
                        </p>
                        <p class="text-xs text-gray-500 leading-relaxed mb-2 font-medium">Resep dokter telah diterima dan sedang diproses tim farmasi.</p>
                        <p class="text-[10px] text-gray-400 font-black">10:45 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
@endsection
