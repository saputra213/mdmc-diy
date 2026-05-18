<?php
use Livewire\Component;
use App\Models\Video;

new class extends Component {
    public function with()
    {
        return [
            'videos' => Video::query()->where('is_active', true)->latest()->paginate(12),
        ];
    }
};
?>

<div class="min-h-screen bg-slate-50 px-6 lg:px-12 py-12">
    <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex items-end justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Video</p>
                <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-slate-900">Video Edukasi & Mitigasi</h1>
                <p class="mt-2 text-slate-500">Pilih video untuk melihat pemutar dengan inset BISINDO.</p>
            </div>
            <a href="/" class="text-sm font-bold text-blue-600 hover:text-blue-700">Kembali</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($videos as $video)
                <a href="/video/{{ $video->id }}" wire:navigate class="group bg-white rounded-3xl border border-slate-100 p-6 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Views</span>
                        <span class="text-xs font-mono text-slate-500">{{ number_format($video->views_count) }}</span>
                    </div>
                    <h2 class="mt-3 text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $video->judul }}</h2>
                    <p class="mt-2 text-sm text-slate-500 line-clamp-3">{{ $video->deskripsi }}</p>
                    <div class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest">Buka Video</div>
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6">
            {{ $videos->links() }}
        </div>
    </div>
</div>
