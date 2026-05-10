@extends('layouts.app')
@section('title', 'Manajemen Artikel - SIFANTAR')
@section('content')

<!-- Header Section -->
<div class="bg-white px-6 pt-12 pb-6 sticky top-0 z-30 border-b border-gray-100">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl font-black text-gray-800">Kelola Artikel</h2>
        <button onclick="toggleArticleForm()" class="bg-primary-green text-white p-2 rounded-xl active:scale-95 transition-transform shadow-lg shadow-green-100/50">
            <i data-lucide="plus" id="addIcon" class="w-5 h-5 transition-transform"></i>
        </button>
    </div>
    <p class="text-xs font-medium text-gray-500">Tulis dan bagikan info kesehatan terbaru.</p>
</div>

<!-- Form Tambah Artikel (Hidden by Default) -->
<div id="articleForm" class="hidden px-6 py-6 border-b border-gray-100 bg-gray-50/50">
    <form action="{{ route('admin.articles.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="text-[10px] font-black uppercase text-gray-400 mb-1 block ml-2">Judul Artikel</label>
            <input type="text" name="title" required placeholder="Contoh: Pentingnya Vitamin C" class="w-full bg-white border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:border-primary-green transition-all">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 mb-1 block ml-2">Kategori</label>
                <select name="category" class="w-full bg-white border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:border-primary-green appearance-none">
                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Edukasi Obat">Edukasi Obat</option>
                    <option value="Info Farmasi">Info Farmasi</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 mb-1 block ml-2">Image URL (Optional)</label>
                <input type="url" name="image" placeholder="https://..." class="w-full bg-white border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:border-primary-green transition-all">
            </div>
        </div>
        <div>
            <label class="text-[10px] font-black uppercase text-gray-400 mb-1 block ml-2">Konten Artikel</label>
            <textarea name="content" rows="5" required placeholder="Tulis isi artikel di sini..." class="w-full bg-white border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:border-primary-green transition-all"></textarea>
        </div>
        <button type="submit" class="btn-primary w-full justify-center">
            <span class="font-black uppercase tracking-widest text-xs">Terbitkan Artikel</span>
        </button>
    </form>
</div>

<!-- Content -->
<div class="px-6 py-6 space-y-4 mb-32">
    @if(session('success'))
        <div class="bg-green-50 text-primary-green p-4 rounded-2xl text-xs font-bold mb-4 border border-green-100 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @forelse($articles as $article)
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-4 flex gap-4 items-center">
        <div class="w-20 h-20 rounded-2xl bg-gray-100 shrink-0 overflow-hidden">
            <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 flex flex-col justify-between min-w-0">
            <div>
                <p class="text-[9px] text-primary-orange font-black uppercase tracking-widest mb-1">{{ $article->category }}</p>
                <h3 class="font-black text-gray-800 text-xs leading-tight line-clamp-2">{{ $article->title }}</h3>
            </div>
            <div class="flex justify-between items-center mt-3">
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">{{ $article->created_at->diffForHumans() }}</span>
                <div class="flex gap-2">
                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="text-gray-400 hover:text-blue-500 transition-colors p-2 bg-gray-50 rounded-xl">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    <form action="{{ route('admin.articles.delete', $article->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2 bg-gray-50 rounded-xl">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20 bg-gray-50/50 rounded-[40px] border border-dashed border-gray-200">
        <i data-lucide="file-text" class="w-12 h-12 text-gray-200 mx-auto mb-4"></i>
        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Belum ada artikel</p>
        <p class="text-[10px] text-gray-300 mt-1">Mulai tulis artikel pertama Anda.</p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
    function toggleArticleForm() {
        const form = document.getElementById('articleForm');
        const icon = document.getElementById('addIcon');
        form.classList.toggle('hidden');
        if (form.classList.contains('hidden')) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(45deg)';
            form.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endpush
