<?php
use Livewire\Component;
use App\Models\BisindoMaterial;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:120')]
    public $judul;

    #[Validate('required|string|min:2|max:60')]
    public $kategori = 'Umum';

    #[Validate('required|string|min:2|max:30')]
    public $tingkat = 'Dasar';

    #[Validate('required|string|max:255')]
    public $gambar_url;

    #[Validate('nullable|string|max:2000')]
    public $deskripsi;

    public $is_active = true;

    public function save()
    {
        $this->validate();

        BisindoMaterial::create([
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'tingkat' => $this->tingkat,
            'gambar_url' => $this->gambar_url,
            'deskripsi' => $this->deskripsi,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('visual-feedback', message: 'Materi BISINDO berhasil ditambahkan!');
        return redirect('/admin/bisindo');
    }
};
?>

<div class="max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Materi BISINDO</h1>
        <p class="text-slate-500 text-sm">Tambah poster/gambar materi untuk halaman belajar BISINDO.</p>
    </div>

    <form wire:submit="save" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
            <input wire:model="judul" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('judul') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                <input wire:model="kategori" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('kategori') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Tingkat</label>
                <select wire:model="tingkat" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    <option>Dasar</option>
                    <option>Menengah</option>
                    <option>Lanjutan</option>
                </select>
                @error('tingkat') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">URL Gambar/Poster</label>
            <input wire:model="gambar_url" type="text" placeholder="https://..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('gambar_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi (opsional)</label>
            <textarea wire:model="deskripsi" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
            @error('deskripsi') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-900">Status Materi</p>
                <p class="text-sm text-slate-500">Jika nonaktif, tidak tampil di publik.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                <div class="w-12 h-7 bg-slate-200 rounded-full peer peer-checked:bg-red-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-5"></div>
            </label>
        </div>

        <div class="flex gap-3">
            <a href="/admin/bisindo" wire:navigate class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 py-4 rounded-2xl font-bold text-center border border-slate-100">Batal</a>
            <button type="submit" class="flex-1 bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100">Simpan</button>
        </div>
    </form>
</div>
