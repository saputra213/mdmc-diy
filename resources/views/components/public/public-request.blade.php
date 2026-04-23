<?php
use Livewire\Component;
use App\Models\BantuanRequest;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\RateLimiter;

new class extends Component {
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

    public function with() {
        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'options' => ['Makanan & Minuman', 'Peralatan Medis', 'Tenda & Hunian', 'Pakaian & Selimut', 'Kebutuhan Bayi/Lansia']
        ];
    }

    public function save() {
        if (Setting::get('emergency_mode', 'off') !== 'on') {
            return session()->flash('error', 'Maaf, form bantuan saat ini dinonaktifkan.');
        }

        // Rate Limiting: Max 2 requests per hour per IP
        if (RateLimiter::tooManyAttempts('bantuan-request:'.request()->ip(), 2)) {
            return $this->addError('nama_pelapor', 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
        }

        $this->validate();

        BantuanRequest::create([
            'nama_pelapor' => $this->nama_pelapor,
            'no_hp' => $this->no_hp,
            'lokasi_detail' => $this->lokasi_detail,
            'jenis_bantuan' => $this->jenis_bantuan,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => 'pending'
        ]);

        RateLimiter::hit('bantuan-request:'.request()->ip(), 3600);

        $this->reset(['nama_pelapor', 'no_hp', 'lokasi_detail', 'jenis_bantuan']);
        $this->success = true;
    }
};
?>

<div class="min-h-screen bg-slate-50 py-12 px-6" x-data="{
    map: null,
    marker: null,
    initMap() {
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
    }
}" x-init="initMap()">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <a href="/" class="inline-flex items-center gap-2 text-slate-400 hover:text-red-600 transition-colors mb-8 font-bold text-xs uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Form Permintaan Bantuan</h1>
            <p class="text-slate-500">Silakan isi data di bawah ini dengan sebenar-benarnya untuk koordinasi logistik.</p>
        </div>

        @if(!$emergencyMode)
            <div class="bg-amber-50 border border-amber-200 p-8 rounded-3xl text-center max-w-xl mx-auto">
                <svg class="w-16 h-16 text-amber-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-xl font-bold text-amber-900 mb-2">Sistem Mode Normal</h3>
                <p class="text-amber-700">Maaf, saat ini sistem tidak dalam Mode Bencana. Form permintaan bantuan dinonaktifkan untuk sementara.</p>
            </div>
        @elseif($success)
            <div class="bg-emerald-50 border border-emerald-200 p-8 rounded-3xl text-center animate-bounce-in max-w-xl mx-auto">
                <svg class="w-16 h-16 text-emerald-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-xl font-bold text-emerald-900 mb-2">Permintaan Terkirim!</h3>
                <p class="text-emerald-700 mb-6">Terima kasih atas laporannya. Tim MDMC DIY akan segera memverifikasi data Anda.</p>
                <button wire:click="$set('success', false)" class="bg-emerald-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">Kirim Laporan Lain</button>
            </div>
        @else
            <form wire:submit="save" class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Pelapor / Penanggung Jawab</label>
                        <input wire:model="nama_pelapor" type="text" placeholder="Contoh: Budi Santoso" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                        @error('nama_pelapor') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp Aktif</label>
                        <input wire:model="no_hp" type="tel" placeholder="Contoh: 081234567890" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                        @error('no_hp') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1 block">Jenis Bantuan yang Dibutuhkan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($options as $option)
                            <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-50 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition-colors group">
                                <input type="checkbox" wire:model="jenis_bantuan" value="{{ $option }}" class="w-5 h-5 rounded-lg border-slate-200 text-red-600 focus:ring-red-500">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">{{ $option }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('jenis_bantuan') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Lokasi Detail (Dusun/RT/RW)</label>
                        <textarea wire:model="lokasi_detail" rows="3" placeholder="Sebutkan lokasi spesifik di mana bantuan dibutuhkan..." class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                        @error('lokasi_detail') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="space-y-4">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Titik Lokasi di Peta</label>
                        <div id="map-public" class="h-[300px] lg:h-[450px] w-full rounded-3xl border border-slate-100 z-0" wire:ignore></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1">Latitude</p>
                                <p class="text-xs font-mono font-bold text-slate-600" x-text="$wire.latitude"></p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1">Longitude</p>
                                <p class="text-xs font-mono font-bold text-slate-600" x-text="$wire.longitude"></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-red-200 flex items-center justify-center gap-3 group">
                            <span wire:loading.remove wire:target="save">Kirim Permintaan Bantuan</span>
                            <span wire:loading wire:target="save">Memproses Laporan...</span>
                            <svg wire:loading.remove wire:target="save" class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <p class="text-[10px] text-slate-400 text-center mt-4 uppercase font-bold tracking-widest">Data Anda akan dijaga kerahasiaannya oleh MDMC DIY</p>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>