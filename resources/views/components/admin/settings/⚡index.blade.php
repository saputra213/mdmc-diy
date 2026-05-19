<?php
use Livewire\Component;
use App\Models\DisasterEvent;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|min:3|max:80')]
    public $hero_headline;

    #[Validate('required|string|min:10|max:240')]
    public $hero_description;

    #[Validate('nullable|string|max:255')]
    public $video_url;

    public $emergency_mode = false;

    #[Validate('nullable|image|max:2048')]
    public $poster;

    public $poster_path;

    #[Validate('nullable|image|max:4096')]
    public $hero_image;

    public $hero_image_path;

    public function mount()
    {
        $this->hero_headline = Setting::get('guest.hero_headline', 'Manajemen Bencana Terpadu untuk Respons Cepat dan Tepat.');
        $this->hero_description = Setting::get('guest.hero_description', 'Pusat informasi dan koordinasi logistik MDMC DIY untuk membantu masyarakat, relawan, dan mitra dalam situasi darurat.');
        $this->video_url = Setting::get('guest.video_url', 'https://www.youtube.com/embed/ysz5S6PUM-U');
        $this->emergency_mode = Setting::get('emergency_mode', 'off') === 'on';
        $this->poster_path = Setting::get('guest.poster_path');
        $this->hero_image_path = Setting::get('guest.hero_image_path');
    }

    private function normalizeVideoUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (Str::contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (preg_match('~v=([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return $url;
    }

    public function save()
    {
        $this->validate();

        if ($this->emergency_mode && !DisasterEvent::active()->exists()) {
            $this->emergency_mode = false;
            session()->flash('error', 'Emergency Mode tidak bisa dinyalakan karena belum ada event bencana yang Active. Buat/aktifkan event terlebih dahulu di menu Manajemen Bencana.');
            return;
        }

        if ($this->hero_image) {
            $dir = public_path('images/hero');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ext = strtolower($this->hero_image->getClientOriginalExtension() ?: 'jpg');
            $filename = 'hero-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $ext;
            $this->hero_image->move($dir, $filename);
            $this->hero_image_path = 'images/hero/' . $filename;
            Setting::set('guest.hero_image_path', $this->hero_image_path, 'string');
        }

        if ($this->poster) {
            $dir = public_path('images/posters');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ext = strtolower($this->poster->getClientOriginalExtension() ?: 'jpg');
            $filename = 'poster-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $ext;
            $this->poster->move($dir, $filename);
            $this->poster_path = 'images/posters/' . $filename;
            Setting::set('guest.poster_path', $this->poster_path, 'string');
        }

        Setting::set('guest.hero_headline', $this->hero_headline, 'string');
        Setting::set('guest.hero_description', $this->hero_description, 'text');
        Setting::set('guest.video_url', $this->normalizeVideoUrl($this->video_url), 'url');
        Setting::set('emergency_mode', $this->emergency_mode ? 'on' : 'off', 'boolean');

        $this->dispatch('visual-feedback', message: 'Pengaturan Beranda berhasil disimpan!');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Beranda (Guest)</h1>
            <p class="text-slate-500 text-sm">Atur headline, deskripsi hero, video mitigasi, dan Mode Bencana.</p>
        </div>
        <a href="/" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Beranda
        </a>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Headline Hero</label>
                <input wire:model="hero_headline" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('hero_headline') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi Hero</label>
                <textarea wire:model="hero_description" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all"></textarea>
                @error('hero_description') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">URL Video Mitigasi (YouTube)</label>
                <input wire:model="video_url" type="text" placeholder="https://youtube.com/watch?v=..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('video_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Boleh link watch, youtu.be, atau embed. Sistem akan normalisasi otomatis.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Hero Image (Background)</label>
                <input wire:model="hero_image" type="file" accept="image/*" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('hero_image') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Untuk background hero landing page. Maksimal 4MB.</p>

                @if($hero_image)
                    <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="p-3 border-b border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Preview Upload</p>
                        </div>
                        <img src="{{ $hero_image->temporaryUrl() }}" class="w-full h-auto" alt="Preview Hero">
                    </div>
                @elseif($hero_image_path)
                    <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hero Saat Ini</p>
                            <a href="{{ asset($hero_image_path) }}" target="_blank" class="text-[10px] font-bold text-mdmc-700 uppercase tracking-widest">Buka</a>
                        </div>
                        <img src="{{ asset($hero_image_path) }}" class="w-full h-auto" alt="Hero Image">
                    </div>
                @endif
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Poster Informasi (Tampil di Beranda)</label>
                <input wire:model="poster" type="file" accept="image/*" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('poster') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Format gambar (JPG/PNG/WebP), maksimal 2MB.</p>

                @if($poster)
                    <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="p-3 border-b border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Preview Upload</p>
                        </div>
                        <img src="{{ $poster->temporaryUrl() }}" class="w-full h-auto" alt="Preview Poster">
                    </div>
                @elseif($poster_path)
                    <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Poster Saat Ini</p>
                            <a href="{{ asset($poster_path) }}" target="_blank" class="text-[10px] font-bold text-mdmc-700 uppercase tracking-widest">Buka</a>
                        </div>
                        <img src="{{ asset($poster_path) }}" class="w-full h-auto" alt="Poster Informasi">
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900">Mode Bencana</p>
                        <p class="text-sm text-slate-500">Jika aktif, tombol “Minta Bantuan” akan tampil untuk publik.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="emergency_mode" class="sr-only peer">
                        <div class="w-12 h-7 bg-slate-200 rounded-full peer peer-checked:bg-mdmc-700 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden">
                <div class="p-4 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Preview Video</p>
                </div>
                <div class="aspect-video bg-black">
                    <iframe class="w-full h-full" src="{{ $video_url }}" title="Video Mitigasi" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>

            <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
