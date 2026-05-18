<?php

use Livewire\Component;
use App\Models\Barang;
use App\Models\DisasterEvent;
use Livewire\WithPagination;

new class extends Component
{
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

    public function with()
    {
        $event = $this->currentEvent();
        $query = Barang::with(['jenis', 'satuan'])->latest();
        if ($event) {
            $query->where('disaster_event_id', $event->id);
        } else {
            $query->whereRaw('1=0');
        }

        return [
            'event' => $event,
            'eventLocked' => !$event || $event->status !== 'active',
            'barangs' => $query->paginate(10),
        ];
    }

    public function delete($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return;
        }

        $event = DisasterEvent::find($barang->disaster_event_id);
        if (!$event || $event->status !== 'active') {
            session()->flash('error', 'Tidak bisa menghapus data karena event sudah diarsipkan atau tidak ditemukan.');
            return;
        }

        $barang->delete();
        session()->flash('message', 'Barang berhasil dihapus.');
    }
};
?>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Data Logistik</h1>
                <p class="text-slate-500">Kelola daftar inventaris logistik.</p>
            </div>
            <a href="/barang/create" @class([
                'bg-mdmc-700 hover:bg-mdmc-800 text-white px-4 py-2 rounded-xl font-medium transition-colors flex items-center gap-2 shadow-sm shadow-mdmc-200',
                'opacity-50 pointer-events-none' => $eventLocked,
            ])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Logistik
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
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nama Barang</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Stok</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Satuan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($barangs as $barang)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-slate-500">{{ $barang->id }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $barang->nama_barang }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                                        {{ $barang->jenis->nama_jenis }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span @class([
                                        'font-bold',
                                        'text-red-600' => $barang->stok <= 5,
                                        'text-slate-900' => $barang->stok > 5,
                                    ])>
                                        {{ $barang->stok }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($barang->stok > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold">Tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold">Habis</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $barang->satuan->nama_satuan }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="/barang/edit/{{ $barang->id }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button wire:click="delete('{{ $barang->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus data ini?" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p class="text-slate-500 font-medium">Belum ada data logistik.</p>
                                        <a href="/barang/create" class="text-mdmc-700 font-medium hover:underline">Tambah data pertama</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $barangs->links() }}
            </div>
        </div>
    </div>
