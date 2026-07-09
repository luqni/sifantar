@extends('layouts.app')
@section('title', 'Manajemen User - SIFANTAR Admin')
@section('content')
<div class="bg-gradient-to-br from-primary-green to-green-500 px-6 pt-12 pb-6 relative overflow-hidden">
    <div class="absolute inset-0 bg-white/5 opacity-50 mix-blend-overlay"></div>
    <div class="flex items-center gap-4 relative z-10">
        <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 bg-white/20 backdrop-blur-xl rounded-xl flex items-center justify-center text-white shadow-sm border border-white/30">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-xl font-black text-white leading-tight">Manajemen User</h2>
            <p class="text-[10px] text-white opacity-80 font-black uppercase tracking-[0.2em]">Data Pengguna Sistem</p>
        </div>
    </div>
</div>

<div class="mt-6 px-6 space-y-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 rounded-2xl p-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="card-sifantar overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest">User</th>
                        <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest">Role</th>
                        <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden shrink-0">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($user->name) }}" alt="Profile" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-[10px] font-medium text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-1 bg-red-100 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-md">Admin</span>
                                @elseif($user->role === 'courier')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-md">Kurir</span>
                                @else
                                    <span class="px-2.5 py-1 bg-green-100 text-green-600 text-[10px] font-black uppercase tracking-widest rounded-md">Pasien</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-primary-orange hover:bg-orange-100 transition-colors">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
