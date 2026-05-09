@extends('layouts.app')
@section('title', 'Daftar Artikel - SIFANTAR')
@section('content')
<!-- Header -->
    <header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center justify-between border-b border-gray-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="p-1 hover:bg-gray-50 rounded-full transition-colors">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </a>
            <h1 class="text-lg font-black text-gray-800 tracking-tight">Daftar Artikel</h1>
        </div>
        <button class="p-1 text-gray-400">
            <i data-lucide="search" class="w-6 h-6"></i>
        </button>
    </header>

    <div class="px-5 pt-4 pb-24">
        <!-- Search Input -->
        <div class="bg-[#F5F7FA] rounded-2xl p-4 flex items-center gap-3 mb-6 border border-transparent focus-within:border-primary-green transition-all">
            <i data-lucide="search" class="text-gray-400 w-5 h-5"></i>
            <input type="text" placeholder="Search" class="bg-transparent outline-none flex-1 text-sm text-gray-700 font-medium">
        </div>

        <!-- Article Grid -->
        <div class="grid grid-cols-2 gap-4">
            @forelse($articles as $article)
            <a href="{{ route('article.show', $article->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 text-left group active:scale-[0.98] transition-all">
                <div class="h-28 bg-gray-100 relative overflow-hidden">
                    <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/10"></div>
                </div>
                <div class="p-3">
                    <p class="text-[9px] text-primary-orange font-black uppercase mb-1 tracking-widest italic">{{ $article->category }}</p>
                    <h4 class="text-xs font-black text-gray-800 leading-tight mb-2 line-clamp-2 h-8">{{ $article->title }}</h4>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-[9px] text-gray-400 font-bold">{{ $article->created_at->format('M d, Y') }}</p>
                        <i data-lucide="arrow-right" class="w-3 h-3 text-primary-green opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-2 text-center py-20">
                <p class="text-gray-400 font-bold italic">Belum ada artikel tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
@endsection
