<?php
use Livewire\Component;
use App\Models\Lokasi;
use Livewire\Attributes\Validate;

new class extends Component {
    public $id;

    #[Validate('required|min:3')]
    public $nama_lokasi;

    #[Validate('required')]
    public $alamat;

    public $latitude;
    public $longitude;

    public function mount($id) {
        $lokasi = Lokasi::findOrFail($id);
        $this->id = $id;
        $this->nama_lokasi = $lokasi->nama_lokasi;
        $this->alamat = $lokasi->alamat;
        $this->latitude = $lokasi->latitude ?? '-7.7956';
        $this->longitude = $lokasi->longitude ?? '110.3695';
    }

    public function update() {
        $this->validate();
        $lokasi = Lokasi::findOrFail($this->id);
        $lokasi->update([
            'nama_lokasi' => $this->nama_lokasi,
            'alamat' => $this->alamat,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
        
        $this->dispatch('visual-feedback', message: 'Data lokasi berhasil diperbarui!');
        return $this->redirect('/lokasi', navigate: true);
    }
};
?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/lokasi" wire:navigate class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Lokasi</h1>
            <p class="text-slate-500 text-sm">Perbarui informasi lokasi: {{ $nama_lokasi }}</p>
        </div>
    </div>

    <form wire:submit="update" class="bg-white p-8 lg:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="space-y-8">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lokasi / Posko</label>
                <input wire:model="nama_lokasi" type="text" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('nama_lokasi') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Alamat Lengkap Lokasi</label>
                <textarea wire:model="alamat" rows="3" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                @error('alamat') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Latitude</label>
                    <input wire:model="latitude" type="text" readonly class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed text-xs font-mono">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Longitude</label>
                    <input wire:model="longitude" type="text" readonly class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed text-xs font-mono">
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" class="flex-1 bg-mdmc-700 hover:bg-mdmc-800 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3 group">
                    Simpan Perubahan
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
                <a href="/lokasi" wire:navigate class="px-8 py-5 border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-colors">Batal</a>
            </div>
        </div>

        <div class="space-y-4">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Titik di Peta</label>
            <div id="map" class="h-[400px] lg:h-full min-h-[400px] w-full rounded-3xl border border-slate-100 z-0" wire:ignore></div>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const mapElement = document.getElementById('map');
            if (!mapElement) return;

            const lat = @js($latitude);
            const lng = @js($longitude);

            const map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                @this.set('latitude', position.lat.toFixed(6));
                @this.set('longitude', position.lng.toFixed(6));
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                @this.set('latitude', e.latlng.lat.toFixed(6));
                @this.set('longitude', e.latlng.lng.toFixed(6));
            });
        });
    </script>
</div>
