@extends('layouts.app')
@section('title', 'Masuk - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-20 pb-12">
    <div class="flex flex-col items-center mb-10">
        <div class="w-28 h-28 mb-6 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-orange/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-orange">
                <i data-lucide="shield-check" class="w-14 h-14"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Ayo Masuk SIFANTAR!</h1>
        <p class="text-gray-500 font-medium text-center text-sm">Sistem Tracking Pengantaran Obat</p>
    </div>

    @if(session('status'))
        <div class="mb-6 px-4 py-3 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-xs font-bold">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="space-y-4 mb-4">
            <div>
                <div class="input-field">
                    <i data-lucide="mail" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs font-bold mt-1 ml-2 italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="lock" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="password" id="password" name="password" required placeholder="Kata Sandi"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    <button type="button" data-toggle-password="password" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs font-bold mt-1 ml-2 italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="text-right mb-8 px-2">
            <a href="{{ route('password.request') }}" class="text-primary-orange text-xs font-black uppercase tracking-widest hover:underline">
                Lupa Kata Sandi?
            </a>
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
        <div class="flex items-center justify-center gap-4 mt-2">
            <a href="{{ route('page', 'register') }}" class="text-primary-green font-black text-sm hover:underline">Daftar Pasien</a>
            <span class="text-gray-200">|</span>
            <a href="{{ route('page', 'register-kurir') }}" class="text-primary-orange font-black text-sm hover:underline">Daftar Kurir</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.togglePassword);
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            var icon = btn.querySelector('i');
            if (icon) icon.setAttribute('data-lucide', show ? 'eye' : 'eye-off');
            if (window.lucide) lucide.createIcons();
        });
    });
</script>
@endpush
