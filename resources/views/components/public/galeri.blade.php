<?php
use Livewire\Component;
use App\Models\DisasterEvent;
use App\Models\DisasterEventPhoto;
use App\Models\Setting;

new class extends Component {
    public $eventId;

    public function mount($eventId = null): void
    {
        if ($eventId) {
            $event = DisasterEvent::findOrFail((int) $eventId);
            $this->eventId = (int) $event->id;
            return;
        }

        $this->eventId = null;
    }

    public function with() {
        $activeEventsCount = DisasterEvent::active()->count();

        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'title' => Setting::get('guest.gallery_title', 'Galeri'),
            'excerpt' => Setting::get('guest.gallery_excerpt', 'Dokumentasi kegiatan koordinasi dan distribusi.'),
            'activeEventsCount' => $activeEventsCount,
            'event' => $this->eventId ? DisasterEvent::find($this->eventId) : null,
            'events' => DisasterEvent::query()
                ->withCount('photos')
                ->with(['photos' => fn ($q) => $q->orderByDesc('id')->take(1)])
                ->orderByDesc('updated_at')
                ->get(),
            'photos' => $this->eventId
                ? DisasterEventPhoto::query()->where('disaster_event_id', $this->eventId)->orderByDesc('id')->get()
                : collect(),
        ];
    }
};
?>

<div class="min-h-screen bg-slate-50">
    <div class="bg-slate-900 text-white text-xs">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-2 flex items-center justify-between" x-data="{ now: new Date(), tick() { this.now = new Date() } }" x-init="setInterval(() => tick(), 1000)">
            <div class="flex items-center gap-3 text-slate-300">
                <span class="font-bold tracking-widest uppercase">Info</span>
                <span class="opacity-70">•</span>
                <span x-text="now.toLocaleString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: '2-digit' })"></span>
                <span class="opacity-70">•</span>
                <span class="font-mono" x-text="now.toLocaleTimeString('id-ID')"></span>
            </div>
            <div class="flex items-center gap-3">
                <a href="/login" class="text-slate-300 hover:text-white transition-colors font-bold uppercase tracking-widest text-[10px]">Login</a>
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg font-bold uppercase tracking-widest text-[10px]">Donasi</a>
                @if($emergencyMode && $activeEventsCount > 0)
                    <a href="/request-bantuan" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg font-bold uppercase tracking-widest text-[10px]">Minta Bantuan</a>
                @endif
            </div>
        </div>
    </div>

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between" x-data="{ open: false }">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <span class="font-extrabold text-lg tracking-tight text-slate-900">MDMC <span class="text-blue-600">DIY</span></span>
            </a>

            <div class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-slate-900 font-extrabold text-sm">Galeri</a>
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-extrabold text-sm transition-all shadow-sm shadow-emerald-100">Donasi</a>
            </div>

            <button class="lg:hidden p-2 rounded-xl hover:bg-slate-50" @click="open = !open" aria-label="Menu">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div x-show="open" x-cloak class="absolute top-20 left-0 right-0 bg-white border-b border-slate-100 lg:hidden">
                <div class="px-6 py-4 space-y-2">
                    <a href="/" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Beranda</a>
                    <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Profil</a>
                    <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Layanan</a>
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Berita</a>
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-slate-900 bg-slate-50">Galeri</a>
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700">Donasi</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="px-6 lg:px-12 py-14 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Galeri</p>
            <h1 class="mt-3 text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">{{ $title }}</h1>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">{{ $excerpt }}</p>
            @if($event)
                <div class="mt-6 inline-flex items-center gap-2 bg-slate-50 border border-slate-100 px-5 py-2.5 rounded-2xl">
                    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-widest">Folder</span>
                    <span class="text-sm font-extrabold text-slate-900">{{ $event->name }}</span>
                    <a href="/galeri" class="ml-2 text-xs font-extrabold text-blue-600 hover:text-blue-700 uppercase tracking-widest">Kembali</a>
                </div>
            @endif
        </div>
    </section>

    @if(!$event)
        <section class="px-6 lg:px-12 py-12">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $ev)
                    @php
                        $cover = $ev->photos->first();
                        $coverSrc = $cover
                            ? (str_starts_with($cover->image_path, 'http://') || str_starts_with($cover->image_path, 'https://') ? $cover->image_path : asset($cover->image_path))
                            : null;
                    @endphp
                    <a href="/galeri/{{ $ev->id }}" class="group bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                        <div class="aspect-[16/10] bg-slate-100 overflow-hidden">
                            @if($coverSrc)
                                <img src="{{ $coverSrc }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="">
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Folder Event</p>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $ev->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500 font-semibold">{{ $ev->location ?: '-' }}</p>
                            <div class="mt-4 inline-flex items-center gap-2 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-full text-xs font-extrabold text-slate-700">
                                {{ (int) $ev->photos_count }} foto
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white border border-slate-100 rounded-3xl p-10 text-center">
                        <p class="text-slate-600 font-semibold">Belum ada foto galeri.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @else
        <section class="px-6 lg:px-12 py-12" x-data="{ open: false, src: '', desc: '' }">
            <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($photos as $photo)
                    @php
                        $src = str_starts_with($photo->image_path, 'http://') || str_starts_with($photo->image_path, 'https://')
                            ? $photo->image_path
                            : asset($photo->image_path);
                    @endphp
                    <button type="button"
                        class="block rounded-3xl overflow-hidden border border-slate-100 bg-white shadow-sm hover:shadow-lg transition-all text-left"
                        @click="open = true; src = @js($src); desc = @js($photo->description)"
                    >
                        <img class="w-full h-full aspect-square object-cover hover:scale-105 transition-transform duration-500" src="{{ $src }}" alt="">
                    </button>
                @empty
                    <div class="col-span-full bg-white border border-slate-100 rounded-3xl p-10 text-center">
                        <p class="text-slate-600 font-semibold">Belum ada foto untuk event ini.</p>
                    </div>
                @endforelse
            </div>

            <div x-show="open" x-cloak class="fixed inset-0 z-[9999] bg-slate-900/70 flex items-center justify-center p-4" @click.self="open = false">
                <div class="bg-white w-full max-w-3xl rounded-3xl overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <p class="font-extrabold text-slate-900">Detail Foto</p>
                        <button type="button" class="p-2 rounded-xl hover:bg-slate-100" @click="open = false">×</button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="bg-slate-100">
                            <img :src="src" class="w-full h-full object-cover" alt="">
                        </div>
                        <div class="p-6 space-y-3">
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Deskripsi</p>
                            <p class="text-slate-700 font-semibold whitespace-pre-line" x-text="desc || '-'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <footer class="py-10 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} MDMC DIY - Muhammadiyah Disaster Management Center</p>
        </div>
    </footer>
</div>
