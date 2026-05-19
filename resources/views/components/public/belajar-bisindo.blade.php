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

<div class="min-h-screen bg-[#F6F9FF]">
    <nav class="bg-gradient-to-b from-[#0B1B43]/95 via-[#162E67]/95 to-[#233876]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 h-20 flex items-center justify-between" x-data="{ open: false }">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <div class="leading-tight">
                    <p class="font-extrabold text-white text-lg tracking-tight">mdmc <span class="text-blue-200">DIY</span></p>
                    <p class="text-[10px] text-blue-100/80 font-bold uppercase tracking-widest">Muhammadiyah Disaster Management Center</p>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-7">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/donasi" class="text-blue-100 hover:text-white font-bold text-sm">Donasi</a>
                <a href="/video" class="text-blue-100 hover:text-white font-bold text-sm">Video</a>
                <a href="/belajar-bisindo" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">BISINDO</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                {{-- <a href="/donasi" class="inline-flex items-center gap-2 bg-[#1E66FF] hover:bg-[#175AE2] text-white px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all shadow-lg shadow-blue-900/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Donasi Sekarang
                </a> --}}
                <a href="/login" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8m11 10v-2a4 4 0 00-3-3.87"/></svg>
                    Login
                </a>
            </div>

            <button class="lg:hidden p-2 rounded-xl bg-white/10 hover:bg-white/15 transition-colors" @click="open = !open" aria-label="Menu">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div x-show="open" x-cloak class="absolute top-20 left-0 right-0 bg-[#233876] border-b border-white/10 lg:hidden">
                <div class="px-6 py-4 space-y-2">
                    <a href="/" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Beranda</a>
                    <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Profil</a>
                    <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Layanan</a>
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Berita</a>
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Galeri</a>
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Donasi</a>
                    <a href="/video" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Video</a>
                    <a href="/belajar-bisindo" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">BISINDO</a>
                    <div class="pt-2 grid grid-cols-2 gap-2">
                        <a href="/donasi" class="px-4 py-3 rounded-xl font-extrabold text-sm text-white bg-[#1E66FF] text-center">Donasi</a>
                        <a href="/login" class="px-4 py-3 rounded-xl font-bold text-sm text-white bg-white/10 text-center">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63]">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-10">
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Edukasi</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">Belajar Bahasa Isyarat (BISINDO)</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">Materi dalam bentuk gambar/poster untuk belajar BISINDO. Gunakan filter untuk mempermudah pencarian.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 p-6 grid grid-cols-1 lg:grid-cols-12 gap-4 items-end shadow-sm">
                <div class="lg:col-span-6 space-y-2">
                    <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Cari Materi</label>
                    <input wire:model.live.debounce.300ms="q" type="text" placeholder="Contoh: angka, salam, evakuasi..." class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                </div>
                <div class="lg:col-span-3 space-y-2">
                    <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                    <select wire:model.live="kategori" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                        <option value="">Semua</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3 space-y-2">
                    <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Tingkat</label>
                    <select wire:model.live="tingkat" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                        <option value="">Semua</option>
                        @foreach($levels as $l)
                            <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($materials as $m)
                    <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden hover:shadow-md transition-all">
                        <a href="{{ $m->gambar_url }}" target="_blank" class="block">
                            <img src="{{ $m->gambar_url }}" alt="" class="w-full aspect-square object-cover">
                        </a>
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="bg-slate-50 text-slate-700 px-2.5 py-1 rounded-full font-extrabold">{{ $m->kategori }}</span>
                                <span class="bg-[#EAF3FF] border border-[#4DA8FF]/25 text-[#233876] px-2.5 py-1 rounded-full font-extrabold">{{ $m->tingkat }}</span>
                            </div>
                            <h2 class="mt-3 font-extrabold text-slate-900 leading-snug">{{ $m->judul }}</h2>
                            <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $m->deskripsi }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl border border-slate-100 p-10 text-center">
                        <p class="font-extrabold text-slate-900">Belum ada materi.</p>
                        <p class="mt-2 text-slate-500 text-sm font-semibold">Tambahkan materi BISINDO dari Panel Admin.</p>
                    </div>
                @endforelse
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 p-6">
                {{ $materials->links() }}
            </div>
        </div>
    </main>

    <footer class="bg-[#233876] text-white">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <p class="font-extrabold text-xl">MDMC DIY</p>
                <p class="mt-2 text-blue-100/90 font-semibold">Muhammadiyah Disaster Management Center — koordinasi logistik, informasi, dan layanan kebencanaan.</p>
            </div>
            <div class="lg:col-span-3">
                <p class="text-xs font-extrabold text-blue-100/80 uppercase tracking-widest">Kontak</p>
                <div class="mt-3 space-y-2 text-blue-100/90 font-semibold">
                    <p>Yogyakarta, DIY</p>
                    <p>info@mdmcdiy.org</p>
                    <p>+62 812-3456-7890</p>
                </div>
            </div>
            <div class="lg:col-span-3">
                <p class="text-xs font-extrabold text-blue-100/80 uppercase tracking-widest">Tautan Cepat</p>
                <div class="mt-3 grid grid-cols-2 gap-2 text-blue-100/90 font-semibold">
                    <a href="/profil" class="hover:text-white">Profil</a>
                    <a href="/layanan" class="hover:text-white">Layanan</a>
                    <a href="/berita" class="hover:text-white">Berita</a>
                    <a href="/galeri" class="hover:text-white">Galeri</a>
                    <a href="/donasi" class="hover:text-white">Donasi</a>
                    <a href="/video" class="hover:text-white">Video</a>
                </div>
            </div>
            <div class="lg:col-span-2">
                <p class="text-xs font-extrabold text-blue-100/80 uppercase tracking-widest">Aksesibilitas</p>
                <div class="mt-3 space-y-2">
                    <a href="/belajar-bisindo" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/10 px-4 py-3 rounded-2xl font-extrabold text-sm transition-colors">
                        BISINDO
                    </a>
                    <p class="text-blue-100/80 text-sm font-semibold">Gunakan widget aksesibilitas untuk zoom/kontras/typografi.</p>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-6 text-blue-100/80 text-sm font-semibold flex flex-col md:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} MDMC DIY</p>
                <p>Ramah difabel tunarungu • Visual-first</p>
            </div>
        </div>
    </footer>
</div>
