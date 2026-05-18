<?php
use Livewire\Component;
use App\Models\Setting;

new class extends Component {
    public function with() {
        $focus = json_decode(Setting::get('guest.profile_focus', ''), true);
        if (!is_array($focus) || count($focus) === 0) {
            $focus = [
                'Manajemen logistik tanggap darurat yang transparan dan terukur.',
                'Edukasi mitigasi berbasis komunitas untuk meningkatkan kesiapsiagaan.',
                'Koordinasi cepat lintas mitra dalam situasi darurat.',
            ];
        }

        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'title' => Setting::get('guest.profile_title', 'Profil MDMC DIY'),
            'excerpt' => Setting::get('guest.profile_excerpt', 'Mengenal peran, fokus, dan cara kerja MDMC DIY dalam kesiapsiagaan dan respons bencana.'),
            'body' => Setting::get('guest.profile_body', 'MDMC DIY berfokus pada kesiapsiagaan, respons, dan pemulihan bencana melalui koordinasi relawan, pengelolaan logistik, serta edukasi mitigasi untuk masyarakat.'),
            'focus' => $focus,
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
                @if($emergencyMode)
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
                <a href="/profil" class="text-slate-900 font-extrabold text-sm">Profil</a>
                <a href="/layanan" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-slate-600 hover:text-slate-900 font-bold text-sm">Galeri</a>
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-extrabold text-sm transition-all shadow-sm shadow-emerald-100">Donasi</a>
            </div>

            <button class="lg:hidden p-2 rounded-xl hover:bg-slate-50" @click="open = !open" aria-label="Menu">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div x-show="open" x-cloak class="absolute top-20 left-0 right-0 bg-white border-b border-slate-100 lg:hidden">
                <div class="px-6 py-4 space-y-2">
                    <a href="/" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Beranda</a>
                    <a href="/profil" class="block px-3 py-2 rounded-xl font-bold text-slate-900 bg-slate-50">Profil</a>
                    <a href="/layanan" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Layanan</a>
                    <a href="/berita" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Berita</a>
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Galeri</a>
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700">Donasi</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="px-6 lg:px-12 py-14">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-7">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Profil</p>
                <h1 class="mt-3 text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">{{ $title }}</h1>
                <p class="mt-4 text-lg text-slate-500 leading-relaxed">{{ $excerpt }}</p>
                <div class="mt-8 bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                    <p class="text-slate-600 leading-relaxed">{{ $body }}</p>
                </div>
            </div>
            <aside class="lg:col-span-5 space-y-6">
                <div class="bg-slate-900 rounded-3xl p-8 text-white">
                    <p class="text-xs font-bold text-slate-300 uppercase tracking-widest">Fokus</p>
                    <ul class="mt-4 space-y-3 text-slate-200">
                        @foreach($focus as $item)
                            <li class="flex items-start gap-3">
                                <span class="mt-1 w-2 h-2 rounded-full bg-blue-600"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kontak</p>
                    <p class="mt-2 font-extrabold text-slate-900">Koordinasi Relawan & Logistik</p>
                    <p class="mt-2 text-sm text-slate-500">Gunakan kanal resmi untuk koordinasi. Untuk kondisi darurat, gunakan tombol Minta Bantuan saat Mode Bencana aktif.</p>
                    <div class="mt-5 flex gap-3">
                        <a href="/layanan" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white px-4 py-3 rounded-2xl font-bold text-center">Lihat Layanan</a>
                        <a href="/berita" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 px-4 py-3 rounded-2xl font-bold text-center border border-slate-100">Baca Berita</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <footer class="py-10 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} MDMC DIY - Muhammadiyah Disaster Management Center</p>
        </div>
    </footer>
</div>
