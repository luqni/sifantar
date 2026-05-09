@extends('layouts.app')
@section('title', 'Register - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="flex flex-col items-center mb-8">
        <div class="w-24 h-24 mb-4">
            <img src="https://api.dicebear.com/7.x/shapes/svg?seed=sifantar" alt="Logo" class="w-full h-full object-contain">
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-1">Daftar Akun SIFANTAR.!</h1>
        <p class="text-gray-600 font-medium text-center text-sm">Sistem Tracking Pengantaran Obat</p>
    </div>

    <div class="space-y-4 mb-6">
        <div class="input-field">
            <i data-lucide="user" class="text-gray-400 w-5 h-5"></i>
            <input type="text" placeholder="Nama Lengkap" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
        </div>
        <div class="input-field">
            <i data-lucide="mail" class="text-gray-400 w-5 h-5"></i>
            <input type="email" placeholder="Email" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
        </div>
        <div class="input-field">
            <i data-lucide="lock" class="text-gray-400 w-5 h-5"></i>
            <input type="password" placeholder="Kata Sandi" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
        </div>
        <div class="input-field">
            <i data-lucide="lock" class="text-gray-400 w-5 h-5"></i>
            <input type="password" placeholder="Ulangi Kata Sandi" class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium">
        </div>
    </div>

    <div class="flex items-center gap-2 mb-8">
        <div class="w-5 h-5 rounded-full bg-primary-green flex items-center justify-center text-white shrink-0">
            <i data-lucide="check" class="w-3 h-3"></i>
        </div>
        <span class="text-xs text-gray-700 font-bold uppercase tracking-tight">Setuju dengan Syarat & Ketentuan</span>
    </div>

    <a href="{{ route('page', 'index') }}" class="btn-primary">
        <span class="text-lg font-bold ml-auto mr-auto pl-6">Daftar</span>
        <div class="bg-white/20 p-2 rounded-full">
            <i data-lucide="user-plus" class="w-6 h-6"></i>
        </div>
    </a>

    <div class="mt-auto text-center">
        <p class="text-gray-600 font-medium text-sm">
            Sudah punya akun? <a href="{{ route('page', 'index') }}" class="text-primary-green font-bold">Login sekarang!</a>
        </p>
    </div>
@endsection
