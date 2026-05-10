@extends('layouts.app')
@section('title', 'Daftar Akun - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-16 pb-12">
    <div class="flex flex-col items-center mb-10">
        <div class="w-24 h-24 mb-6 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-green/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-green">
                <i data-lucide="user-plus" class="w-12 h-12"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Daftar Akun SIFANTAR!</h1>
        <p class="text-gray-600 font-medium text-center text-sm">Bergabunglah untuk layanan antar obat terbaik</p>
    </div>

    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="space-y-4 mb-8">
            <div>
                <div class="input-field">
                    <i data-lucide="user" class="text-gray-400 w-5 h-5"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Lengkap" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="mail" class="text-gray-400 w-5 h-5"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="lock" class="text-gray-400 w-5 h-5"></i>
                    <input type="password" name="password" required placeholder="Kata Sandi" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div class="input-field">
                <i data-lucide="shield-check" class="text-gray-400 w-5 h-5"></i>
                <input type="password" name="password_confirmation" required placeholder="Ulangi Kata Sandi" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
            </div>
        </div>

        <div class="flex items-center gap-3 mb-8 px-2">
            <div class="w-5 h-5 rounded-md bg-primary-green flex items-center justify-center text-white shrink-0">
                <i data-lucide="check" class="w-3 h-3"></i>
            </div>
            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">Saya setuju dengan <a href="#" class="text-primary-green">Syarat & Ketentuan</a></span>
        </div>

        <button type="submit" class="btn-primary w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">Daftar Sekarang</span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="arrow-right" class="w-6 h-6"></i>
            </div>
        </button>
    </form>

    <div class="mt-12 text-center">
        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Sudah punya akun?</p>
        <a href="{{ route('page', 'index') }}" class="text-primary-green font-black text-sm mt-2 inline-block hover:underline">Masuk ke SIFANTAR</a>
    </div>
</div>
@endsection
