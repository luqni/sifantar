@extends('layouts.app', ['hideNav' => true])
@section('title', 'Edit Profil - SIFANTAR')
@section('content')
<!-- Header -->
<div class="bg-gradient-to-br from-primary-orange to-orange-400 px-6 pt-12 pb-6 relative overflow-hidden">
    <div class="absolute inset-0 bg-white/5 opacity-50 mix-blend-overlay"></div>
    <div class="flex items-center gap-4 relative z-10">
        <a href="{{ route('page', 'profile') }}" class="w-10 h-10 bg-white/20 backdrop-blur-xl rounded-xl flex items-center justify-center text-white shadow-sm border border-white/30">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-xl font-black text-white leading-tight">Edit Profil</h2>
            <p class="text-[10px] text-white opacity-80 font-black uppercase tracking-[0.2em]">Perbarui Data Anda</p>
        </div>
    </div>
</div>

<div class="mt-6 px-6 space-y-4">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 rounded-2xl p-4 text-sm font-medium">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="card-sifantar space-y-5">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Nama Lengkap</label>
            <div class="input-field">
                <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium placeholder-gray-400 outline-none" required>
            </div>
        </div>

        <div>
            <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Email</label>
            <div class="input-field">
                <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium placeholder-gray-400 outline-none" required>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <h4 class="text-sm font-black text-gray-800 mb-4">Ganti Password <span class="text-[10px] text-gray-400 font-medium">(opsional)</span></h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Password Baru</label>
                    <div class="input-field">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium placeholder-gray-400 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                    <div class="input-field">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium placeholder-gray-400 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full btn-primary mt-6">
            <span class="font-bold">Simpan Perubahan</span>
            <i data-lucide="save" class="w-5 h-5"></i>
        </button>
    </form>
</div>
@endsection
