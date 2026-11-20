@extends('layouts.app')
@section('title', 'Reset Kata Sandi - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
<div class="px-6 max-w-md mx-auto pt-14 pb-12">
    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20 mb-5 flex items-center justify-center relative">
            <div class="absolute inset-0 bg-primary-green/10 rounded-full animate-pulse"></div>
            <div class="relative z-10 text-primary-green">
                <i data-lucide="lock-keyhole" class="w-10 h-10"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Buat Kata Sandi Baru</h1>
        <p class="text-gray-500 font-medium text-center text-sm">Minimal 8 karakter dan mudah Anda ingat.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="space-y-4 mb-8">
            <div>
                <div class="input-field">
                    <i data-lucide="mail" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required readonly placeholder="Email"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="lock" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="password" id="password" name="password" required placeholder="Kata sandi baru"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    <button type="button" data-toggle-password="password" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
                @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="shield-check" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    <button type="button" data-toggle-password="password_confirmation" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">Simpan Kata Sandi</span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="check" class="w-6 h-6"></i>
            </div>
        </button>
    </form>
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
