@extends('layouts.app')
@section('title', 'Detail Artikel - SIFANTAR')
@section('content')
<header class="sticky top-0 bg-white/80 backdrop-blur-md z-40 px-4 py-4 flex items-center justify-between border-b border-gray-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('page', 'articles') }}" class="p-1">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </a>
            <h1 class="text-lg font-black text-gray-800 tracking-tight">Detail Artikel</h1>
        </div>
        <button class="p-1 text-gray-400">
            <i data-lucide="share-2" class="w-6 h-6"></i>
        </button>
    </header>

    <div class="px-0">
        <div class="w-full h-64 bg-gray-100 overflow-hidden relative shadow-inner">
            <img src="https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&auto=format&fit=crop&q=60" alt="Article Image" class="w-full h-full object-cover">
            <div class="absolute top-4 left-4 bg-primary-green p-2 rounded-xl text-white shadow-lg flex items-center justify-center backdrop-blur-sm bg-opacity-90">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
        </div>
        
        <div class="px-6 py-6 border-b border-gray-50">
            <div class="flex items-center gap-2 mb-3">
                <div class="bg-blue-500 rounded-lg p-1.5 flex items-center justify-center text-white shadow-sm">
                    <i data-lucide="stethoscope" class="w-4 h-4"></i>
                </div>
                <span class="text-sm font-black text-blue-500 uppercase tracking-widest italic">EDUKASI OBAT</span>
            </div>
            <h1 class="text-2xl font-black text-gray-800 mb-3 leading-tight tracking-tight">Cara Penggunaan Diskus Inhaler yang Benar</h1>
            <p class="text-gray-500 font-bold mb-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-primary-green rounded-full"></span>
                Tim Farmasi SIFANTAR
            </p>
            <div class="flex items-center gap-2 text-xs text-gray-400 font-black">
                <span>4 mnt dibaca</span>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span>Februari 05, 2026</span>
            </div>
        </div>

        <div class="px-6 py-8">
            <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                <i data-lucide="align-left" class="text-primary-green w-5 h-5"></i>
                Deskripsi & Panduan
            </h3>
            <div class="prose prose-sm max-w-none text-gray-600 font-medium leading-relaxed space-y-4">
                <p>Diskus adalah alat inhaler berbentuk bulat yang digunakan untuk menghirup obat langsung ke paru-paru. Penggunaan yang tepat sangat penting agar obat bekerja maksimal dan mengurangi risiko efek samping.</p>
                
                <div class="bg-bg-base border-l-4 border-primary-orange p-4 rounded-r-2xl italic">
                    "Gunakan setiap hari secara teratur sesuai petunjuk dokter meskipun Anda sedang merasa sehat."
                </div>

                <h4 class="font-black text-gray-800 mt-6 mb-2">Langkah-Langkah Penggunaan:</h4>
                <ol class="list-decimal pl-5 space-y-3">
                    <li><strong class="text-gray-800">Buka Diskus:</strong> Pegang diskus dengan satu tangan, letakkan ibu jari tangan lain pada tempatnya lalu geser penutup hingga terdengar bunyi klik.</li>
                    <li><strong class="text-gray-800">Siapkan Dosis:</strong> Geser tuas menjauhi Anda sampai berbunyi klik. Satu dosis obat kini siap dihirup.</li>
                    <li><strong class="text-gray-800">Buang Napas:</strong> Buang napas sejauh mungkin jangan meniupkan ke dalam alat diskus.</li>
                    <li><strong class="text-gray-800">Hirup Obat:</strong> Letakkan mouth piece di antara gigi dan katupkan bibir secara rapat. Tarik napas secara dalam dan kuat.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
@endsection
