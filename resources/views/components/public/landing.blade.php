<?php
use Livewire\Component;
use App\Models\BantuanRequest;
use App\Models\Barang;
use App\Models\BisindoMaterial;
use App\Models\DisasterEvent;
use App\Models\DisasterEventPhoto;
use App\Models\Kebutuhan;
use App\Models\Lokasi;
use App\Models\Setting;
use App\Models\Video;

new class extends Component {
    public $selectedEventId;

    public function mount(): void
    {
        $first = DisasterEvent::active()->orderByDesc('updated_at')->value('id');
        $this->selectedEventId = $first ? (int) $first : null;
    }

    public function updatedSelectedEventId($value): void
    {
        $this->selectedEventId = $value ? (int) $value : null;
    }

    private function heroImageUrl(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1920&q=80';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset($value);
    }

    public function with(): array
    {
        $emergencyMode = Setting::get('emergency_mode', 'off') === 'on';
        $events = DisasterEvent::active()->orderByDesc('updated_at')->get();

        $event = null;
        if ($this->selectedEventId) {
            $event = $events->firstWhere('id', (int) $this->selectedEventId) ?: DisasterEvent::find((int) $this->selectedEventId);
        }
        if (!$event) {
            $event = $events->first();
        }

        $stats = [
            'status' => $event ? $event->status : null,
            'impacted_people' => $event ? (int) Kebutuhan::query()->where('disaster_event_id', $event->id)->sum('jumlah_korban') : 0,
            'affected_houses' => $event ? $event->affected_houses : null,
            'logistics_packages' => $event ? (int) Barang::query()->where('disaster_event_id', $event->id)->sum('stok') : 0,
            'posko_active' => (int) Lokasi::query()->count(),
            'requests_count' => $event ? (int) BantuanRequest::query()->where('disaster_event_id', $event->id)->count() : 0,
        ];

        $videos = Video::query()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        $bisindo = BisindoMaterial::query()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        $news = json_decode(Setting::get('guest.news_posts', ''), true);
        if (!is_array($news) || count($news) === 0) {
            $news = [
                ['title' => 'Update Posko Bantul: Kebutuhan Mendesak', 'date' => now()->subDays(1)->format('Y-m-d'), 'category' => 'Berita Terkini', 'img' => 'https://images.unsplash.com/photo-1520975916090-3105956dac38?w=1200&q=80'],
                ['title' => 'Mitigasi Banjir: Panduan Singkat untuk Warga', 'date' => now()->subDays(2)->format('Y-m-d'), 'category' => 'Mitigasi', 'img' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=1200&q=80'],
                ['title' => 'Relawan: Prosedur Penerimaan & Distribusi Barang', 'date' => now()->subDays(3)->format('Y-m-d'), 'category' => 'Info Logistik', 'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80'],
            ];
        }

        $photos = DisasterEventPhoto::query()->latest()->take(6)->get();

        return [
            'emergencyMode' => $emergencyMode,
            'hasActiveEvent' => $events->count() > 0,
            'events' => $events,
            'event' => $event,
            'stats' => $stats,
            'heroHeadline' => Setting::get('guest.hero_headline', 'Sistem Informasi Logistik Kebencanaan Ramah Difabel Tunarungu'),
            'heroDescription' => Setting::get('guest.hero_description', 'Akses informasi kebencanaan yang inklusif, mudah dipahami, dan dapat diakses oleh difabel tunarungu melalui label bahasa isyarat (BISINDO) dan media visual.'),
            'heroImageUrl' => $this->heroImageUrl(Setting::get('guest.hero_image_path')),
            'videos' => $videos,
            'bisindo' => $bisindo,
            'news' => array_slice($news, 0, 3),
            'photos' => $photos,
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
                <a href="/" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
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
                    <a href="/" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">Beranda</a>
                    <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Profil</a>
                    <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Layanan</a>
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Berita</a>
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Galeri</a>
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

    <header class="relative overflow-hidden">
        <div class="absolute inset-0 bg-center bg-cover" style="background-image: url('{{ $heroImageUrl }}');"></div>
        <div class="absolute inset-0 bg-[#0B1635]/70"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0B1635]/90 via-[#0B1635]/75 to-transparent"></div>

        <div class="relative max-w-[1280px] mx-auto px-6 lg:px-12 pt-14 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-6">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight">
                        {{ $heroHeadline }}
                    </h1>
                    <p class="mt-4 text-blue-100/90 text-base lg:text-lg leading-relaxed max-w-2xl">
                        {{ $heroDescription }}
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/10 text-blue-100 px-4 py-2 rounded-full text-xs font-extrabold">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-400/20 inline-flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Ramah Tunarungu
                        </span>
                        <a href="/belajar-bisindo" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/10 text-white px-4 py-2 rounded-full text-xs font-extrabold transition-colors">
                            Belajar BISINDO
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="#bencana-aktif" class="inline-flex items-center justify-center gap-2 bg-[#1E66FF] hover:bg-[#175AE2] text-white px-6 py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/25">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                            Lihat Informasi
                        </a>
                        @if($emergencyMode && $hasActiveEvent)
                            <a href="/request-bantuan" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-red-900/25">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Request Bantuan
                            </a>
                        @else
                            <div class="bg-white/10 border border-white/10 text-blue-100 px-6 py-4 rounded-2xl font-extrabold text-center">
                                Request Bantuan (Nonaktif)
                            </div>
                        @endif
                    </div>
                </div>
                <div class="hidden lg:block lg:col-span-6"></div>
            </div>
        </div>
    </header>

    <section class="-mt-10 px-6 lg:px-12">
        <div class="max-w-[1280px] mx-auto">
            <div class="bg-white/90 backdrop-blur-xl border border-white/70 rounded-3xl shadow-[0_20px_60px_rgba(15,23,42,0.12)] px-4 py-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    <div class="lg:col-span-2 flex items-center gap-3 px-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#EAF3FF] border border-[#4DA8FF]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                        </div>
                        <div class="leading-tight">
                            <p class="text-[#233876] font-extrabold text-sm uppercase">Akses</p>
                            <p class="text-[#233876] font-extrabold text-sm uppercase">Cepat</p>
                        </div>
                    </div>

                    <div class="lg:col-span-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <a href="#bencana-aktif" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#F2F7FF] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors">Informasi Bencana</p>
                        </a>
                        <a href="/donasi" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#F2F7FF] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors">Donasi</p>
                        </a>
                        <a href="/history-bencana" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#F2F7FF] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors">History Bencana</p>
                        </a>
                        <a href="/video" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#F2F7FF] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 4h6a2 2 0 002-2V8a2 2 0 00-2-2H9a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors">Video Edukasi</p>
                        </a>
                        <a href="/belajar-bisindo" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#F2F7FF] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#3155A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 4a2 2 0 100-4m0 4a2 2 0 110-4m-6-2v2m0-2a2 2 0 100 4m0-4a2 2 0 110 4"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors">Belajar BISINDO</p>
                        </a>
                        <a href="/request-bantuan" class="group bg-white rounded-2xl border border-slate-100 px-4 py-4 hover:shadow-md transition-all text-center">
                            <div class="w-10 h-10 mx-auto rounded-2xl bg-[#FFECEC] flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <p class="mt-3 text-xs font-extrabold text-slate-900 group-hover:text-red-600 transition-colors">Request Bantuan</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="bencana-aktif" class="px-6 lg:px-12 pt-10 pb-14">
        <div class="max-w-[1280px] mx-auto">
            <div class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63] rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(15,23,42,0.18)]">
                <div class="p-6 lg:p-8 flex items-start justify-between gap-6">
                    <div>
                        <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Bencana Aktif</p>
                    </div>
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-4 py-3">
                        <div class="flex items-center gap-3">
                            <p class="text-[10px] font-extrabold text-blue-100/80 uppercase tracking-widest">Pilih Event</p>
                            <select wire:model.live="selectedEventId" class="bg-transparent text-white font-extrabold outline-none">
                                <option value="">-</option>
                                @foreach($events as $ev)
                                    <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 lg:px-8 pb-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                            <p class="text-[10px] text-blue-100/70 font-extrabold uppercase tracking-widest">Status Bencana</p>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <p class="text-2xl font-extrabold text-white">{{ $event ? 'ACTIVE' : '-' }}</p>
                            </div>
                            <p class="mt-2 text-blue-100/80 font-semibold text-sm">Status saat ini</p>
                        </div>

                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                            <p class="text-[10px] text-blue-100/70 font-extrabold uppercase tracking-widest">Warga Terdampak</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <p class="text-2xl font-extrabold text-white">{{ number_format((int) $stats['impacted_people']) }}</p>
                                <svg class="w-7 h-7 text-blue-100/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1m-6 6H2v-2a4 4 0 014-4h1m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 4a4 4 0 10-8 0 4 4 0 008 0z"/></svg>
                            </div>
                            <p class="mt-2 text-blue-100/80 font-semibold text-sm">Jiwa</p>
                        </div>

                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                            <p class="text-[10px] text-blue-100/70 font-extrabold uppercase tracking-widest">Rumah Terdampak</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <p class="text-2xl font-extrabold text-white">{{ $stats['affected_houses'] !== null ? number_format((int) $stats['affected_houses']) : '-' }}</p>
                                <svg class="w-7 h-7 text-blue-100/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4m10-11v10a1 1 0 01-1 1h-4m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <p class="mt-2 text-blue-100/80 font-semibold text-sm">Unit</p>
                        </div>

                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                            <p class="text-[10px] text-blue-100/70 font-extrabold uppercase tracking-widest">Paket Logistik Tersedia</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <p class="text-2xl font-extrabold text-white">{{ number_format((int) $stats['logistics_packages']) }}</p>
                                <svg class="w-7 h-7 text-blue-100/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6"/></svg>
                            </div>
                            <p class="mt-2 text-blue-100/80 font-semibold text-sm">Paket</p>
                        </div>

                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                            <p class="text-[10px] text-blue-100/70 font-extrabold uppercase tracking-widest">Posko Aktif</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <p class="text-2xl font-extrabold text-white">{{ number_format((int) $stats['posko_active']) }}</p>
                                <svg class="w-7 h-7 text-blue-100/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V5a2 2 0 012-2h14a2 2 0 012 2v10.382a2 2 0 01-1.106 1.789L15 20v-6H9v6z"/></svg>
                            </div>
                            <p class="mt-2 text-blue-100/80 font-semibold text-sm">Posko</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 lg:px-12 pb-14">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Video Edukasi</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Video Edukasi & Mitigasi</p>
                    </div>
                    <a href="/video" class="text-xs font-extrabold text-[#3155A6] uppercase tracking-widest">Lihat Semua</a>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($videos as $v)
                        @php
                            $thumb = 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80&sig=' . ((int) $v->id * 11);
                            $durations = ['04:15', '03:42', '05:10', '06:05', '02:58', '04:48'];
                            $duration = $durations[((int) $v->id) % count($durations)];
                        @endphp
                        <a href="/video/{{ $v->id }}" class="group bg-white border border-slate-100 rounded-3xl overflow-hidden hover:shadow-md transition-all">
                            <div class="relative aspect-video overflow-hidden">
                                <img src="{{ $thumb }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-[#0B1635]/25"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-white/85 backdrop-blur border border-white/50 flex items-center justify-center shadow-lg shadow-black/20">
                                        <svg class="w-6 h-6 text-[#233876]" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                <div class="absolute right-3 bottom-3 bg-black/60 backdrop-blur border border-white/10 text-white px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-widest">
                                    {{ $duration }}
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="font-extrabold text-slate-900 leading-snug group-hover:text-[#3155A6] transition-colors line-clamp-2">{{ $v->judul }}</p>
                                @if($v->bisindo_embed_url)
                                    <div class="mt-3 inline-flex items-center gap-2 bg-[#EAF3FF] border border-[#4DA8FF]/25 text-[#233876] px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">
                                        Subtitle BISINDO
                                    </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="md:col-span-3 bg-slate-50 border border-slate-100 rounded-3xl p-10 text-center text-slate-600 font-semibold">
                            Belum ada video.
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="lg:col-span-4 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Aksesibilitas</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">BISINDO</p>
                    </div>
                    <a href="/belajar-bisindo" class="text-xs font-extrabold text-[#3155A6] uppercase tracking-widest">Lihat Semua</a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($bisindo as $m)
                        <a href="/belajar-bisindo" class="group flex items-center justify-between gap-4 bg-[#F6F9FF] border border-slate-100 rounded-3xl p-4 hover:shadow-md transition-all">
                            <div class="flex items-center gap-4 min-w-0">
                                <img src="{{ $m->gambar_url }}" alt="" class="w-12 h-12 rounded-2xl object-cover border border-white">
                                <div class="min-w-0">
                                    <p class="font-extrabold text-slate-900 group-hover:text-[#3155A6] transition-colors truncate">{{ $m->judul }}</p>
                                    <p class="mt-1 text-xs text-slate-500 font-semibold truncate">{{ $m->kategori }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 bg-white border border-slate-200 text-[#233876] px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">
                                {{ $m->tingkat }}
                            </span>
                        </a>
                    @empty
                        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-10 text-center text-slate-600 font-semibold">
                            Belum ada materi BISINDO.
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>

    <section class="px-6 lg:px-12 pb-14">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Berita Terkini</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Berita Terkini</p>
                    </div>
                    <a href="/berita" class="text-xs font-extrabold text-[#3155A6] uppercase tracking-widest">Lihat Semua</a>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($news as $n)
                        <a href="/berita" class="group bg-[#F6F9FF] border border-slate-100 rounded-3xl overflow-hidden hover:shadow-md transition-all">
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ $n['img'] ?? 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80' }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-4">
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">{{ $n['date'] ?? '' }}</p>
                                <p class="mt-2 font-extrabold text-slate-900 leading-snug group-hover:text-[#3155A6] transition-colors line-clamp-2">{{ $n['title'] ?? '-' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Galeri Kegiatan</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Galeri Kegiatan</p>
                    </div>
                    <a href="/galeri" class="text-xs font-extrabold text-[#3155A6] uppercase tracking-widest">Lihat Semua</a>
                </div>
                <div class="p-6 grid grid-cols-4 gap-3">
                    @forelse($photos->take(4) as $p)
                        @php
                            $src = str_starts_with($p->image_path, 'http://') || str_starts_with($p->image_path, 'https://')
                                ? $p->image_path
                                : asset($p->image_path);
                        @endphp
                        <a href="/galeri/{{ $p->disaster_event_id }}" class="group block rounded-2xl overflow-hidden border border-slate-100 bg-slate-100">
                            <img src="{{ $src }}" alt="" class="w-full h-full aspect-square object-cover group-hover:scale-105 transition-transform duration-500">
                        </a>
                    @empty
                        <div class="col-span-4 bg-slate-50 border border-slate-100 rounded-3xl p-10 text-center text-slate-600 font-semibold">
                            Belum ada foto galeri.
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="lg:col-span-3 bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63] rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(35,56,118,0.22)] relative">
                <div class="absolute right-6 top-8 opacity-95">
                    <svg width="140" height="110" viewBox="0 0 140 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 38c0-5.523 4.477-10 10-10h76c5.523 0 10 4.477 10 10v48c0 5.523-4.477 10-10 10H32c-5.523 0-10-4.477-10-10V38z" fill="#0B1635" fill-opacity=".25"/>
                        <path d="M30 46c0-4.418 3.582-8 8-8h60c4.418 0 8 3.582 8 8v32c0 4.418-3.582 8-8 8H38c-4.418 0-8-3.582-8-8V46z" fill="#4DA8FF" fill-opacity=".18"/>
                        <path d="M54 62c0-7.732 6.268-14 14-14s14 6.268 14 14-6.268 14-14 14-14-6.268-14-14z" fill="#4DA8FF" fill-opacity=".35"/>
                        <path d="M68 53.8l2.3 2.2 4.8-4.9" stroke="#EAF3FF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M95 58h18v6H95v-6z" fill="#EAF3FF" fill-opacity=".18"/>
                        <path d="M95 70h14v6H95v-6z" fill="#EAF3FF" fill-opacity=".12"/>
                    </svg>
                </div>

                <div class="p-6 border-b border-white/10 relative">
                    <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Donasi</p>
                    <h3 class="mt-2 text-2xl font-extrabold text-white leading-tight">Bantu Sesama, Tepat Sasaran</h3>
                    <p class="mt-3 text-blue-100/90 font-semibold">Donasi tunai melalui BAZNAS atau donasi barang sesuai kebutuhan event.</p>
                </div>
                <div class="p-6 space-y-3 relative">
                    <a href="/donasi" class="w-full inline-flex items-center justify-center gap-2 bg-[#1E66FF] hover:bg-[#175AE2] text-white px-5 py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/25">
                        Donasi Sekarang
                    </a>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="/donasi" class="text-center bg-white/10 hover:bg-white/15 text-white px-4 py-3 rounded-2xl font-extrabold text-sm transition-colors">Donasi Barang</a>
                        <a href="/donasi" class="text-center bg-white/10 hover:bg-white/15 text-white px-4 py-3 rounded-2xl font-extrabold text-sm transition-colors">Donasi Tunai</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

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
