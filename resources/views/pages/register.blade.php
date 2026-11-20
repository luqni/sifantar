@extends('layouts.app')
@section('title', 'Daftar Akun - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
@php
    // Halaman yang sedang aktif: 'pasien' (default) atau 'kurir'.
    // $forceMode dikirim oleh pages/register-kurir agar tampilan terkunci ke kurir.
    $mode = request('role') === 'kurir' || (isset($forceMode) && $forceMode === 'kurir') ? 'kurir' : 'pasien';
@endphp
<div class="px-6 max-w-md mx-auto pt-14 pb-12">
    {{-- Tombol kembali --}}
    <a href="{{ route('page', 'index') }}" class="inline-flex items-center gap-2 text-gray-400 font-bold text-[11px] uppercase tracking-widest mb-8 hover:text-gray-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>

    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20 mb-5 flex items-center justify-center relative">
            <div class="absolute inset-0 {{ $mode === 'kurir' ? 'bg-primary-orange/10' : 'bg-primary-green/10' }} rounded-full animate-pulse"></div>
            <div class="relative z-10 {{ $mode === 'kurir' ? 'text-primary-orange' : 'text-primary-green' }}">
                <i data-lucide="{{ $mode === 'kurir' ? 'bike' : 'user-plus' }}" class="w-10 h-10"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-gray-900 text-center mb-2">Daftar Akun SIFANTAR</h1>
        <p class="text-gray-500 font-medium text-center text-sm">
            {{ $mode === 'kurir' ? 'Gabung jadi mitra kurir pengantar obat' : 'Bergabunglah untuk layanan antar obat terbaik' }}
        </p>
    </div>

    {{-- Pilihan tipe akun --}}
    <div class="bg-gray-50 rounded-2xl p-1.5 flex items-center gap-1.5 mb-8 border border-gray-100">
        <a href="{{ route('page', 'register') }}"
           class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all {{ $mode === 'pasien' ? 'bg-white text-primary-green shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
            <i data-lucide="user" class="w-4 h-4"></i>
            <span>Pasien</span>
        </a>
        <a href="{{ route('page', 'register-kurir') }}"
           class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all {{ $mode === 'kurir' ? 'bg-white text-primary-orange shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
            <i data-lucide="bike" class="w-4 h-4"></i>
            <span>Kurir</span>
        </a>
    </div>

    @if($mode === 'kurir')
        <div class="mb-6 px-4 py-3 rounded-2xl bg-orange-50 border border-orange-100 flex items-start gap-3">
            <i data-lucide="info" class="w-4 h-4 text-primary-orange shrink-0 mt-0.5"></i>
            <p class="text-[10px] font-bold text-orange-700 leading-relaxed uppercase tracking-tight">
                Akun kurir diverifikasi admin sebelum bisa menerima orderan.
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-4 py-3 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <input type="hidden" name="role" value="{{ $mode }}">

        <div class="space-y-4 mb-6">
            <div>
                <div class="input-field">
                    <i data-lucide="user" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Lengkap"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="mail" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="phone" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="No. WhatsApp {{ $mode === 'kurir' ? '(wajib)' : '(opsional)' }}"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                </div>
                @error('phone') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            @if($mode === 'kurir')
                <div>
                    <div class="input-field items-start">
                        <i data-lucide="map-pin" class="text-gray-400 w-5 h-5 shrink-0 mt-0.5"></i>
                        <textarea name="address" rows="2" required placeholder="Alamat domisili / basis operasi kurir"
                                  class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm resize-none">{{ old('address') }}</textarea>
                    </div>
                    @error('address') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="input-field">
                        <i data-lucide="truck" class="text-gray-400 w-5 h-5 shrink-0"></i>
                        <select name="vehicle" class="flex-1 bg-transparent outline-none text-gray-800 font-medium text-sm appearance-none">
                            @foreach(['motor' => 'Motor', 'mobil' => 'Mobil', 'sepeda' => 'Sepeda'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('vehicle', 'motor') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="text-gray-400 w-4 h-4 shrink-0"></i>
                    </div>
                    @error('vehicle') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="input-field">
                        <i data-lucide="credit-card" class="text-gray-400 w-5 h-5 shrink-0"></i>
                        <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate') }}" required placeholder="Plat Nomor Kendaraan (contoh: B 1234 XYZ)"
                               class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm uppercase">
                    </div>
                    @error('vehicle_plate') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="input-field">
                        <i data-lucide="id-card" class="text-gray-400 w-5 h-5 shrink-0"></i>
                        <input type="text" name="identity_number" value="{{ old('identity_number') }}" required placeholder="NIK / No. KTP"
                               class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    </div>
                    @error('identity_number') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <div class="input-field">
                    <i data-lucide="lock" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="password" id="password" name="password" required placeholder="Kata Sandi"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    <button type="button" data-toggle-password="password" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 font-semibold mt-1 ml-2">Minimal 8 karakter.</p>
                @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="input-field">
                    <i data-lucide="shield-check" class="text-gray-400 w-5 h-5 shrink-0"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi Kata Sandi"
                           class="flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 font-medium text-sm">
                    <button type="button" data-toggle-password="password_confirmation" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <label class="flex items-start gap-3 mb-8 px-2 cursor-pointer">
            <input type="checkbox" name="terms" required class="mt-0.5 w-5 h-5 shrink-0 accent-primary-green rounded-md">
            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tight leading-relaxed">
                Saya setuju dengan <a href="#" class="text-primary-green hover:underline">Syarat &amp; Ketentuan</a>
                serta kebijakan privasi SIFANTAR
            </span>
        </label>
        @error('terms') <p class="text-red-500 text-[10px] font-bold mb-4 ml-2">{{ $message }}</p> @enderror

        <button type="submit" class="{{ $mode === 'kurir' ? 'btn-primary-orange' : 'btn-primary' }} w-full group">
            <span class="text-base font-black uppercase tracking-widest pl-4">
                {{ $mode === 'kurir' ? 'Daftar Sebagai Kurir' : 'Daftar Sekarang' }}
            </span>
            <div class="bg-white/20 p-2 rounded-full transform group-hover:translate-x-1 transition-transform">
                <i data-lucide="arrow-right" class="w-6 h-6"></i>
            </div>
        </button>
    </form>

    <div class="mt-10 text-center">
        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Sudah punya akun?</p>
        <a href="{{ route('page', 'index') }}" class="text-primary-green font-black text-sm mt-2 inline-block hover:underline">Masuk ke SIFANTAR</a>
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
