<?php

use App\Models\DisasterEvent;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

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
            ->withSum('barangs as total_stok', 'stok')
            ->orderByDesc('updated_at')
            ->paginate(10);

        return [
            'events' => $events,
        ];
    }

    public function activate(int $id): void
    {
        DisasterEvent::query()->whereKey($id)->update(['status' => 'active']);

        session(['admin_disaster_event_id' => $id]);

        session()->flash('message', 'Event berhasil diaktifkan.');
        $this->dispatch('disaster-event-changed');
    }
};

?>

<div class="space-y-8">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">History Bencana</h1>
            <p class="text-slate-500 text-sm">Daftar event yang telah diarsip. Data bersifat read-only untuk publik.</p>
        </div>
        <a href="/disaster-events" class="bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-xl font-bold transition-all shadow-sm border border-slate-200 text-sm">
            Kembali ke Manajemen
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100">
            <h2 class="text-lg font-extrabold text-slate-900">Event Diarsipkan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-extrabold">
                    <tr>
                        <th class="px-8 py-4">Event</th>
                        <th class="px-8 py-4">Ringkasan</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-8 py-5">
                                <p class="font-extrabold text-slate-900">{{ $event->name }}</p>
                                <p class="text-xs text-slate-500 font-semibold mt-1">{{ $event->location ?: '-' }}</p>
                                <p class="text-[11px] text-slate-400 font-semibold mt-2">Diarsip: {{ optional($event->updated_at)->format('Y-m-d H:i') }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-extrabold">Request: {{ (int) $event->bantuan_requests_count }}</span>
                                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-extrabold">Approved: {{ (int) $event->bantuan_approved_count }}</span>
                                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-extrabold">Masuk: {{ (int) ($event->total_masuk ?? 0) }}</span>
                                    <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-extrabold">Keluar: {{ (int) ($event->total_keluar ?? 0) }}</span>
                                    <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-extrabold">Stok Akhir: {{ (int) ($event->total_stok ?? 0) }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button wire:click="activate({{ $event->id }})" class="bg-mdmc-700 hover:bg-mdmc-800 text-white px-4 py-2 rounded-xl font-extrabold text-xs uppercase tracking-widest shadow-sm shadow-mdmc-100">
                                    Aktifkan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-16 text-center text-slate-500 font-semibold">
                                Belum ada event yang diarsipkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
            {{ $events->links() }}
        </div>
    </div>
</div>
