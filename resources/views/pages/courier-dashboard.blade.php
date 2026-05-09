@extends('layouts.app')
@section('title', 'Panel Kurir - SIFANTAR')
@section('content')

<!-- Header/Hero Section -->
<div class="bg-gradient-to-br from-blue-700 to-indigo-800 rounded-b-[40px] px-6 pt-12 pb-24 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="flex justify-between items-start relative z-10">
        <div class="text-white">
            <p class="text-sm opacity-90 mb-1 font-medium">Panel Kurir SIFANTAR</p>
            <h2 class="text-2xl font-black">{{ auth()->user()->name }}</h2>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('page', 'notifications') }}" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full">
                <i data-lucide="bell" class="w-6 h-6"></i>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-white p-2 relative bg-white/20 backdrop-blur-md rounded-full active:scale-95 transition-transform">
                    <i data-lucide="log-out" class="w-6 h-6"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-8 relative z-10">
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-4 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="package" class="mb-2 w-6 h-6"></i>
            <span class="text-[10px] opacity-80 uppercase font-black tracking-widest">Tugas Aktif</span>
            <span class="font-black text-xl mt-1">{{ $myJobs->count() }}</span>
        </div>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-4 flex flex-col items-center text-white border border-white/20">
            <i data-lucide="search" class="mb-2 w-6 h-6"></i>
            <span class="text-[10px] opacity-80 uppercase font-black tracking-widest">Tersedia</span>
            <span class="font-black text-xl mt-1">{{ $availableJobs->count() }}</span>
        </div>
    </div>
</div>

<div class="px-6 -mt-16 relative z-20 pb-32">
    <!-- Active Tasks Section -->
    @if($myJobs->count() > 0)
    <div class="mb-8">
        <h3 class="text-white font-black uppercase text-[10px] tracking-[0.2em] mb-4 ml-2">Sedang Dijalankan</h3>
        <div class="space-y-4">
            @foreach($myJobs as $job)
            <div class="card-sifantar border-l-4 border-l-blue-500 shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="bg-blue-100 text-blue-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block">On Delivery</span>
                        <h4 class="text-gray-800 font-black text-base">#{{ $job->tracking_number }}</h4>
                        <p class="text-[11px] text-gray-500 font-bold mt-1">Pasien: {{ $job->patient->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $job->created_at->format('H:i') }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl flex items-start gap-3 mb-6">
                    <i data-lucide="map-pin" class="w-5 h-5 text-red-400 shrink-0"></i>
                    <p class="text-xs font-medium text-gray-700 leading-relaxed">{{ $job->delivery_address }}</p>
                </div>

                <form action="{{ route('courier.job.complete', $job->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 active:scale-95 transition-transform flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Selesaikan Antaran
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Jobs Section -->
    <div>
        <h3 class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] mb-4 ml-2">Tersedia Untuk Diambil</h3>
        <div class="space-y-4">
            @forelse($availableJobs as $job)
            <div class="card-sifantar group active:border-primary-green transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="bg-orange-100 text-primary-orange text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block">Ready to Pick</span>
                        <h4 class="text-gray-800 font-black text-base">#{{ $job->tracking_number }}</h4>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold">{{ $job->created_at->diffForHumans() }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl flex items-start gap-3 mb-6">
                    <i data-lucide="map-pin" class="w-5 h-5 text-gray-300 shrink-0"></i>
                    <p class="text-xs font-medium text-gray-600 line-clamp-2">{{ $job->delivery_address }}</p>
                </div>

                <form action="{{ route('courier.job.accept', $job->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-primary-green text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 active:scale-95 transition-transform flex items-center justify-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        Ambil Tugas Ini
                    </button>
                </form>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-[32px] border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-gray-200"></i>
                </div>
                <p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Belum ada tugas</p>
                <p class="text-[10px] text-gray-300 mt-1">Harap tunggu admin farmasi memanggil...</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($myJobs->count() > 0)
        // GPS Tracking Logic
        if ("geolocation" in navigator) {
            function updateCourierLocation() {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch("{{ route('courier.location.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(response => response.json())
                    .then(data => console.log("Location Synced"))
                    .catch(error => console.error("Sync Error:", error));
                }, null, { enableHighAccuracy: true });
            }

            updateCourierLocation();
            setInterval(updateCourierLocation, 10000);
        }
        @endif
    });
</script>
@endpush
