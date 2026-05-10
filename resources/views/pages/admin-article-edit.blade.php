@extends('layouts.app')
@section('title', 'Edit Artikel - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-6 sticky top-0 z-30 border-b border-gray-100 flex items-center gap-4">
    <a href="{{ route('admin.articles') }}" class="p-2 bg-gray-50 rounded-xl active:scale-95 transition-transform">
        <i data-lucide="chevron-left" class="w-6 h-6 text-gray-800"></i>
    </a>
    <div>
        <h2 class="text-xl font-black text-gray-800">Edit Artikel</h2>
        <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Update konten edukasi</p>
    </div>
</div>

<div class="px-6 py-8 pb-32">
    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block ml-2">Judul Artikel</label>
            <input type="text" name="title" value="{{ $article->title }}" required class="w-full bg-white border border-gray-100 rounded-3xl p-5 text-sm font-bold outline-none focus:border-primary-green shadow-sm transition-all">
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block ml-2">Kategori</label>
                <div class="relative">
                    <select name="category" class="w-full bg-white border border-gray-100 rounded-3xl p-5 text-sm font-bold outline-none focus:border-primary-green shadow-sm appearance-none cursor-pointer">
                        <option value="Kesehatan" {{ $article->category === 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="Edukasi Obat" {{ $article->category === 'Edukasi Obat' ? 'selected' : '' }}>Edukasi Obat</option>
                        <option value="Info Farmasi" {{ $article->category === 'Info Farmasi' ? 'selected' : '' }}>Info Farmasi</option>
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block ml-2">URL Gambar Baru (Opsional)</label>
                <input type="url" name="image" value="{{ $article->image }}" class="w-full bg-white border border-gray-100 rounded-3xl p-5 text-sm font-bold outline-none focus:border-primary-green shadow-sm transition-all">
                <p class="text-[10px] text-gray-400 mt-2 ml-2 italic">*Kosongkan jika tidak ingin mengubah gambar.</p>
            </div>
        </div>

        <div>
            <label class="text-[10px] font-black uppercase text-gray-400 mb-2 block ml-2">Isi Konten</label>
            <textarea name="content" rows="10" required class="w-full bg-white border border-gray-100 rounded-[32px] p-6 text-sm font-medium leading-relaxed outline-none focus:border-primary-green shadow-sm transition-all">{{ $article->content }}</textarea>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('admin.articles') }}" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-center">Batal</a>
            <button type="submit" class="flex-[2] bg-primary-green text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 active:scale-95 transition-transform flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
