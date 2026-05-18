<?php
use Livewire\Component;
use App\Models\DisasterEvent;
use App\Models\Setting;

new class extends Component {
    public function with() {
        $activeEvents = DisasterEvent::active()->orderByDesc('updated_at')->get(['id', 'name']);
        $activeEventCount = $activeEvents->count();
        $activeEventName = $activeEventCount === 1 ? ($activeEvents->first()?->name) : null;
        $servicesCards = json_decode(Setting::get('guest.services_cards', ''), true);
        if (!is_array($servicesCards) || count($servicesCards) === 0) {
            $servicesCards = [
                ['label' => 'Info Logistik', 'title' => 'Ketersediaan Stok', 'desc' => 'Pantau ringkas ketersediaan barang dan prioritas kebutuhan.'],
                ['label' => 'Mitigasi', 'title' => 'Panduan Siaga', 'desc' => 'Video dan ringkasan langkah cepat untuk berbagai skenario bencana.'],
                ['label' => 'Kontak', 'title' => 'Koordinasi Relawan', 'desc' => 'Informasi koordinasi dan komunikasi untuk mitra lapangan.'],
            ];
        }

        $newsLatest = json_decode(Setting::get('guest.news_posts', ''), true);
        if (!is_array($newsLatest) || count($newsLatest) === 0) {
            $newsLatest = [
                ['title' => 'Update Posko Bantul: Kebutuhan Mendesak', 'date' => now()->subDays(1)->format('Y-m-d'), 'category' => 'Berita Terkini', 'img' => 'https://images.unsplash.com/photo-1520975916090-3105956dac38?w=1200&q=80'],
                ['title' => 'Mitigasi Banjir: Panduan Singkat untuk Warga', 'date' => now()->subDays(2)->format('Y-m-d'), 'category' => 'Mitigasi', 'img' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=1200&q=80'],
                ['title' => 'Relawan: Prosedur Penerimaan & Distribusi Barang', 'date' => now()->subDays(3)->format('Y-m-d'), 'category' => 'Info Logistik', 'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80'],
            ];
        }

        $galleryImages = json_decode(Setting::get('guest.gallery_images', ''), true);
        if (!is_array($galleryImages) || count($galleryImages) === 0) {
            $galleryImages = [
                'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&q=80',
                'https://images.unsplash.com/photo-1520975916090-3105956dac38?w=600&q=80',
                'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=600&q=80',
                'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=600&q=80',
            ];
        }

        $featured = $newsLatest[0] ?? [
            'title' => 'Distribusi Logistik Terpadu untuk Dampak Maksimal',
            'date' => now()->format('Y-m-d'),
            'category' => 'Info Logistik',
            'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80',
        ];

        $latest = array_slice($newsLatest, 0, 4);

        $popular = json_decode(Setting::get('guest.popular_posts', ''), true);
        if (!is_array($popular) || count($popular) === 0) {
            $popular = [
                ['title' => 'Panduan Darurat: Nomor Penting DIY', 'date' => now()->subDays(10)->format('Y-m-d')],
                ['title' => 'Tata Cara Request Bantuan yang Benar', 'date' => now()->subDays(9)->format('Y-m-d')],
                ['title' => 'FAQ Distribusi Logistik untuk Relawan', 'date' => now()->subDays(8)->format('Y-m-d')],
            ];
        }

        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'hasActiveEvent' => $activeEventCount > 0,
            'activeEventCount' => $activeEventCount,
            'activeEventName' => $activeEventName,
            'heroHeadline' => Setting::get('guest.hero_headline', 'Manajemen Bencana Terpadu untuk Respons Cepat dan Tepat.'),
            'heroDescription' => Setting::get('guest.hero_description', 'Pusat informasi dan koordinasi logistik MDMC DIY untuk membantu masyarakat, relawan, dan mitra dalam situasi darurat.'),
            'videoUrl' => Setting::get('guest.video_url', 'https://www.youtube.com/embed/ysz5S6PUM-U'),
            'posterPath' => Setting::get('guest.poster_path'),
            'profileTitle' => Setting::get('guest.profile_title', 'Profil MDMC DIY'),
            'profileExcerpt' => Setting::get('guest.profile_excerpt', 'Mengenal peran, fokus, dan cara kerja MDMC DIY dalam kesiapsiagaan dan respons bencana.'),
            'servicesTitle' => Setting::get('guest.services_title', 'Layanan Informasi'),
            'servicesExcerpt' => Setting::get('guest.services_excerpt', 'Ringkasan layanan untuk kebutuhan tanggap darurat.'),
            'newsTitle' => Setting::get('guest.news_title', 'Berita & Informasi'),
            'newsExcerpt' => Setting::get('guest.news_excerpt', 'Update terkini seputar kebencanaan dan kegiatan lapangan.'),
            'galleryTitle' => Setting::get('guest.gallery_title', 'Galeri'),
            'galleryExcerpt' => Setting::get('guest.gallery_excerpt', 'Dokumentasi kegiatan koordinasi dan distribusi.'),
            'featured' => $featured,
            'latest' => $latest,
            'popular' => $popular,
            'servicesCards' => $servicesCards,
            'galleryImages' => array_slice($galleryImages, 0, 4),
        ];
    }
};
?>

