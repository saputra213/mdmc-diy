<?php
use Livewire\Component;
use App\Models\BarangKeluar;
use App\Models\DisasterEvent;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    private function currentEvent(): ?DisasterEvent
    {
        $selected = session('admin_disaster_event_id');
        if ($selected) {
            return DisasterEvent::find((int) $selected);
        }

        $fallbackId = DisasterEvent::active()->value('id') ?: DisasterEvent::orderByDesc('id')->value('id');
        if ($fallbackId) {
            session(['admin_disaster_event_id' => (int) $fallbackId]);
            return DisasterEvent::find((int) $fallbackId);
        }

        return null;
    }

    public function with() {
        $event = $this->currentEvent();
        $query = BarangKeluar::with(['barang', 'lokasi'])->latest();
        if ($event) {
            $query->where('disaster_event_id', $event->id);
        } else {
            $query->whereRaw('1=0');
        }

        return [
            'event' => $event,
            'eventLocked' => !$event || $event->status !== 'active',
            'keluars' => $query->paginate(10),
        ];
    }

    public function delete(string $id): void
    {
        $keluar = BarangKeluar::with('barang')->find($id);
        if (!$keluar) {
            return;
        }

        $event = DisasterEvent::find($keluar->disaster_event_id);
        if (!$event || $event->status !== 'active') {
            session()->flash('error', 'Tidak bisa menghapus data karena event sudah diarsipkan atau tidak ditemukan.');
            return;
        }

        DB::transaction(function () use ($keluar) {
            if ($keluar->barang) {
                $keluar->barang->increment('stok', (int) $keluar->jumlah_keluar);
            }
            $keluar->delete();
        });

        session()->flash('message', 'Logistik keluar berhasil dihapus dan stok dikembalikan.');
    }
};
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Logistik Keluar</h1>
            <p class="text-slate-500 text-sm">Riwayat pengeluaran logistik ke lokasi bencana.</p>
        </div>
        <a href="/barang-keluar/create" wire:navigate @class([
            'bg-mdmc-700 hover:bg-mdmc-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-mdmc-100 flex items-center gap-2 text-sm',
            'opacity-50 pointer-events-none' => $eventLocked,
        ])>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Barang Keluar
        </a>
    </div>

    @if(!$event)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl font-semibold">
            Belum ada event bencana. Buat event terlebih dahulu di menu Manajemen Bencana.
        </div>
    @elseif($eventLocked)
        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-semibold">
            Event terpilih diarsipkan. Input data baru dinonaktifkan.
        </div>
    @else
        <div class="bg-white border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-semibold shadow-sm">
            Event: <span class="font-extrabold">{{ $event->name }}</span>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl font-semibold">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($keluars as $keluar)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $keluar->tanggal_keluar }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $keluar->barang->nama_barang }}</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $keluar->lokasi->nama_lokasi }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full font-bold">-{{ $keluar->jumlah_keluar }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="delete('{{ $keluar->id }}')" wire:confirm="Yakin ingin menghapus data ini? Stok akan dikembalikan." @class([
                            'text-red-600 hover:text-red-700 font-bold text-xs uppercase',
                            'opacity-50 pointer-events-none' => $eventLocked,
                        ])>Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $keluars->links() }}
        </div>
    </div>
</div>
