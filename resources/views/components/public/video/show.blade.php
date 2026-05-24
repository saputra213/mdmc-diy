<?php
use Livewire\Component;
use App\Models\Video;
use App\Models\VideoComment;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

new class extends Component {
    use WithPagination;

    public Video $video;
    public $mainEmbedUrl;
    public $bisindoEmbedUrl;

    #[Validate('nullable|string|max:80')]
    public $nama;

    #[Validate('required|string|min:2|max:1000')]
    public $komentar;

    public function mount($id)
    {
        $this->video = Video::query()->where('is_active', true)->findOrFail($id);
        $this->mainEmbedUrl = $this->normalizeEmbedUrl((string) $this->video->video_embed_url);
        $this->bisindoEmbedUrl = $this->video->bisindo_embed_url ? $this->normalizeEmbedUrl((string) $this->video->bisindo_embed_url) : null;

        $key = 'video_viewed_' . $this->video->id;
        if (!session()->has($key)) {
            $this->video->increment('views_count');
            session()->put($key, true);
            $this->video->refresh();
        }
    }

    private function normalizeEmbedUrl(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        if (str_contains($value, 'youtube.com/embed/') || str_contains($value, 'youtube-nocookie.com/embed/')) {
            return $value;
        }

        $parts = parse_url($value);
        if (!is_array($parts)) {
            return $value;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $query = (string) ($parts['query'] ?? '');

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
            if ($id !== '') {
                return 'https://www.youtube.com/embed/' . $id;
            }
        }

        if ($host === 'www.youtube.com' || $host === 'youtube.com' || $host === 'm.youtube.com') {
            if (str_starts_with($path, '/watch')) {
                parse_str($query, $qs);
                $id = (string) ($qs['v'] ?? '');
                if ($id !== '') {
                    return 'https://www.youtube.com/embed/' . $id;
                }
            }

            if (preg_match('#^/shorts/([^/?]+)#', $path, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1];
            }
        }

        return $value;
    }

    public function addComment()
    {
        $this->validate();

        VideoComment::create([
            'video_id' => $this->video->id,
            'nama' => $this->nama ?: null,
            'komentar' => $this->komentar,
            'is_approved' => true,
        ]);

        $this->reset('komentar');
        $this->resetPage();
        $this->dispatch('visual-feedback', message: 'Komentar berhasil dikirim!');
    }

    public function with()
    {
        return [
            'comments' => VideoComment::query()
                ->where('video_id', $this->video->id)
                ->where('is_approved', true)
                ->latest()
                ->paginate(8),
        ];
    }
};
?>

<div class="min-h-screen bg-[#F6F9FF]" x-data="{
    fullscreen(el) {
        if (!document.fullscreenElement) {
            el.requestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    }
}">
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
                <a href="/video" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">Video</a>
                <a href="/belajar-bisindo" class="text-blue-100 hover:text-white font-bold text-sm">BISINDO</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                <a href="/donasi" class="inline-flex items-center gap-2 bg-[#1E66FF] hover:bg-[#175AE2] text-white px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all shadow-lg shadow-blue-900/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Donasi Sekarang
                </a>
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
                    <a href="/video" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">Video</a>
                    <a href="/belajar-bisindo" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">BISINDO</a>
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
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Detail Video</p>
            <h1 class="mt-2 text-2xl lg:text-4xl font-extrabold text-white">{{ $video->judul }}</h1>
            <p class="mt-3 text-blue-100/90 font-semibold">{{ number_format($video->views_count) }} tayangan</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 p-4">
                    <div class="relative rounded-2xl overflow-hidden bg-black" style="aspect-ratio: 16/9" x-ref="player">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $mainEmbedUrl }}" title="Video Utama" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                        @if($bisindoEmbedUrl)
                            <div class="absolute right-3 bottom-3 w-[38%] max-w-[280px] min-w-[160px] aspect-video rounded-xl overflow-hidden border-2 border-white/70 shadow-2xl">
                                <iframe class="w-full h-full" src="{{ $bisindoEmbedUrl }}" title="Translate BISINDO" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endif

                        <button type="button" @click="fullscreen($refs.player)" class="absolute left-3 bottom-3 bg-white/90 hover:bg-white text-slate-900 px-3 py-2 rounded-xl font-extrabold text-xs">
                            Full Screen
                        </button>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Deskripsi</p>
                        <p class="mt-2 text-slate-600 leading-relaxed">{{ $video->deskripsi }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 p-8 space-y-6">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Komentar</p>
                            <p class="mt-2 text-slate-500 text-sm font-semibold">Tulis komentar untuk diskusi dan masukan.</p>
                        </div>
                        <span class="text-xs font-mono text-slate-400">{{ $comments->total() }} komentar</span>
                    </div>

                    <form wire:submit="addComment" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1 space-y-2">
                                <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Nama (opsional)</label>
                                <input wire:model="nama" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Komentar</label>
                                <textarea wire:model="komentar" rows="3" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all"></textarea>
                                @error('komentar') <span class="text-red-600 text-[10px] font-extrabold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#1E66FF] hover:bg-[#175AE2] text-white px-6 py-3 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/15">
                                Kirim Komentar
                            </button>
                        </div>
                    </form>

                    <div class="space-y-4">
                        @foreach($comments as $c)
                            <div class="rounded-3xl border border-slate-100 bg-slate-50/50 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-extrabold text-slate-900">{{ $c->nama ?: 'Anonim' }}</p>
                                    <p class="text-xs font-mono text-slate-400">{{ $c->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <p class="mt-2 text-slate-600 leading-relaxed">{{ $c->komentar }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        {{ $comments->links() }}
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-4 space-y-6">
                <div class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63] text-white rounded-3xl p-6 shadow-[0_20px_60px_rgba(35,56,118,0.22)]">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-blue-100/80">Aksesibilitas</p>
                    <p class="mt-2 text-xl font-extrabold leading-tight">Video dengan Translate BISINDO</p>
                    <p class="mt-2 text-sm text-blue-100/90 font-semibold">Inset BISINDO tampil di pojok kanan bawah video utama.</p>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Keterangan</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <p><span class="font-extrabold">Full Screen:</span> gunakan tombol Full Screen pada video.</p>
                        <p><span class="font-extrabold">Toolbar Aksesibilitas:</span> gunakan tombol A11Y di pojok bawah untuk grayscale/zoom/typografi.</p>
                        <a href="/video" wire:navigate class="inline-flex items-center gap-2 text-[#3155A6] font-extrabold mt-3">
                            Kembali ke daftar video
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    </div>
                </div>
            </aside>
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
