@extends('layouts.app')
@section('title', 'Profil - SIFANTAR')
@section('content')
<!-- Header / Hero -->
    <div class="bg-gradient-to-br from-primary-orange to-orange-400 rounded-b-[40px] px-6 pt-12 pb-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-white/5 opacity-50 mix-blend-overlay"></div>
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center text-white shadow-2xl border border-white/30">
                <i data-lucide="smile" class="w-8 h-8"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-white leading-tight">Profil Anda</h2>
                <p class="text-[10px] text-white opacity-80 font-black uppercase tracking-[0.2em]">Pusat Kendali Akun</p>
            </div>
        </div>

        <div class="absolute bottom-0 left-6 right-6 translate-y-1/2 bg-white rounded-3xl p-2.5 flex items-center gap-4 shadow-2xl shadow-gray-200">
            <div class="h-16 w-full bg-primary-orange/90 rounded-2xl flex items-center p-3 gap-4 border border-orange-300">
                <div class="w-12 h-12 bg-gray-100 rounded-full border-2 border-white/50 flex items-center justify-center overflow-hidden shadow-inner">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Budi" alt="Profile" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-white text-base leading-none mb-1">Budi Santoso</h3>
                    <div class="flex items-center gap-1 opacity-80">
                        <i data-lucide="award" class="w-3 h-3 text-white"></i>
                        <span class="text-[9px] text-white font-bold uppercase tracking-widest">Pasien Prioritas</span>
                    </div>
                </div>
                <button class="p-2 text-white bg-white/20 rounded-xl">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <div class="mt-20 px-6 space-y-4">
        <div class="card-sifantar py-4 px-2 divide-y divide-gray-50">
            <a href="{{ route('page', 'patient-data') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all group active:scale-[0.98]">
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all shadow-sm">
                    <i data-lucide="user-round" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-left">
                    <h4 class="text-sm font-black text-gray-800 leading-none mb-1.5">Data Pasien</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Detail Personal & Alamat</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300"></i>
            </a>

            <a href="{{ route('page', 'history') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all group active:scale-[0.98]">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-left">
                    <h4 class="text-sm font-black text-gray-800 leading-none mb-1.5">Semua Pesanan</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Riwayat Belanja Obat</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300"></i>
            </a>

            <div class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition-all group cursor-pointer active:scale-[0.98]">
                <div class="w-11 h-11 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 group-hover:scale-110 group-hover:bg-yellow-500 group-hover:text-white transition-all shadow-sm">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-left">
                    <h4 class="text-sm font-black text-gray-800 leading-none mb-1.5">Pengaturan</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Privasi & Keamanan</p>
                </div>
                <span class="px-2 py-0.5 bg-red-100 text-red-500 text-[8px] font-black rounded uppercase mr-2 tracking-widest">Update</span>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300"></i>
            </div>
        </div>

        <!-- Danger Zone -->
        <a href="{{ route('page', 'index') }}" class="w-full flex items-center gap-4 p-5 bg-red-50 hover:bg-red-100 rounded-3xl transition-all active:scale-[0.98] mt-8 group border border-red-100 shadow-sm">
            <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm border border-red-50 group-hover:scale-110 transition-transform">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 text-left">
                <h4 class="text-sm font-black text-red-600 leading-none mb-1">Log out</h4>
                <p class="text-[9px] text-red-400 font-black uppercase tracking-widest">Sesi anda akan diakhiri</p>
            </div>
            <i data-lucide="arrow-right" class="w-5 h-5 text-red-300"></i>
        </a>
    </div>

    <!-- Bottom Navigation -->
@endsection
