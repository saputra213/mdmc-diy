<?php

use App\Models\BantuanRequest;
use App\Models\DisasterEvent;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $event;

    public function mount($id): void
    {
        $event = DisasterEvent::findOrFail((int) $id);
        if ($event->status !== 'archived') {
            abort(404);
        }
        $this->event = $event;
    }

    public function with(): array
    {
        $event = $this->event;

        $requests = BantuanRequest::query()
            ->where('disaster_event_id', $event->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return [
            'requests' => $requests,
            'totalRequests' => BantuanRequest::where('disaster_event_id', $event->id)->count(),
            'approvedRequests' => BantuanRequest::where('disaster_event_id', $event->id)->where('status', 'approved')->count(),
            'rejectedRequests' => BantuanRequest::where('disaster_event_id', $event->id)->where('status', 'rejected')->count(),
            'totalMasuk' => (int) ($event->barangMasuks()->sum('jumlah_masuk') ?? 0),
            'totalKeluar' => (int) ($event->barangKeluars()->sum('jumlah_keluar') ?? 0),
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
            <a href="/history-bencana" class="inline-flex items-center gap-2 text-blue-100/80 hover:text-white font-extrabold text-xs uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke History
            </a>
            <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-white">{{ $event->name }}</h1>
            <p class="text-blue-100/90 mt-3 font-semibold">{{ $event->location ?: '-' }}</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Permintaan</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ (int) $totalRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Approved</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ (int) $approvedRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Rejected</p>
                    <p class="mt-2 text-3xl font-extrabold text-rose-700">{{ (int) $rejectedRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Distribusi Masuk</p>
                    <p class="mt-2 text-3xl font-extrabold text-blue-700">{{ (int) $totalMasuk }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Distribusi Keluar</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ (int) $totalKeluar }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100">
                    <h2 class="text-lg font-extrabold text-slate-900">Rekam Jejak Permintaan Bantuan</h2>
                    <p class="text-sm text-slate-500 mt-1">Data event yang sudah diarsip bersifat read-only.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-extrabold">
                            <tr>
                                <th class="px-8 py-4">Pelapor</th>
                                <th class="px-8 py-4">Lokasi</th>
                                <th class="px-8 py-4">Jenis Bantuan</th>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($requests as $r)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-8 py-5">
                                        <p class="font-extrabold text-slate-900">{{ $r->nama_pelapor }}</p>
                                        <p class="text-xs text-slate-400 font-semibold mt-1">{{ $r->no_hp }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-slate-600 font-semibold max-w-md truncate">{{ $r->lokasi_detail }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach((array) $r->jenis_bantuan as $item)
                                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-[11px] font-extrabold">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($r->status === 'approved')
                                            <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-widest">Approved</span>
                                        @elseif($r->status === 'rejected')
                                            <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-widest">Rejected</span>
                                        @else
                                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-widest">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-right text-slate-500 font-semibold">
                                        {{ optional($r->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center text-slate-500 font-semibold">
                                        Belum ada data permintaan bantuan untuk event ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $requests->links() }}
                </div>
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
