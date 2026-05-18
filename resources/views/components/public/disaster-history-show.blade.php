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

<div class="min-h-screen bg-slate-50">
    <nav class="bg-blue-900/95 backdrop-blur-md sticky top-0 z-50 border-b border-blue-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <span class="font-extrabold text-lg tracking-tight text-white">MDMC <span class="text-blue-200">DIY</span></span>
            </a>
            <div class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
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
            <a href="/history-bencana" class="inline-flex items-center gap-2 text-blue-100/80 hover:text-white font-extrabold text-xs uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke History
            </a>
            <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-white">{{ $event->name }}</h1>
            <p class="text-blue-100/90 mt-3 font-semibold">{{ $event->location ?: '-' }}</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Permintaan</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ (int) $totalRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Approved</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ (int) $approvedRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Rejected</p>
                    <p class="mt-2 text-3xl font-extrabold text-rose-700">{{ (int) $rejectedRequests }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Distribusi Masuk</p>
                    <p class="mt-2 text-3xl font-extrabold text-blue-700">{{ (int) $totalMasuk }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Distribusi Keluar</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ (int) $totalKeluar }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
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
</div>
