<?php

use App\Models\DisasterEvent;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function with(): array
    {
        $events = DisasterEvent::query()
            ->where('status', 'archived')
            ->withCount([
                'bantuanRequests as bantuan_requests_count',
                'bantuanRequests as bantuan_approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->withSum('barangMasuks as total_masuk', 'jumlah_masuk')
            ->withSum('barangKeluars as total_keluar', 'jumlah_keluar')
            ->orderByDesc('updated_at')
            ->paginate(9);

        return [
            'events' => $events,
        ];
    }
};

?>

<div class="min-h-screen bg-[#F6F9FF]">
    <nav class="bg-gradient-to-b from-[#0B1B43]/95 via-[#162E67]/95 to-[#233876]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/10" x-data="{ open: false }">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <div class="leading-tight">
                    <p class="font-extrabold text-white text-lg tracking-tight">mdmc <span class="text-blue-200">DIY</span></p>
                    <p class="text-[10px] text-blue-100/80 font-bold uppercase tracking-widest">Muhammadiyah Disaster Management Center</p>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-7">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/donasi" class="text-blue-100 hover:text-white font-bold text-sm">Donasi</a>
                <a href="/video" class="text-blue-100 hover:text-white font-bold text-sm">Video</a>
                <a href="/belajar-bisindo" class="text-blue-100 hover:text-white font-bold text-sm">BISINDO</a>
                <a href="/history-bencana" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">History</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                {{-- <a href="/donasi" class="inline-flex items-center gap-2 bg-[#1E66FF] hover:bg-[#175AE2] text-white px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all shadow-lg shadow-blue-900/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Donasi Sekarang
                </a> --}}
                <a href="/login" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8m11 10v-2a4 4 0 00-3-3.87"/></svg>
                    Login
                </a>
            </div>

            <button class="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/15 transition-colors" @click="open = !open" aria-label="Menu">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div x-show="open" x-cloak class="lg:hidden border-t border-white/10 bg-[#233876]">
            <div class="max-w-[1280px] mx-auto px-6 py-4 space-y-2">
                <a href="/" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Beranda</a>
                <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Profil</a>
                <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Layanan</a>
                <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Berita</a>
                <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Galeri</a>
                <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Donasi</a>
                <a href="/video" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Video</a>
                <a href="/belajar-bisindo" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">BISINDO</a>
                <a href="/history-bencana" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">History</a>
                <div class="pt-2 grid grid-cols-2 gap-2">
                    <a href="/donasi" class="px-4 py-3 rounded-xl font-extrabold text-sm text-white bg-[#1E66FF] text-center">Donasi</a>
                    <a href="/login" class="px-4 py-3 rounded-xl font-bold text-sm text-white bg-white/10 text-center">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63]">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-10">
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">History</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">History Bencana</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">Rekam jejak event yang telah selesai. Ringkasan ini membantu publik melihat jumlah permintaan dan distribusi.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <a href="/history-bencana/{{ $event->id }}" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow block">
                        <div class="p-6 border-b border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Event</p>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900">{{ $event->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500 font-semibold">{{ $event->location ?: '-' }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Permintaan</p>
                                    <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ (int) $event->bantuan_requests_count }}</p>
                                    <p class="text-xs text-slate-500 font-semibold mt-1">Approved: {{ (int) $event->bantuan_approved_count }}</p>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Distribusi</p>
                                    <p class="mt-2 text-sm font-extrabold text-slate-900">Masuk: {{ (int) ($event->total_masuk ?? 0) }}</p>
                                    <p class="mt-1 text-sm font-extrabold text-slate-900">Keluar: {{ (int) ($event->total_keluar ?? 0) }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 font-semibold">Diarsip: {{ optional($event->updated_at)->format('Y-m-d H:i') }}</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-3xl shadow-sm border border-slate-200 p-10 text-center">
                        <p class="text-slate-600 font-semibold">Belum ada history bencana yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $events->links() }}
            </div>
        </div>
    </main>

    <footer class="bg-[#233876] text-white">
        <div class="border-t border-white/10">
            <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-6 text-blue-100/80 text-sm font-semibold flex flex-col md:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} MDMC DIY</p>
                <p>Ramah difabel tunarungu • Visual-first</p>
            </div>
        </div>
    </footer>
</div>
