@extends('layouts.app')
@section('title', 'Manajemen Artikel - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-6 sticky top-0 z-30 border-b border-gray-100">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl font-black text-gray-800">Kelola Artikel</h2>
        <button class="bg-primary-green text-white p-2 rounded-xl active:scale-95 transition-transform shadow-lg shadow-green-100/50">
            <i data-lucide="plus" class="w-5 h-5"></i>
        </button>
    </div>
    <p class="text-xs font-medium text-gray-500">Tulis dan bagikan info kesehatan terbaru.</p>
</div>

<!-- Content -->
<div class="px-6 py-6 space-y-4 mb-24">
    @forelse($articles as $article)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex gap-4">
        <div class="w-20 h-20 rounded-xl bg-gray-100 shrink-0 overflow-hidden">
            <img src="{{ $article->image ?? 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300&h=300&fit=crop' }}" alt="Article Image" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 flex flex-col justify-between min-w-0">
            <div>
                <p class="text-[10px] text-primary-orange font-black uppercase tracking-wider mb-1">{{ $article->category }}</p>
                <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2">{{ $article->title }}</h3>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-[10px] text-gray-400 font-medium">{{ $article->created_at->diffForHumans() }}</span>
                <div class="flex gap-2">
                    <button class="text-gray-400 hover:text-blue-500 transition-colors p-1 bg-gray-50 rounded-lg"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                    <button class="text-gray-400 hover:text-red-500 transition-colors p-1 bg-gray-50 rounded-lg"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <p class="text-gray-400 font-bold">Belum ada artikel</p>
    </div>
    @endforelse
</div>

@endsection
