<?php
use Livewire\Component;
use App\Models\BisindoMaterial;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $kategori = '';
    public $tingkat = '';
    public $q = '';

    public function updated($name, $value)
    {
        if (in_array($name, ['kategori', 'tingkat', 'q'], true)) {
            $this->resetPage();
        }
    }

    public function with()
    {
        $base = BisindoMaterial::query()->where('is_active', true);

        $kategori = trim((string) $this->kategori);
        if ($kategori !== '') {
            $base->where('kategori', $kategori);
        }

        $tingkat = trim((string) $this->tingkat);
        if ($tingkat !== '') {
            $base->where('tingkat', $tingkat);
        }

        $q = trim((string) $this->q);
        if ($q !== '') {
            $base->where(function ($qBuilder) use ($q) {
                $qBuilder->where('judul', 'like', '%' . $q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $q . '%');
            });
        }

        return [
            'categories' => BisindoMaterial::query()->where('is_active', true)->distinct()->orderBy('kategori')->pluck('kategori'),
            'levels' => BisindoMaterial::query()->where('is_active', true)->distinct()->orderBy('tingkat')->pluck('tingkat'),
            'materials' => $base->latest()->paginate(12),
        ];
    }
};
?>

<div class="min-h-screen bg-slate-50 px-6 lg:px-12 py-12">
    <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex items-end justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Edukasi</p>
                <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-slate-900">Belajar Bahasa Isyarat (BISINDO)</h1>
                <p class="mt-2 text-slate-500">Materi dalam bentuk gambar/poster untuk belajar BISINDO. Gunakan filter untuk mempermudah pencarian.</p>
            </div>
            <a href="/" wire:navigate class="text-sm font-bold text-blue-600 hover:text-blue-700">Kembali</a>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-6 space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Cari Materi</label>
                <input wire:model.live.debounce.300ms="q" type="text" placeholder="Contoh: angka, salam, evakuasi..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            </div>
            <div class="lg:col-span-3 space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                <select wire:model.live="kategori" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    <option value="">Semua</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3 space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tingkat</label>
                <select wire:model.live="tingkat" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    <option value="">Semua</option>
                    @foreach($levels as $l)
                        <option value="{{ $l }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($materials as $m)
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden hover:shadow-lg transition-all">
                    <a href="{{ $m->gambar_url }}" target="_blank" class="block">
                        <img src="{{ $m->gambar_url }}" alt="" class="w-full aspect-square object-cover">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="bg-slate-50 text-slate-700 px-2.5 py-1 rounded-full font-bold">{{ $m->kategori }}</span>
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-bold">{{ $m->tingkat }}</span>
                        </div>
                        <h2 class="mt-3 font-extrabold text-slate-900 leading-snug">{{ $m->judul }}</h2>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $m->deskripsi }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-slate-100 p-10 text-center">
                    <p class="font-extrabold text-slate-900">Belum ada materi.</p>
                    <p class="mt-2 text-slate-500 text-sm">Tambahkan materi BISINDO dari Panel Admin.</p>
                </div>
            @endforelse
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6">
            {{ $materials->links() }}
        </div>
    </div>
</div>
