<?php

use Livewire\Component;
use App\Models\DisasterEvent;
use App\Models\Kebutuhan;
use App\Models\Setting;

new class extends Component {
    public $selectedEventId;

    public function mount(): void
    {
        $first = DisasterEvent::active()->orderByDesc('updated_at')->value('id');
        $this->selectedEventId = $first ? (int) $first : null;
    }

    private function normalizeWaNumber(?string $value): ?string
    {
        $value = preg_replace('/\s+/', '', (string) $value);
        $value = ltrim($value, '+');

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '0')) {
            return '62' . substr($value, 1);
        }

        return $value;
    }

    private function buildWaMessage(?DisasterEvent $event, $needs): string
    {
        $template = Setting::get('guest.donation_wa_template', 'Halo MDMC DIY, saya ingin donasi barang untuk event {event}. Saya tertarik membantu kebutuhan berikut:\n{items}\n\nMohon info lokasi drop-off dan prosedur selanjutnya.');

        $eventName = $event?->name ?: '-';
        $items = '';
        foreach ($needs as $n) {
            $items .= '- ' . $n->nama_barang . ' (' . $n->butuh_barang . ')' . "\n";
        }
        $items = trim($items) !== '' ? trim($items) : '- (belum ada kebutuhan tercatat)';

        return str_replace(
            ['{event}', '{items}'],
            [$eventName, $items],
            (string) $template
        );
    }

    public function with(): array
    {
        $baznasUrl = Setting::get('guest.donation_baznas_url', 'https://baznas.go.id/');
        $waNumber = $this->normalizeWaNumber(Setting::get('guest.donation_wa_number', ''));

        $events = DisasterEvent::active()->orderByDesc('updated_at')->get();
        $event = $this->selectedEventId ? $events->firstWhere('id', (int) $this->selectedEventId) : null;

        $needs = collect();
        if ($event) {
            $needs = Kebutuhan::query()
                ->where('disaster_event_id', $event->id)
                ->where('status', false)
                ->orderByDesc('id')
                ->take(12)
                ->get();
        }

        $waLink = null;
        if ($waNumber) {
            $message = $this->buildWaMessage($event, $needs);
            $waLink = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($message);
        }

        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'baznasUrl' => $baznasUrl,
            'events' => $events,
            'event' => $event,
            'needs' => $needs,
            'waLink' => $waLink,
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
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/donasi" class="text-white font-extrabold text-sm border-b-2 border-white pb-1">Donasi</a>
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
                    <a href="/galeri" class="block px-3 py-2 rounded-xl font-bold text-blue-100 hover:bg-white/10 hover:text-white">Galeri</a>
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-white bg-white/10">Donasi</a>
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
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Donasi</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">Donasi Masyarakat</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">Pilih donasi tunai atau donasi barang sesuai kebutuhan event bencana.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Donasi Tunai</p>
                        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Diarahkan ke BAZNAS</h2>
                        <p class="mt-2 text-sm text-slate-500 font-semibold">Klik tombol di bawah untuk menuju halaman donasi tunai.</p>
                    </div>
                    <div class="p-8">
                        <a href="{{ $baznasUrl }}" target="_blank" class="w-full inline-flex items-center justify-center gap-3 bg-[#1E66FF] hover:bg-[#175AE2] text-white py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/15">
                            Buka Donasi Tunai (BAZNAS)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Donasi Barang</p>
                        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Berdasarkan kebutuhan event</h2>
                        <p class="mt-2 text-sm text-slate-500 font-semibold">Pilih event, lihat barang yang dibutuhkan, lalu chat WhatsApp MDMC.</p>
                    </div>
                    <div class="p-8 space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Pilih Event Aktif</label>
                            <select wire:model.live="selectedEventId" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                                <option value="">Pilih event...</option>
                                @foreach($events as $ev)
                                    <option value="{{ $ev->id }}">{{ $ev->name }}{{ $ev->location ? ' — ' . $ev->location : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(!$event)
                            <div class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl font-semibold">
                                Belum ada event aktif untuk donasi barang.
                            </div>
                        @else
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900">Kebutuhan Barang</p>
                                        <p class="text-[11px] text-slate-500 font-semibold">{{ $event->name }}</p>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">{{ $needs->count() }} item</span>
                                </div>
                                <div class="divide-y divide-slate-200">
                                    @forelse($needs as $n)
                                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                                            <div>
                                                <p class="font-extrabold text-slate-900">{{ $n->nama_barang }}</p>
                                                <p class="text-[11px] text-slate-500 font-semibold">{{ $n->nama_lokasi }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-extrabold text-slate-900">{{ $n->butuh_barang }}</p>
                                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Jumlah</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-6 py-8 text-center text-slate-500 font-semibold">
                                            Belum ada data kebutuhan barang untuk event ini.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <a href="{{ $waLink ?: '#' }}" target="_blank" @class([
                                'w-full inline-flex items-center justify-center gap-3 py-4 rounded-2xl font-extrabold transition-all',
                                'bg-emerald-600 hover:bg-emerald-700 text-white' => (bool) $waLink,
                                'bg-slate-200 text-slate-500 pointer-events-none' => !$waLink,
                            ])>
                                Chat WhatsApp MDMC
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
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
