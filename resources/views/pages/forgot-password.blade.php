@extends('layouts.app')
@section('title', 'Lupa Kata Sandi - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-14 pb-12">
    <a href="{{ route('page', 'index') }}" class="inline-flex items-center gap-2 text-gray-400 font-bold text-[11px] uppercase tracking-widest mb-8 hover:text-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Login</span>
    </a>

    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20 mb-5 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-orange/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-orange">
                <i data-lucide="key-round" class="w-10 h-10"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Lupa Kata Sandi?</h1>
        <p class="text-gray-500 font-medium text-center text-sm leading-relaxed">
            Masukkan email akun SIFANTAR Anda.<br>Kami akan mengirim tautan untuk membuat kata sandi baru.
        </p>
    </div>

    @if(session('status'))
        <div class="mb-6 px-4 py-3 rounded-2xl bg-green-50 border border-green-100 flex items-start gap-3">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
            <p class="text-[11px] font-bold text-green-700 leading-relaxed">{{ session('status') }}</p>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-6">
            <div class="input-field">
                <i data-lucide="mail" class="text-gray-400 w-5 h-5 shrink-0"></i>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email terdaftar"
                       class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
            </div>
            @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary-orange w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">Kirim Tautan</span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="send" class="w-6 h-6"></i>
            </div>
        </button>
    </form>

    <div class="mt-10 px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 flex items-start gap-3">
        <i data-lucide="info" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5"></i>
        <p class="text-[10px] font-bold text-gray-500 leading-relaxed uppercase tracking-tight">
            Tautan berlaku 60 menit. Jika email tidak masuk, hubungi admin apotek untuk reset manual.
        </p>
    </div>
</div>
@endsection
