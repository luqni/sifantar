<nav class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-gray-100 flex justify-around py-3 px-2 z-50">
    <a href="{{ auth()->user() && auth()->user()->isCourier() ? route('courier.dashboard') : (auth()->user() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('home')) }}" class="bottom-nav-item {{ request()->is('home') || request()->routeIs('admin.dashboard') || request()->routeIs('courier.dashboard') ? 'active' : '' }}">
        <i data-lucide="home" class="w-6 h-6"></i>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Home</span>
    </a>
    
    @if(auth()->user() && auth()->user()->isPatient())
        <a href="{{ route('page', 'articles') }}" class="bottom-nav-item {{ request()->is('articles') ? 'active' : '' }}">
            <i data-lucide="file-text" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-tighter">Artikel</span>
        </a>
    @endif
    
    @if(auth()->user() && auth()->user()->isCourier())
        <a href="{{ route('courier.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
            <i data-lucide="truck" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-tighter">Antar</span>
        </a>
    @endif

    @if(auth()->user() && auth()->user()->isAdmin())
        <a href="{{ route('admin.articles') }}" class="bottom-nav-item {{ request()->routeIs('admin.articles') ? 'active' : '' }}">
            <i data-lucide="file-text" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-tighter">Artikel</span>
        </a>
        <a href="{{ route('admin.chat') }}" class="bottom-nav-item {{ request()->routeIs('admin.chat') ? 'active' : '' }} relative">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
            <span class="absolute top-0 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-bold uppercase tracking-tighter">Chat</span>
        </a>
    @else
        <a href="{{ route('history') }}" class="bottom-nav-item {{ request()->routeIs('history') ? 'active' : '' }}">
            <i data-lucide="clipboard-list" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-tighter">Riwayat</span>
        </a>
    @endif

    <a href="{{ route('page', 'profile') }}" class="bottom-nav-item {{ request()->is('profile') ? 'active' : '' }}">
        <i data-lucide="user" class="w-6 h-6"></i>
        <span class="text-[10px] font-bold uppercase tracking-tighter">Profil</span>
    </a>
</nav>