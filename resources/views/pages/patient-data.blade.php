@extends('layouts.app')
@section('title', 'Data Pasien - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center gap-3 border-b border-gray-50">
        <a href="{{ route('page', 'profile') }}" class="p-1">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-black text-gray-800 tracking-tight">Data Pasien</h1>
    </header>

    <div class="px-6 py-8 space-y-6">
        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-400 tracking-[0.2em] uppercase ml-1">ID Pasien</label>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 shadow-inner flex items-center justify-between">
                <span class="text-gray-800 font-black">USR001</span>
                <i data-lucide="copy" class="w-4 h-4 text-gray-300"></i>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-400 tracking-[0.2em] uppercase ml-1">Nama Lengkap</label>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 shadow-inner">
                <span class="text-gray-800 font-bold">Budi Santoso</span>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-400 tracking-[0.2em] uppercase ml-1">Email Terdaftar</label>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 shadow-inner flex items-center gap-3">
                <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                <span class="text-gray-800 font-bold">budi@gmail.com</span>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-400 tracking-[0.2em] uppercase ml-1">No. Telepon</label>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 shadow-inner flex items-center gap-3">
                <i data-lucide="phone" class="w-4 h-4 text-gray-400"></i>
                <span class="text-gray-800 font-bold">08123456789</span>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-400 tracking-[0.2em] uppercase ml-1">Alamat Utama</label>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 shadow-inner flex items-start gap-3">
                <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mt-1"></i>
                <span class="text-gray-800 font-bold leading-relaxed">Jl. Kesehatan No. 123, Blok C, Jakarta Selatan, 12345</span>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-24 right-6">
        <button class="w-14 h-14 bg-primary-green rounded-2xl flex items-center justify-center text-white shadow-2xl shadow-green-200 active:scale-90 transition-transform">
            <i data-lucide="edit-3" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Bottom Navigation -->
@endsection
