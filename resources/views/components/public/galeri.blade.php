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
                <a href="/galeri" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">Galeri</a>
                <a href="/donasi" class="text-blue-100 hover:text-white font-bold text-sm">Donasi</a>
                <a href="/video" class="text-blue-100 hover:text-white font-bold text-sm">Video</a>
                <a href="/belajar-bisindo" class="text-blue-100 hover:text-white font-bold text-sm">BISINDO</a>
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
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">Galeri</a>
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Donasi</a>
                    <a href="/video" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Video</a>
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
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Galeri</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">{{ $title }}</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">{{ $excerpt }}</p>
            @if($event)
                <div class="mt-6 inline-flex items-center gap-2 bg-white/10 border border-white/10 px-5 py-2.5 rounded-2xl">
                    <span class="text-xs font-extrabold text-blue-100/80 uppercase tracking-widest">Folder</span>
                    <span class="text-sm font-extrabold text-white">{{ $event->name }}</span>
                    <a href="/galeri" class="ml-2 text-xs font-extrabold text-[#4DA8FF] hover:text-white uppercase tracking-widest">Kembali</a>
                </div>
            @endif
        </div>
    </header>

    @if(!$event)
        <section class="px-6 lg:px-12 py-10">
            <div class="max-w-[1280px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $ev)
                    @php
                        $cover = $ev->photos->first();
                        $coverSrc = $cover
                            ? (str_starts_with($cover->image_path, 'http://') || str_starts_with($cover->image_path, 'https://') ? $cover->image_path : asset($cover->image_path))
                            : null;
                    @endphp
                    <a href="/galeri/{{ $ev->id }}" class="group bg-[#F6F9FF] rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-all">
                        <div class="aspect-[16/10] bg-slate-100 overflow-hidden">
                            @if($coverSrc)
                                <img src="{{ $coverSrc }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="">
                            @endif
                        </div>
                        <div class="p-6 bg-white">
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
        <section class="px-6 lg:px-12 py-10" x-data="{ open: false, src: '', desc: '' }">
            <div class="max-w-[1280px] mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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

            <div x-show="open" x-cloak class="fixed inset-0 z-[9999] bg-[#0B1635]/70 flex items-center justify-center p-4" @click.self="open = false">
                <div class="bg-white w-full max-w-3xl rounded-3xl overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <p class="font-extrabold text-slate-900">Detail Foto</p>
                        <button type="button" class="p-2 rounded-xl hover:bg-slate-100" @click="open = false" aria-label="Tutup">×</button>
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
