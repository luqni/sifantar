@extends('layouts.app')
@section('title', $article->title . ' - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center justify-between border-b border-gray-50">
    <div class="flex items-center gap-3">
        <a href="{{ route('articles') }}" class="p-1">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-lg font-black text-gray-800 tracking-tight">Detail Artikel</h1>
    </div>
    <button class="p-1 text-gray-400">
        <i data-lucide="share-2" class="w-6 h-6"></i>
    </button>
</header>

<div class="px-0 pb-24">
    <div class="w-full h-72 bg-gray-100 overflow-hidden relative shadow-inner">
        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        <div class="absolute top-4 left-4 bg-primary-green p-2 rounded-xl text-white shadow-lg flex items-center justify-center backdrop-blur-sm bg-opacity-90">
            <i data-lucide="award" class="w-5 h-5"></i>
        </div>
    </div>
    
    <div class="px-6 py-8 border-b border-gray-50 bg-white -mt-8 rounded-t-[40px] relative z-10">
        <div class="flex items-center gap-2 mb-4">
            <div class="bg-blue-500 rounded-lg p-1.5 flex items-center justify-center text-white shadow-sm">
                <i data-lucide="stethoscope" class="w-4 h-4"></i>
            </div>
            <span class="text-sm font-black text-blue-500 uppercase tracking-widest italic">{{ strtoupper($article->category) }}</span>
        </div>
        
        <h1 class="text-2xl font-black text-gray-800 mb-4 leading-tight tracking-tight">{{ $article->title }}</h1>
        
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-orange rounded-full flex items-center justify-center text-white font-black text-xs">
                    SF
                </div>
                <div>
                    <p class="text-sm font-black text-gray-800">{{ $article->author ? $article->author->name : 'Tim Farmasi SIFANTAR' }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $article->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-2 rounded-2xl flex items-center gap-2">
                <i data-lucide="clock" class="w-3 h-3 text-gray-400"></i>
                <span class="text-[10px] text-gray-400 font-black">5 MIN READ</span>
            </div>
        </div>

        <div class="prose prose-sm max-w-none text-gray-600 font-medium leading-relaxed">
            {!! nl2br(e($article->content)) !!}
            <p class="mt-6 italic text-gray-400 text-xs border-l-4 border-primary-green pl-4">
                Disclaimer: Artikel ini hanya bersifat edukasi. Untuk diagnosis medis yang akurat, harap hubungi dokter terdekat atau konsultasi langsung melalui menu chat kami.
            </p>
        </div>
        
        <div class="mt-12 flex items-center justify-between p-6 bg-orange-50 rounded-[32px] border border-orange-100">
            <div>
                <h4 class="text-primary-orange font-black text-sm mb-1">Ada Pertanyaan?</h4>
                <p class="text-[10px] text-orange-400 font-bold">Konsultasi gratis dengan apoteker kami.</p>
            </div>
            <a href="{{ route('chat') }}" class="bg-primary-orange text-white p-3 rounded-2xl shadow-lg shadow-orange-200 active:scale-95 transition-transform">
                <i data-lucide="message-circle" class="w-6 h-6"></i>
            </a>
        </div>
    </div>
</div>
@endsection
