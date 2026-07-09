@extends('layouts.app')
@section('title', 'Edit User - SIFANTAR Admin')
@section('content')
<div class="bg-gradient-to-br from-primary-green to-green-500 px-6 pt-12 pb-6 relative overflow-hidden">
    <div class="absolute inset-0 bg-white/5 opacity-50 mix-blend-overlay"></div>
    <div class="flex items-center gap-4 relative z-10">
        <a href="{{ route('admin.users') }}" class="w-10 h-10 bg-white/20 backdrop-blur-xl rounded-xl flex items-center justify-center text-white shadow-sm border border-white/30">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-xl font-black text-white leading-tight">Edit User</h2>
            <p class="text-[10px] text-white opacity-80 font-black uppercase tracking-[0.2em]">{{ $user->name }}</p>
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

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 rounded-2xl p-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card-sifantar space-y-5">
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

        <div>
            <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-2">Role</label>
            <div class="input-field">
                <i data-lucide="shield" class="w-5 h-5 text-gray-400"></i>
                <select name="role" class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-700 outline-none" required>
                    <option value="patient" {{ (old('role', $user->role) === 'patient') ? 'selected' : '' }}>Pasien</option>
                    <option value="courier" {{ (old('role', $user->role) === 'courier') ? 'selected' : '' }}>Kurir</option>
                    <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full btn-primary mt-6">
            <span class="font-bold">Simpan Perubahan</span>
            <i data-lucide="save" class="w-5 h-5"></i>
        </button>
    </form>

    <div class="card-sifantar mt-6 border-red-100">
        <h4 class="text-sm font-black text-red-600 mb-4">Reset Password</h4>
        <p class="text-xs text-gray-500 mb-4">Password user akan direset menjadi default yaitu: <strong class="text-gray-800">password123</strong></p>
        
        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset password user ini?');">
            @csrf
            <button type="submit" class="w-full py-3 px-4 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold text-sm transition-colors flex items-center justify-center gap-2">
                <i data-lucide="key" class="w-4 h-4"></i>
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
