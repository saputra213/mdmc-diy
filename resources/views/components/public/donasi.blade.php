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

<div class="min-h-screen bg-slate-50">
    <nav class="bg-blue-900/95 backdrop-blur-md sticky top-0 z-50 border-b border-blue-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between" x-data="{ open: false }">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <span class="font-extrabold text-lg tracking-tight text-white">MDMC <span class="text-blue-200">DIY</span></span>
            </a>

            <div class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-blue-100 hover:text-white font-bold text-sm">Beranda</a>
                <a href="/profil" class="text-blue-100 hover:text-white font-bold text-sm">Profil</a>
                <a href="/layanan" class="text-blue-100 hover:text-white font-bold text-sm">Layanan</a>
                <a href="/berita" class="text-blue-100 hover:text-white font-bold text-sm">Berita</a>
                <a href="/galeri" class="text-blue-100 hover:text-white font-bold text-sm">Galeri</a>
                <a href="/history-bencana" class="text-blue-100 hover:text-white font-bold text-sm">History</a>
                <a href="/donasi" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-extrabold text-sm transition-all shadow-lg shadow-emerald-100">Donasi</a>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                <a href="/login" class="px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/15 transition-colors">Login</a>
                @if($emergencyMode)
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
                    <a href="/donasi" class="block px-3 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700">Donasi</a>
                    <div class="pt-2 flex gap-2">
                        <a href="/login" class="flex-1 px-4 py-3 rounded-xl font-bold text-sm text-white bg-white/10 text-center">Login</a>
                        @if($emergencyMode)
                            <a href="/request-bantuan" class="flex-1 px-4 py-3 rounded-xl font-bold text-sm text-white bg-red-600 text-center">Minta Bantuan</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <header class="px-6 lg:px-12 pt-10 pb-8 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-white">Donasi Masyarakat</h1>
            <p class="text-blue-100/90 mt-3 max-w-2xl">Pilih donasi tunai atau donasi barang sesuai kebutuhan event bencana.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 border-b border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Donasi Tunai</p>
                        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Diarahkan ke BAZNAS</h2>
                        <p class="mt-2 text-sm text-slate-500 font-semibold">Klik tombol di bawah untuk menuju halaman donasi tunai.</p>
                    </div>
                    <div class="p-8">
                        <a href="{{ $baznasUrl }}" target="_blank" class="w-full inline-flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-extrabold transition-all">
                            Buka Donasi Tunai (BAZNAS)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 border-b border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Donasi Barang</p>
                        <h2 class="mt-2 text-xl font-extrabold text-slate-900">Berdasarkan kebutuhan event</h2>
                        <p class="mt-2 text-sm text-slate-500 font-semibold">Pilih event, lihat barang yang dibutuhkan, lalu chat WhatsApp MDMC.</p>
                    </div>
                    <div class="p-8 space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Pilih Event Aktif</label>
                            <select wire:model.live="selectedEventId" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
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
</div>
