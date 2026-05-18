<?php

use Livewire\Component;
use App\Models\Kebutuhan;
use App\Models\DisasterEvent;

new class extends Component
{
    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    public $nama_lokasi;
    public $jumlah_korban;
    public $nama_barang;
    public $butuh_barang;
    public $status = 0;

    public function save()
    {
        $this->validate([
            'nama_lokasi' => 'required|string',
            'jumlah_korban' => 'required|numeric',
            'nama_barang' => 'required|string',
            'butuh_barang' => 'required|numeric',
        ]);

        $eventId = session('admin_disaster_event_id');
        $event = $eventId ? DisasterEvent::find((int) $eventId) : DisasterEvent::active()->first();
        if (!$event) {
            session()->flash('error', 'Buat dan pilih event bencana terlebih dahulu.');
            return;
        }
        if ($event->status !== 'active') {
            session()->flash('error', 'Event terpilih sudah diarsipkan. Pilih event yang Active untuk input data baru.');
            return;
        }

        Kebutuhan::create([
            'disaster_event_id' => $event->id,
            'nama_lokasi' => $this->nama_lokasi,
            'jumlah_korban' => $this->jumlah_korban,
            'nama_barang' => $this->nama_barang,
            'butuh_barang' => $this->butuh_barang,
            'status' => $this->status,
        ]);

        // Visual Feedback for Deaf Users
        $this->dispatch('visual-feedback', message: 'Data Kebutuhan Berhasil Ditambahkan!');

        return $this->redirect('/kebutuhan', navigate: true);
    }
};
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/kebutuhan" wire:navigate class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Kebutuhan</h1>
            <p class="text-slate-500 text-sm">Input kebutuhan logistik baru.</p>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Nama Lokasi</label>
                <input type="text" wire:model="nama_lokasi" placeholder="Contoh: Posko Pengungsian A" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('nama_lokasi') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Jumlah Korban (Jiwa)</label>
                <input type="number" wire:model="jumlah_korban" placeholder="0" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('jumlah_korban') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Barang Dibutuhkan</label>
                <input type="text" wire:model="nama_barang" placeholder="Contoh: Beras, Selimut" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('nama_barang') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Jumlah Dibutuhkan</label>
                <input type="number" wire:model="butuh_barang" placeholder="0" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('butuh_barang') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 ml-1">Status Kebutuhan</label>
            <select wire:model="status" class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                <option value="0">🔴 Menunggu (Belum Terpenuhi)</option>
                <option value="1">🟢 Terpenuhi</option>
            </select>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-100 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Data Kebutuhan
            </button>
        </div>
    </form>
</div>
