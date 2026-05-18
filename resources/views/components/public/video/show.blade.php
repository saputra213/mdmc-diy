<?php
use Livewire\Component;
use App\Models\Video;
use App\Models\VideoComment;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

new class extends Component {
    use WithPagination;

    public Video $video;

    #[Validate('nullable|string|max:80')]
    public $nama;

    #[Validate('required|string|min:2|max:1000')]
    public $komentar;

    public function mount($id)
    {
        $this->video = Video::query()->where('is_active', true)->findOrFail($id);

        $key = 'video_viewed_' . $this->video->id;
        if (!session()->has($key)) {
            $this->video->increment('views_count');
            session()->put($key, true);
            $this->video->refresh();
        }
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

<div class="min-h-screen bg-slate-50 px-6 lg:px-12 py-10" x-data="{
    fullscreen(el) {
        if (!document.fullscreenElement) {
            el.requestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    }
}">
    <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex items-start justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Video</p>
                <h1 class="mt-2 text-2xl lg:text-3xl font-extrabold text-slate-900 leading-tight">{{ $video->judul }}</h1>
                <div class="mt-2 flex items-center gap-3 text-sm text-slate-500">
                    <span class="font-bold">{{ number_format($video->views_count) }} tayangan</span>
                    <span class="text-slate-300">•</span>
                    <a href="/video" wire:navigate class="font-bold text-blue-600 hover:text-blue-700">Kembali ke daftar video</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 p-4">
                    <div class="relative rounded-2xl overflow-hidden bg-black" style="aspect-ratio: 16/9" x-ref="player">
                        <iframe class="absolute inset-0 w-full h-full" src="{{ $video->video_embed_url }}" title="Video Utama" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                        @if($video->bisindo_embed_url)
                            <div class="absolute right-3 bottom-3 w-[38%] max-w-[280px] min-w-[160px] aspect-video rounded-xl overflow-hidden border-2 border-white/70 shadow-2xl">
                                <iframe class="w-full h-full" src="{{ $video->bisindo_embed_url }}" title="Translate BISINDO" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endif

                        <button type="button" @click="fullscreen($refs.player)" class="absolute left-3 bottom-3 bg-white/90 hover:bg-white text-slate-900 px-3 py-2 rounded-xl font-bold text-xs">
                            Full Screen
                        </button>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Deskripsi</p>
                        <p class="mt-2 text-slate-600 leading-relaxed">{{ $video->deskripsi }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 p-8 space-y-6">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Komentar</p>
                            <p class="mt-2 text-slate-500 text-sm">Tulis komentar untuk diskusi dan masukan.</p>
                        </div>
                        <span class="text-xs font-mono text-slate-400">{{ $comments->total() }} komentar</span>
                    </div>

                    <form wire:submit="addComment" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1 space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama (opsional)</label>
                                <input wire:model="nama" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Komentar</label>
                                <textarea wire:model="komentar" rows="3" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                                @error('komentar') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/10">
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
                <div class="bg-slate-900 text-white rounded-3xl p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Aksesibilitas</p>
                    <p class="mt-2 text-xl font-extrabold leading-tight">Video dengan Translate BISINDO</p>
                    <p class="mt-2 text-sm text-slate-300">Inset BISINDO tampil di pojok kanan bawah video utama.</p>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Keterangan</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        <p><span class="font-bold">Full Screen:</span> gunakan tombol Full Screen pada video.</p>
                        <p><span class="font-bold">Toolbar Aksesibilitas:</span> gunakan tombol A11Y di pojok bawah untuk grayscale/zoom/typografi.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
