<?php
use Livewire\Component;
use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;

new class extends Component {
    #[Validate('required')]
    public $barang_id;

    #[Validate('required')]
    public $supplier_id;

    #[Validate('required|numeric|min:1')]
    public $jumlah_masuk;

    #[Validate('required|date')]
    public $tanggal_masuk;

    public function mount() {
        $this->tanggal_masuk = date('Y-m-d');
    }

    public function with() {
        return [
            'barangs' => Barang::all(),
            'suppliers' => Supplier::all(),
        ];
    }

    public function save() {
        $this->validate();

        DB::transaction(function() {
            $id = 'BM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            
            BarangMasuk::create([
                'id' => $id,
                'barang_id' => $this->barang_id,
                'supplier_id' => $this->supplier_id,
                'user_id' => auth()->id() ?? 1, // Fallback for testing
                'jumlah_masuk' => $this->jumlah_masuk,
                'tanggal_masuk' => $this->tanggal_masuk,
            ]);

            $barang = Barang::find($this->barang_id);
            $barang->increment('stok', $this->jumlah_masuk);
        });

        session()->flash('message', 'Logistik masuk berhasil dicatat dan stok diperbarui.');
        return $this->redirect('/barang-masuk', navigate: true);
    }
};
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/barang-masuk" wire:navigate class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Catat Logistik Masuk</h1>
            <p class="text-slate-500 text-sm">Input data barang yang diterima dari donatur.</p>
        </div>
    </div>

    <form wire:submit="save" class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Barang</label>
                <select wire:model="barang_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all appearance-none">
                    <option value="">Pilih Barang...</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }} (Stok: {{ $barang->stok }})</option>
                    @endforeach
                </select>
                @error('barang_id') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Donatur</label>
                <select wire:model="supplier_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all appearance-none">
                    <option value="">Pilih Donatur...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Jumlah Masuk</label>
                <input wire:model="jumlah_masuk" type="number" placeholder="0" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('jumlah_masuk') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal Terima</label>
                <input wire:model="tanggal_masuk" type="date" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('tanggal_masuk') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="pt-4 flex gap-4">
            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-emerald-100 flex items-center justify-center gap-3 group">
                Catat Barang Masuk
                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <a href="/barang-masuk" wire:navigate class="px-8 py-5 border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-colors">Batal</a>
        </div>
    </form>
</div>