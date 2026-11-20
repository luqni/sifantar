@extends('layouts.app')
@section('title', 'Verifikasi Email - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-20 pb-12">
    <div class="flex flex-col items-center mb-8">
        <div class="w-28 h-28 mb-6 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-orange/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-orange">
                <i data-lucide="mail-check" class="w-14 h-14"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Verifikasi Email Anda</h1>
        <p class="text-gray-600 font-medium text-center text-sm">
            Kami telah mengirim tautan verifikasi ke
            <span class="font-black text-gray-800">{{ auth()->user()->email }}</span>.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-6 bg-primary-green/10 border border-primary-green/30 text-primary-green rounded-2xl p-4">
            <p class="text-sm font-bold text-center">Tautan verifikasi baru sudah dikirim ke email Anda.</p>
        </div>
    @endif

    <div class="card-sifantar mb-6">
        <p class="text-xs text-gray-500 font-medium leading-relaxed">
            Periksa kotak masuk (atau folder spam) dan klik tautan verifikasi untuk mengaktifkan akun.
            Tautan tidak menemukan email? Kirim ulang tautan di bawah ini.
        </p>
    </div>

    <form action="{{ route('verification.send') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="btn-primary w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">Kirim Ulang</span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="send" class="w-6 h-6"></i>
            </div>
        </button>
    </form>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full text-center text-gray-400 font-black text-xs uppercase tracking-widest hover:text-red-500 transition-colors py-3">
            Keluar
        </button>
    </form>
</div>
@endsection
