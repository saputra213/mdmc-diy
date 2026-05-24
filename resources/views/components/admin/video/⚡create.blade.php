<?php
use Livewire\Component;
use App\Models\Video;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:120')]
    public $judul;

    #[Validate('nullable|string|max:2000')]
    public $deskripsi;

    #[Validate('required|string|max:255')]
    public $video_embed_url;

    #[Validate('nullable|string|max:255')]
    public $bisindo_embed_url;

    public $is_active = true;

    public function save()
    {
        $this->validate();

        $videoEmbedUrl = $this->normalizeEmbedUrl((string) $this->video_embed_url);
        $bisindoEmbedUrl = $this->bisindo_embed_url ? $this->normalizeEmbedUrl((string) $this->bisindo_embed_url) : null;

        Video::create([
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'video_embed_url' => $videoEmbedUrl,
            'bisindo_embed_url' => $bisindoEmbedUrl,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('visual-feedback', message: 'Video berhasil ditambahkan!');
        return redirect('/admin/video');
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
};
?>

<div class="max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Video</h1>
        <p class="text-slate-500 text-sm">Tambahkan video utama dan video translate BISINDO (embed).</p>
    </div>

    <form wire:submit="save" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
            <input wire:model="judul" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('judul') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
            <textarea wire:model="deskripsi" rows="5" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
            @error('deskripsi') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Embed URL Video Utama</label>
            <input wire:model="video_embed_url" type="text" placeholder="https://www.youtube.com/embed/..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('video_embed_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Embed URL Video BISINDO (opsional)</label>
            <input wire:model="bisindo_embed_url" type="text" placeholder="https://www.youtube.com/embed/..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('bisindo_embed_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-900">Status Video</p>
                <p class="text-sm text-slate-500">Jika nonaktif, tidak tampil di publik.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                <div class="w-12 h-7 bg-slate-200 rounded-full peer peer-checked:bg-red-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-5"></div>
            </label>
        </div>

        <div class="flex gap-3">
            <a href="/admin/video" wire:navigate class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 py-4 rounded-2xl font-bold text-center border border-slate-100">Batal</a>
            <button type="submit" class="flex-1 bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100">Simpan</button>
        </div>
    </form>
</div>
