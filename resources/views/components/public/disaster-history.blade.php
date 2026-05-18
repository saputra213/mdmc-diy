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

<div class="min-h-screen bg-slate-50">
    <nav class="bg-blue-900/95 backdrop-blur-md sticky top-0 z-50 border-b border-blue-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <span class="font-extrabold text-lg tracking-tight text-white">MDMC <span class="text-blue-200">DIY</span></span>
            </a>
            <div class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/history-bencana" class="text-white font-extrabold text-sm">History</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-100">Donasi</a>
                <a href="/login" class="px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/15 transition-colors">Login</a>
            </div>
        </div>
    </nav>

    <header class="px-6 lg:px-12 pt-10 pb-8 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-white">History Bencana</h1>
            <p class="text-blue-100/90 mt-3 max-w-2xl">Rekam jejak event yang telah selesai. Ringkasan ini membantu publik melihat jumlah permintaan dan distribusi.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <a href="/history-bencana/{{ $event->id }}" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow block">
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
</div>