<div class="min-h-screen bg-slate-50">
    <div class="bg-blue-900 text-white text-xs">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-2 flex items-center justify-between" x-data="{ now: new Date(), tick() { this.now = new Date() } }" x-init="setInterval(() => tick(), 1000)">
            <div class="flex items-center gap-3 text-blue-100/80">
                <span class="font-bold tracking-widest uppercase">Info</span>
                <span class="opacity-70">•</span>
                <span x-text="now.toLocaleString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: '2-digit' })"></span>
                <span class="opacity-70">•</span>
                <span class="font-mono" x-text="now.toLocaleTimeString('id-ID')"></span>
            </div>
            <div class="flex items-center gap-3">
                <a href="#" class="text-blue-100/80 hover:text-white transition-colors" aria-label="Instagram">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5a4.25 4.25 0 00-4.25-4.25h-8.5z"/><path d="M12 7a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z"/><path d="M17.5 6.75a.75.75 0 110 1.5.75.75 0 010-1.5z"/></svg>
                </a>
                <a href="#" class="text-blue-100/80 hover:text-white transition-colors" aria-label="YouTube">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.6 7.2a3 3 0 00-2.1-2.1C17.7 4.5 12 4.5 12 4.5s-5.7 0-7.5.6A3 3 0 002.4 7.2 31.6 31.6 0 002.1 12a31.6 31.6 0 00.3 4.8 3 3 0 002.1 2.1c1.8.6 7.5.6 7.5.6s5.7 0 7.5-.6a3 3 0 002.1-2.1A31.6 31.6 0 0021.9 12a31.6 31.6 0 00-.3-4.8zM10.2 15.3V8.7L15.9 12l-5.7 3.3z"/></svg>
                </a>
                <a href="#" class="text-blue-100/80 hover:text-white transition-colors" aria-label="X">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 2H22l-6.7 7.7L23 22h-6.2l-4.9-7.1L5.7 22H2.6l7.1-8.2L1 2h6.3l4.4 6.4L18.9 2zm-1.1 18h1.7L6.1 4H4.3l13.5 16z"/></svg>
                </a>
            </div>
        </div>
    </div>

    <nav class="bg-blue-900/95 backdrop-blur-md sticky top-0 z-50 border-b border-blue-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between" x-data="{ open: false }">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <span class="font-extrabold text-lg tracking-tight text-white">MDMC <span class="text-blue-200">DIY</span></span>
            </div>

            <div class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/history-bencana" class="text-blue-100 hover:text-white font-bold text-sm">History</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-100">Donasi</a>
                <a href="/login" class="px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/15 transition-colors">Login</a>
                @if($emergencyMode && $hasActiveEvent)
                    <a href="/request-bantuan" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-red-100">Minta Bantuan</a>
                @endif
            </div>

            <button class="lg:hidden p-2 rounded-xl hover:bg-blue-800/60" @click="open = !open" aria-label="Menu">
                <svg class="w-6 h-6 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div x-show="open" x-cloak class="absolute top-20 left-0 right-0 bg-blue-900 border-b border-blue-800 lg:hidden">
                <div class="px-6 py-4 space-y-2">
                    <a href="/" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">Beranda</a>
                    <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">Profil</a>
                    <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">Layanan</a>
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">Berita</a>
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">Galeri</a>
                    <a href="/history-bencana" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-blue-800/60 hover:text-white">History</a>
                    <div class="pt-2 flex gap-2">
                        <a href="/donasi" class="flex-1 px-4 py-3 rounded-xl font-bold text-sm text-white bg-emerald-600 text-center">Donasi</a>
                        <a href="/login" class="flex-1 px-4 py-3 rounded-xl font-bold text-sm text-white bg-white/10 text-center">Login</a>
                        @if($emergencyMode && $hasActiveEvent)
                            <a href="/request-bantuan" class="flex-1 px-4 py-3 rounded-xl font-bold text-sm text-white bg-red-600 text-center">Minta Bantuan</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <section class="px-6 lg:px-12 pt-10 pb-10 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5">
                    <span class="inline-flex items-center gap-2 bg-white/10 text-blue-100 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                        Sistem Informasi Bencana
                    </span>
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight">{{ $heroHeadline }}</h1>
                    <p class="text-blue-100/90 mt-5 text-lg leading-relaxed">{{ $heroDescription }}</p>
                    @if($hasActiveEvent)
                        <div class="mt-4 inline-flex items-center gap-2 bg-white/10 border border-white/10 text-blue-100 px-4 py-2 rounded-2xl text-sm font-bold">
                            Event Aktif: {{ (int) $activeEventCount }}@if($activeEventName) ({{ $activeEventName }})@endif
                        </div>
                    @endif
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="/berita" class="bg-white hover:bg-blue-50 text-blue-900 px-6 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/20 text-center">Lihat Berita</a>
                        @if($emergencyMode && $hasActiveEvent)
                            <a href="/request-bantuan" class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-red-100 text-center">Emergency Call / Minta Bantuan</a>
                        @else
                            <div class="bg-white/10 border border-white/10 text-blue-100 px-6 py-4 rounded-2xl font-bold text-center">Mode Normal</div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-7">
                            <a href="/berita" class="group block bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                                <div class="aspect-[16/10] overflow-hidden">
                                    <img src="{{ $featured['img'] }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="p-6 space-y-2">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-bold">{{ $featured['category'] }}</span>
                                        <span class="text-slate-400 font-bold">{{ $featured['date'] }}</span>
                                    </div>
                                    <h2 class="text-xl font-extrabold text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">{{ $featured['title'] }}</h2>
                                    <p class="text-sm text-slate-500">Berita utama hari ini terkait kesiapan logistik dan koordinasi lapangan.</p>
                                </div>
                            </a>
                        </div>

                        <div class="lg:col-span-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-widest">Terbaru</h3>
                                <a href="/berita" class="text-xs font-bold text-blue-600 hover:text-blue-700">Lihat Semua</a>
                            </div>
                            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm divide-y divide-slate-50">
                                @foreach($latest as $item)
                                    <a href="/berita" class="block p-5 hover:bg-slate-50/60 transition-colors">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item['category'] }}</span>
                                            <span class="text-xs font-mono text-slate-400">{{ $item['date'] }}</span>
                                        </div>
                                        <p class="mt-2 font-bold text-slate-700 leading-snug hover:text-blue-600 transition-colors">{{ $item['title'] }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="/profil" class="group bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Profil</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $profileTitle }}</p>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $profileExcerpt }}</p>
                    <p class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest">Buka Halaman</p>
                </a>
                <a href="/layanan" class="group bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Layanan</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $servicesTitle }}</p>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $servicesExcerpt }}</p>
                    <p class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest">Buka Halaman</p>
                </a>
                <a href="/berita" class="group bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Berita</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $newsTitle }}</p>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $newsExcerpt }}</p>
                    <p class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest">Buka Halaman</p>
                </a>
                <a href="/galeri" class="group bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Galeri</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $galleryTitle }}</p>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">{{ $galleryExcerpt }}</p>
                    <p class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest">Buka Halaman</p>
                </a>
            </div>
        </div>
    </section>

    <section class="px-6 lg:px-12 py-14 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-8">
                <div class="flex items-end justify-between gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Highlight</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-slate-900">{{ $servicesTitle }}</h2>
                        <p class="mt-2 text-slate-500">{{ $servicesExcerpt }}</p>
                    </div>
                    <a href="/layanan" class="text-sm font-bold text-blue-600 hover:text-blue-700">Buka Halaman</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(array_slice($servicesCards, 0, 3) as $card)
                        <a href="/layanan" class="bg-slate-50/60 rounded-3xl border border-slate-100 p-6 hover:bg-slate-50 transition-colors">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $card['label'] ?? 'Layanan' }}</p>
                            <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $card['title'] ?? '-' }}</p>
                            <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $card['desc'] ?? '' }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="flex items-end justify-between gap-6 pt-2">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Highlight</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-slate-900">{{ $galleryTitle }}</h2>
                        <p class="mt-2 text-slate-500">{{ $galleryExcerpt }}</p>
                    </div>
                    <a href="/galeri" class="text-sm font-bold text-blue-600 hover:text-blue-700">Buka Halaman</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($galleryImages as $img)
                        <a href="/galeri" class="block rounded-3xl overflow-hidden border border-slate-100">
                            <img class="w-full h-full aspect-square object-cover hover:scale-105 transition-transform duration-500" src="{{ $img }}" alt="">
                        </a>
                    @endforeach
                </div>
            </div>

            <aside class="lg:col-span-4 space-y-6">
                <div class="bg-slate-900 text-white rounded-3xl p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Emergency</p>
                    <p class="mt-2 text-xl font-extrabold leading-tight">Emergency Call / Minta Bantuan</p>
                    <p class="mt-2 text-sm text-slate-300">Gunakan fitur ini hanya untuk situasi darurat di area terdampak bencana.</p>
                    <div class="mt-5">
                        @if($emergencyMode)
                            <a href="/request-bantuan" class="block bg-red-600 hover:bg-red-700 text-white text-center px-6 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-red-900/30">
                                Minta Bantuan Sekarang
                            </a>
                        @else
                            <div class="bg-white/10 border border-white/10 text-slate-200 px-6 py-4 rounded-2xl font-bold text-center">
                                Mode Normal (Nonaktif)
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <p class="font-extrabold text-slate-900">Berita Populer</p>
                        <a href="/berita" class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Buka</a>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @foreach(array_slice($popular, 0, 4) as $item)
                            <a href="/berita" class="block p-5 hover:bg-slate-50/60 transition-colors">
                                <p class="font-bold text-slate-700 leading-snug hover:text-blue-600 transition-colors">{{ $item['title'] }}</p>
                                <p class="mt-2 text-xs text-slate-400 font-mono">{{ $item['date'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($posterPath)
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="font-extrabold text-slate-900">Poster Informasi</p>
                                <p class="text-sm text-slate-500 mt-1">Info singkat untuk masyarakat.</p>
                            </div>
                            <a href="{{ asset($posterPath) }}" target="_blank" class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Buka</a>
                        </div>
                        <a href="{{ asset($posterPath) }}" target="_blank" class="block">
                            <img src="{{ asset($posterPath) }}" class="w-full h-auto" alt="Poster Informasi">
                        </a>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="font-extrabold text-slate-900">Video Mitigasi</p>
                            <p class="text-sm text-slate-500 mt-1">Panduan singkat untuk kesiapsiagaan.</p>
                        </div>
                        <a href="/layanan" class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Buka</a>
                    </div>
                    <div class="aspect-video bg-black">
                        <iframe class="w-full h-full" src="{{ $videoUrl }}" title="Video Mitigasi" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <footer class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} MDMC DIY - Muhammadiyah Disaster Management Center</p>
        </div>
    </footer>
</div>
