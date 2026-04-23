<?php
use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|min:3')]
    public $nama_supplier;

    #[Validate('required|numeric')]
    public $no_telp;

    #[Validate('required')]
    public $alamat;

    public function save() {
        $this->validate();
        Supplier::create([
            'nama_supplier' => $this->nama_supplier,
            'no_telp' => $this->no_telp,
            'alamat' => $this->alamat,
        ]);
        session()->flash('message', 'Donatur berhasil ditambahkan.');
        return $this->redirect('/donatur', navigate: true);
    }
};
?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/donatur" wire:navigate class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Donatur</h1>
            <p class="text-slate-500 text-sm">Masukkan informasi lengkap donatur/supplier.</p>
        </div>
    </div>

    <form wire:submit="save" class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap Donatur</label>
            <input wire:model="nama_supplier" type="text" placeholder="Contoh: Yayasan Kemanusiaan" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('nama_supplier') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
            <input wire:model="no_telp" type="tel" placeholder="0812..." class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('no_telp') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Alamat Kantor/Pusat</label>
            <textarea wire:model="alamat" rows="3" placeholder="Alamat lengkap..." class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
            @error('alamat') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 flex gap-4">
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-red-100 flex items-center justify-center gap-3 group">
                Simpan Donatur
                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <a href="/donatur" wire:navigate class="px-8 py-5 border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-colors">Batal</a>
        </div>
    </form>
</div>