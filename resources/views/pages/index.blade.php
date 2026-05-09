@extends('layouts.app')
@section('title', 'Login - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-20">
    <div class="flex flex-col items-center mb-12">
        <div class="w-32 h-32 mb-6 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-orange/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-orange">
                <i data-lucide="shield-check" class="w-16 h-16"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Ayo Masuk SIFANTAR!</h1>
        <p class="text-gray-600 font-medium text-center text-sm">Sistem Tracking Pengantaran Obat</p>
    </div>

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="space-y-4 mb-8">
            <div class="input-field">
                <i data-lucide="mail" class="text-gray-400 w-5 h-5"></i>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
            </div>
            <div class="input-field">
                <i data-lucide="lock" class="text-gray-400 w-5 h-5"></i>
                <input type="password" name="password" required placeholder="Password" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
                <i data-lucide="eye-off" class="text-gray-400 w-5 h-5 cursor-pointer"></i>
            </div>
            @error('email')
                <p class="text-red-500 text-xs font-bold mt-1 ml-2 italic">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">Masuk</span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </div>
        </button>
    </form>

    <div class="mt-12 text-center">
        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Belum punya akun?</p>
        <a href="{{ route('page', 'register') }}" class="text-primary-orange font-black text-sm mt-2 inline-block hover:underline">Daftar Sekarang</a>
    </div>
</div>
@endsection
