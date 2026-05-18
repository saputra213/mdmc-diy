<?php
use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:80')]
    public $news_title;

    #[Validate('required|string|min:10|max:240')]
    public $news_excerpt;

    public $news_posts = [];
    public $popular_posts = [];

    public function mount()
    {
        $this->news_title = Setting::get('guest.news_title', 'Berita & Informasi');
        $this->news_excerpt = Setting::get('guest.news_excerpt', 'Update terkini seputar kebencanaan dan kegiatan lapangan.');

        $posts = json_decode(Setting::get('guest.news_posts', ''), true);
        $this->news_posts = is_array($posts) && count($posts) ? $posts : [
            ['title' => 'Update Posko Bantul: Kebutuhan Mendesak', 'date' => now()->subDays(1)->format('Y-m-d'), 'category' => 'Berita Terkini', 'img' => 'https://images.unsplash.com/photo-1520975916090-3105956dac38?w=1200&q=80'],
            ['title' => 'Mitigasi Banjir: Panduan Singkat untuk Warga', 'date' => now()->subDays(2)->format('Y-m-d'), 'category' => 'Mitigasi', 'img' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=1200&q=80'],
            ['title' => 'Relawan: Prosedur Penerimaan & Distribusi Barang', 'date' => now()->subDays(3)->format('Y-m-d'), 'category' => 'Info Logistik', 'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80'],
        ];

        $popular = json_decode(Setting::get('guest.popular_posts', ''), true);
        $this->popular_posts = is_array($popular) && count($popular) ? $popular : [
            ['title' => 'Panduan Darurat: Nomor Penting DIY', 'date' => now()->subDays(10)->format('Y-m-d')],
            ['title' => 'Tata Cara Request Bantuan yang Benar', 'date' => now()->subDays(9)->format('Y-m-d')],
            ['title' => 'FAQ Distribusi Logistik untuk Relawan', 'date' => now()->subDays(8)->format('Y-m-d')],
        ];
    }

    public function addPost()
    {
        $this->news_posts[] = ['title' => '', 'date' => now()->format('Y-m-d'), 'category' => 'Berita', 'img' => ''];
    }

    public function removePost($index)
    {
        unset($this->news_posts[$index]);
        $this->news_posts = array_values($this->news_posts);
    }

    public function addPopular()
    {
        $this->popular_posts[] = ['title' => '', 'date' => now()->format('Y-m-d')];
    }

    public function removePopular($index)
    {
        unset($this->popular_posts[$index]);
        $this->popular_posts = array_values($this->popular_posts);
    }

    public function save()
    {
        $this->validate();

        $posts = [];
        foreach ($this->news_posts as $p) {
            $title = trim((string) ($p['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $posts[] = [
                'title' => $title,
                'date' => trim((string) ($p['date'] ?? '')),
                'category' => trim((string) ($p['category'] ?? 'Berita')) ?: 'Berita',
                'img' => trim((string) ($p['img'] ?? '')),
            ];
        }

        $popular = [];
        foreach ($this->popular_posts as $p) {
            $title = trim((string) ($p['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $popular[] = [
                'title' => $title,
                'date' => trim((string) ($p['date'] ?? '')),
            ];
        }

        Setting::set('guest.news_title', $this->news_title, 'string');
        Setting::set('guest.news_excerpt', $this->news_excerpt, 'text');
        Setting::set('guest.news_posts', json_encode($posts, JSON_UNESCAPED_UNICODE), 'json');
        Setting::set('guest.popular_posts', json_encode($popular, JSON_UNESCAPED_UNICODE), 'json');

        $this->dispatch('visual-feedback', message: 'Pengaturan Berita berhasil disimpan!');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Berita (Guest)</h1>
            <p class="text-slate-500 text-sm">Atur judul, ringkasan, berita terbaru, dan berita populer.</p>
        </div>
        <a href="/berita" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Berita
        </a>
    </div>

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul Berita</label>
                    <input wire:model="news_title" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    @error('news_title') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Ringkasan</label>
                    <textarea wire:model="news_excerpt" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                    @error('news_excerpt') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900">Berita Populer</p>
                        <p class="text-sm text-slate-500">Tampil di sidebar Beranda dan Berita.</p>
                    </div>
                    <button type="button" wire:click="addPopular" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-2 rounded-xl font-bold text-xs">Tambah</button>
                </div>
                <div class="space-y-3">
                    @foreach($popular_posts as $i => $post)
                        <div class="bg-white rounded-3xl border border-slate-200 p-5 space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Populer {{ $i + 1 }}</p>
                                <button type="button" wire:click="removePopular({{ $i }})" class="text-red-600 font-bold">Hapus</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal</label>
                                    <input wire:model="popular_posts.{{ $i }}.date" type="date" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
                                    <input wire:model="popular_posts.{{ $i }}.title" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-slate-900">Daftar Berita</p>
                    <p class="text-sm text-slate-500">Tampil di halaman Berita dan highlight Beranda.</p>
                </div>
                <button type="button" wire:click="addPost" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-2 rounded-xl font-bold text-xs">Tambah</button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($news_posts as $i => $post)
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-3">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Berita {{ $i + 1 }}</p>
                            <button type="button" wire:click="removePost({{ $i }})" class="text-red-600 font-bold">Hapus</button>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
                            <input wire:model="news_posts.{{ $i }}.title" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                                <input wire:model="news_posts.{{ $i }}.category" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal</label>
                                <input wire:model="news_posts.{{ $i }}.date" type="date" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">URL Gambar</label>
                            <input wire:model="news_posts.{{ $i }}.img" type="text" placeholder="https://..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Pengaturan
        </button>
    </form>
</div>
