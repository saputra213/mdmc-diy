<?php
use Livewire\Component;
use App\Models\BantuanRequest;
use App\Models\DisasterEvent;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\RateLimiter;

new class extends Component {
    #[Validate('required|integer')]
    public $disaster_event_id;

    #[Validate('required|min:3')]
    public $nama_pelapor;

    #[Validate('required|numeric|digits_between:10,15')]
    public $no_hp;

    #[Validate('required|min:10')]
    public $lokasi_detail;

    #[Validate('required|array|min:1')]
    public $jenis_bantuan = [];

    public $latitude = '-7.7956';
    public $longitude = '110.3695';

    public $success = false;

    public function mount(): void
    {
        $event = DisasterEvent::active()->orderByDesc('updated_at')->first();
        $this->disaster_event_id = $event?->id;
    }

    public function with()
    {
        $activeEvents = DisasterEvent::active()->orderByDesc('updated_at')->get();
        $emergencyMode = Setting::get('emergency_mode', 'off') === 'on';

        return [
            'emergencyMode' => $emergencyMode,
            'activeEvents' => $activeEvents,
            'hasActiveEvent' => $activeEvents->count() > 0,
            'options' => ['Makanan & Minuman', 'Peralatan Medis', 'Tenda & Hunian', 'Pakaian & Selimut', 'Kebutuhan Bayi/Lansia'],
        ];
    }

    public function save()
    {
        if (Setting::get('emergency_mode', 'off') !== 'on') {
            return session()->flash('error', 'Maaf, form bantuan saat ini dinonaktifkan.');
        }

        $event = DisasterEvent::active()->find($this->disaster_event_id);
        if (!$event) {
            $this->addError('disaster_event_id', 'Event tidak valid atau sudah tidak aktif.');
            return session()->flash('error', 'Maaf, belum ada event bencana yang aktif saat ini.');
        }

        // Rate Limiting: Max 2 requests per hour per IP
        if (RateLimiter::tooManyAttempts('bantuan-request:'.request()->ip(), 2)) {
            return $this->addError('nama_pelapor', 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
        }

        $this->validate();

        BantuanRequest::create([
            'disaster_event_id' => $event->id,
            'nama_pelapor' => $this->nama_pelapor,
            'no_hp' => $this->no_hp,
            'lokasi_detail' => $this->lokasi_detail,
            'jenis_bantuan' => $this->jenis_bantuan,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => 'pending',
        ]);

        RateLimiter::hit('bantuan-request:'.request()->ip(), 3600);

        $this->reset(['nama_pelapor', 'no_hp', 'lokasi_detail', 'jenis_bantuan']);
        $this->success = true;
    }
};
?>

<div class="min-h-screen bg-[#F6F9FF]" x-data="{
    map: null,
    marker: null,
    init() {
        this.$watch('$wire.success', (value) => {
            if (value) return;
            this.map = null;
            this.marker = null;
            this.$nextTick(() => this.initMap());
        });
    },
    initMap() {
        const el = document.getElementById('map-public');
        if (!el) return;
        if (this.map) return;
        this.map = L.map('map-public').setView([-7.7956, 110.3695], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        this.marker = L.marker([-7.7956, 110.3695], {
            draggable: true
        }).addTo(this.map);

        this.marker.on('dragend', (e) => {
            const pos = this.marker.getLatLng();
            @this.set('latitude', pos.lat.toFixed(6));
            @this.set('longitude', pos.lng.toFixed(6));
        });

        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            @this.set('latitude', e.latlng.lat.toFixed(6));
            @this.set('longitude', e.latlng.lng.toFixed(6));
        });

        setTimeout(() => {
            if (this.map) this.map.invalidateSize();
        }, 150);
    }
}" x-init="init(); initMap()">
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

    <header class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63]">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-10">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Emergency Call</p>
                    <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">Form Permintaan Bantuan</h1>
                    <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">Isi data di bawah ini untuk koordinasi logistik. Pilih event bencana yang sesuai sebelum mengirim laporan.</p>
                </div>
                <a href="/" class="inline-flex items-center gap-2 text-blue-100/90 hover:text-white font-extrabold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto">
            @if (session()->has('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$emergencyMode || !$hasActiveEvent)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden max-w-2xl mx-auto">
                    <div class="p-6 border-b border-slate-100">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Status</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Form Tidak Tersedia</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-700 font-semibold">Maaf, saat ini tidak ada event bencana yang aktif atau Emergency Mode sedang nonaktif.</p>
                                <p class="mt-2 text-slate-500 text-sm">Jika ada kondisi darurat, silakan pantau informasi terbaru di halaman Beranda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($success)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden max-w-2xl mx-auto">
                    <div class="p-6 border-b border-slate-100">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Status</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Permintaan Terkirim</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-slate-700 font-semibold">Terima kasih atas laporannya. Tim MDMC DIY akan segera memverifikasi data Anda.</p>
                                <div class="mt-5 flex flex-col sm:flex-row gap-3">
                                    <button type="button" wire:click="$set('success', false)" class="inline-flex items-center justify-center bg-[#1E66FF] hover:bg-[#175AE2] text-white px-6 py-3 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/15">
                                        Kirim Laporan Lain
                                    </button>
                                    <a href="/" class="inline-flex items-center justify-center bg-[#F6F9FF] hover:bg-slate-50 text-slate-800 px-6 py-3 rounded-2xl font-extrabold transition-all border border-slate-100">
                                        Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Form</p>
                        <p class="mt-2 text-xl font-extrabold text-slate-900">Isi data pelapor & titik lokasi</p>
                    </div>

                    <form wire:submit="save" class="p-6 lg:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-6 space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Event Bencana</label>
                                <select wire:model="disaster_event_id" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                                    <option value="">Pilih Event...</option>
                                    @foreach($activeEvents as $ev)
                                        <option value="{{ $ev->id }}">{{ $ev->name }}{{ $ev->location ? ' — ' . $ev->location : '' }}</option>
                                    @endforeach
                                </select>
                                @error('disaster_event_id') <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Pelapor / Penanggung Jawab</label>
                                <input wire:model="nama_pelapor" type="text" placeholder="Contoh: Budi Santoso" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                                @error('nama_pelapor') <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp Aktif</label>
                                <input wire:model="no_hp" type="tel" placeholder="Contoh: 081234567890" class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                                @error('no_hp') <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1 block">Jenis Bantuan yang Dibutuhkan</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($options as $option)
                                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-[#F6F9FF] cursor-pointer hover:bg-slate-50 transition-colors group">
                                            <input type="checkbox" wire:model="jenis_bantuan" value="{{ $option }}" class="w-5 h-5 rounded-lg border-slate-300 text-[#1E66FF] focus:ring-[#1E66FF]">
                                            <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('jenis_bantuan') <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Lokasi Detail (Dusun/RT/RW)</label>
                                <textarea wire:model="lokasi_detail" rows="3" placeholder="Sebutkan lokasi spesifik di mana bantuan dibutuhkan..." class="w-full px-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all"></textarea>
                                @error('lokasi_detail') <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="lg:col-span-6 space-y-6">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Titik Lokasi di Peta</label>
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Klik peta / geser pin</span>
                                </div>
                                <div id="map-public" class="h-[320px] lg:h-[480px] w-full rounded-3xl border border-slate-100 z-0 overflow-hidden" wire:ignore></div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-[#F6F9FF] p-4 rounded-2xl border border-slate-100">
                                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Latitude</p>
                                        <p class="text-sm font-mono font-extrabold text-slate-700" x-text="$wire.latitude"></p>
                                    </div>
                                    <div class="bg-[#F6F9FF] p-4 rounded-2xl border border-slate-100">
                                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Longitude</p>
                                        <p class="text-sm font-mono font-extrabold text-slate-700" x-text="$wire.longitude"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-5 rounded-2xl font-extrabold text-base lg:text-lg transition-all shadow-lg shadow-red-900/15 flex items-center justify-center gap-3 group">
                                    <span wire:loading.remove wire:target="save">Kirim Permintaan Bantuan</span>
                                    <span wire:loading wire:target="save">Memproses Laporan...</span>
                                    <svg wire:loading.remove wire:target="save" class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                                <p class="text-[10px] text-slate-400 text-center mt-4 uppercase font-extrabold tracking-widest">Data Anda akan dijaga kerahasiaannya oleh MDMC DIY</p>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
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
                    <p class="text-blue-100/90 font-semibold text-sm">Aktifkan toolbar aksesibilitas di pojok layar untuk preferensi tampilan.</p>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-6 text-center text-blue-100/80 text-sm font-semibold">
            © {{ date('Y') }} MDMC DIY. Semua hak dilindungi.
        </div>
    </footer>
</div>
