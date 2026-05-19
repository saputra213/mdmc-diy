<?php
use Livewire\Component;
use App\Models\Setting;

new class extends Component {
    public function with() {
        $posts = json_decode(Setting::get('guest.news_posts', ''), true);
        if (!is_array($posts) || count($posts) === 0) {
            $posts = [
                ['title' => 'Update Posko Bantul: Kebutuhan Mendesak', 'date' => now()->subDays(1)->format('Y-m-d'), 'category' => 'Berita Terkini', 'img' => 'https://images.unsplash.com/photo-1520975916090-3105956dac38?w=1200&q=80'],
                ['title' => 'Mitigasi Banjir: Panduan Singkat untuk Warga', 'date' => now()->subDays(2)->format('Y-m-d'), 'category' => 'Mitigasi', 'img' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=1200&q=80'],
                ['title' => 'Relawan: Prosedur Penerimaan & Distribusi Barang', 'date' => now()->subDays(3)->format('Y-m-d'), 'category' => 'Info Logistik', 'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1200&q=80'],
                ['title' => 'Info Cuaca DIY dari BMKG (Pekan Ini)', 'date' => now()->subDays(4)->format('Y-m-d'), 'category' => 'Berita Terkini', 'img' => 'https://images.unsplash.com/photo-1534088568595-a066f410bcda?w=1200&q=80'],
            ];
        }

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
            'title' => Setting::get('guest.news_title', 'Berita & Informasi'),
            'excerpt' => Setting::get('guest.news_excerpt', 'Update terkini seputar kebencanaan dan kegiatan lapangan.'),
            'posts' => $posts,
            'popular' => $popular,
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
                <a href="/berita" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/donasi" class="text-blue-100 hover:text-white font-bold text-sm">Donasi</a>
                <a href="/video" class="text-blue-100 hover:text-white font-bold text-sm">Video</a>
                <a href="/belajar-bisindo" class="text-blue-100 hover:text-white font-bold text-sm">BISINDO</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                <button type="button" class="p-2 rounded-xl bg-white/10 hover:bg-white/15 transition-colors" aria-label="Search">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                </button>
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
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">Berita</a>
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

    <header class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63]">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-10">
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Berita</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">{{ $title }}</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">{{ $excerpt }}</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($posts as $post)
                    <a href="#" class="group bg-[#F6F9FF] border border-slate-100 rounded-3xl overflow-hidden hover:shadow-md transition-all">
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $post['img'] ?? 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80' }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="bg-white border border-slate-100 text-slate-700 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">{{ $post['category'] ?? 'Berita' }}</span>
                                <span class="text-xs font-mono text-slate-400">{{ $post['date'] ?? '' }}</span>
                            </div>
                            <p class="mt-3 font-extrabold text-slate-900 leading-snug group-hover:text-[#3155A6] transition-colors">{{ $post['title'] ?? '-' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <aside class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <p class="font-extrabold text-slate-900">Berita Populer</p>
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Top</span>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @foreach($popular as $item)
                            <a href="#" class="block p-5 hover:bg-slate-50/60 transition-colors">
                                <p class="font-extrabold text-slate-800 leading-snug hover:text-[#3155A6] transition-colors">{{ $item['title'] }}</p>
                                <p class="mt-2 text-xs text-slate-400 font-mono">{{ $item['date'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63] text-white rounded-3xl p-6 shadow-[0_20px_60px_rgba(35,56,118,0.22)]">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-blue-100/80">Emergency</p>
                    <p class="mt-2 text-xl font-extrabold leading-tight">Emergency Call / Request Bantuan</p>
                    <p class="mt-2 text-sm text-blue-100/90 font-semibold">Jika Anda berada di area terdampak bencana, gunakan form resmi untuk permintaan bantuan.</p>
                    <div class="mt-5">
                        @if($emergencyMode)
                            <a href="/request-bantuan" class="block bg-red-600 hover:bg-red-700 text-white text-center px-6 py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-red-900/25">
                                Request Bantuan
                            </a>
                        @else
                            <div class="bg-white/10 border border-white/10 text-blue-100 px-6 py-4 rounded-2xl font-extrabold text-center">
                                Mode Normal (Nonaktif)
                            </div>
                        @endif
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
