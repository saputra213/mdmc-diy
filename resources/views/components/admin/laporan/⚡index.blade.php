<?php
use Livewire\Component;

new class extends Component {
    public $jenis_laporan = 'masuk';
    public $tgl_mulai;
    public $tgl_selesai;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_selesai = date('Y-m-d');
    }

    public function cetak() {
        $url = route('admin.laporan.print', [
            'jenis' => $this->jenis_laporan,
            'mulai' => $this->tgl_mulai,
            'selesai' => $this->tgl_selesai,
            'event_id' => session('admin_disaster_event_id'),
        ]);
        
        $this->dispatch('open-print-window', url: $url);
    }
};
?>
<div class="space-y-6" x-data="{ 
    openPrint(url) {
        const win = window.open(url, '_blank');
        win.focus();
    }
}" @open-print-window.window="openPrint($event.detail.url)">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Cetak Laporan</h1>
        <p class="text-slate-500 text-sm">Generate laporan logistik dalam format PDF (via Print Browser).</p>
    </div>

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-6">
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih Jenis Laporan</label>
            <div class="grid grid-cols-3 gap-4">
                <button wire:click="$set('jenis_laporan', 'masuk')" @class(['px-4 py-3 rounded-xl border font-bold text-sm transition-all', 'bg-emerald-50 border-emerald-200 text-emerald-600' => $jenis_laporan === 'masuk', 'bg-white border-slate-100 text-slate-400 hover:bg-slate-50' => $jenis_laporan !== 'masuk'])>
                    Logistik Masuk
                </button>
                <button wire:click="$set('jenis_laporan', 'keluar')" @class(['px-4 py-3 rounded-xl border font-bold text-sm transition-all', 'bg-red-50 border-red-200 text-red-600' => $jenis_laporan === 'keluar', 'bg-white border-slate-100 text-slate-400 hover:bg-slate-50' => $jenis_laporan !== 'keluar'])>
                    Logistik Keluar
                </button>
                <button wire:click="$set('jenis_laporan', 'stok')" @class(['px-4 py-3 rounded-xl border font-bold text-sm transition-all', 'bg-blue-50 border-blue-200 text-blue-600' => $jenis_laporan === 'stok', 'bg-white border-slate-100 text-slate-400 hover:bg-slate-50' => $jenis_laporan !== 'stok'])>
                    Stok Logistik
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</label>
                <input type="date" wire:model="tgl_mulai" class="w-full px-4 py-3 rounded-xl border border-slate-100 focus:ring-2 focus:ring-red-500 outline-none transition-all text-sm">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</label>
                <input type="date" wire:model="tgl_selesai" class="w-full px-4 py-3 rounded-xl border border-slate-100 focus:ring-2 focus:ring-red-500 outline-none transition-all text-sm">
            </div>
        </div>

        <button wire:click="cetak" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Buka Pratinjau Cetak
        </button>
    </div>
</div>
