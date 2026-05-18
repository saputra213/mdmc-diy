<?php
use Livewire\Component;
use App\Models\BantuanRequest;
use App\Models\DisasterEvent;
use App\Models\Setting;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    private function currentEvent(): ?DisasterEvent
    {
        $selected = session('admin_disaster_event_id');
        if ($selected) {
            return DisasterEvent::find((int) $selected);
        }

        $fallbackId = DisasterEvent::active()->value('id') ?: DisasterEvent::orderByDesc('id')->value('id');
        if ($fallbackId) {
            session(['admin_disaster_event_id' => (int) $fallbackId]);
            return DisasterEvent::find((int) $fallbackId);
        }

        return null;
    }

    public function with() {
        $event = $this->currentEvent();
        $query = BantuanRequest::latest();
        if ($event) {
            $query->where('disaster_event_id', $event->id);
        } else {
            $query->whereRaw('1=0');
        }

        return [
            'event' => $event,
            'requests' => $query->paginate(10),
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on'
        ];
    }

    public function toggleEmergency() {
        $current = Setting::get('emergency_mode', 'off');
        $next = $current === 'on' ? 'off' : 'on';

        if ($next === 'on' && !DisasterEvent::active()->exists()) {
            session()->flash('error', 'Tidak bisa mengaktifkan Emergency Mode karena belum ada event bencana yang Active.');
            return;
        }

        Setting::set('emergency_mode', $next, 'boolean');
        session()->flash('message', 'Mode Darurat berhasil diperbarui.');
    }

    public function updateStatus($id, $status) {
        $request = BantuanRequest::findOrFail($id);
        $event = DisasterEvent::find($request->disaster_event_id);
        if (!$event || $event->status !== 'active') {
            session()->flash('error', 'Tidak bisa memproses permintaan karena event sudah diarsipkan atau tidak ditemukan.');
            return;
        }
        $request->update(['status' => $status]);
        
        $this->dispatch('visual-feedback', message: 'Status permintaan berhasil diperbarui.');
    }

    public function showMap($lat, $lng) {
        $this->dispatch('open-map-modal', lat: $lat, lng: $lng);
    }
};
?>

<div class="space-y-8" x-data="{
    openMap(lat, lng) {
        Swal.fire({
            title: 'Lokasi Permintaan',
            html: '<div id=&quot;map-modal&quot; style=&quot;height: 300px; width: 100%; border-radius: 1rem;&quot;></div>',
            width: '600px',
            showConfirmButton: false,
            didOpen: () => {
                const map = L.map('map-modal').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                L.marker([lat, lng]).addTo(map);
            }
        });
    }
}" @open-map-modal.window="openMap($event.detail.lat, $event.detail.lng)">
    @if(!$event)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl font-semibold">
            Belum ada event bencana. Buat event terlebih dahulu di menu Manajemen Bencana.
        </div>
    @elseif($event->status !== 'active')
        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-5 py-4 rounded-2xl font-semibold">
            Event terpilih diarsipkan. Input publik dinonaktifkan untuk event ini.
        </div>
    @else
        <div class="bg-white border border-slate-200 text-slate-700 px-5 py-4 rounded-2xl font-semibold shadow-sm">
            Event: <span class="font-extrabold">{{ $event->name }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Emergency Control Panel -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6">
            <div @class(['p-4 rounded-2xl transition-colors', 'bg-red-50 text-red-600' => $emergencyMode, 'bg-slate-50 text-slate-400' => !$emergencyMode])>
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Emergency Toggle Control</h2>
                <p class="text-slate-500 text-sm">Aktifkan untuk membuka form permintaan bantuan di landing page publik.</p>
            </div>
        </div>
        <button wire:click="toggleEmergency" @class(['px-8 py-4 rounded-2xl font-bold transition-all shadow-xl flex items-center gap-3', 'bg-red-600 text-white shadow-red-100' => $emergencyMode, 'bg-slate-900 text-white shadow-slate-200' => !$emergencyMode])>
            <div @class(['w-3 h-3 rounded-full animate-pulse', 'bg-white' => $emergencyMode, 'bg-slate-400' => !$emergencyMode])></div>
            {{ $emergencyMode ? 'MODE DARURAT: AKTIF' : 'AKTIFKAN MODE DARURAT' }}
        </button>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-lg font-bold text-slate-900">Daftar Permintaan Bantuan Masyarakat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5">Pelapor</th>
                        <th class="px-8 py-5">Lokasi Detail</th>
                        <th class="px-8 py-5">Jenis Bantuan</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($requests as $request)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <p class="font-bold text-slate-700 leading-none">{{ $request->nama_pelapor }}</p>
                            <p class="text-xs text-slate-400 mt-2 font-medium">{{ $request->no_hp }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-slate-500 max-w-xs truncate">{{ $request->lokasi_detail }}</p>
                            @if($request->latitude && $request->longitude)
                                <button wire:click="showMap('{{ $request->latitude }}', '{{ $request->longitude }}')" class="mt-2 flex items-center gap-1.5 text-[10px] font-bold text-red-600 hover:text-red-700 uppercase tracking-widest">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lihat Peta
                                </button>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1">
                                @foreach($request->jenis_bantuan as $item)
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">{{ $item }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @if($request->status === 'pending')
                                <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Pending</span>
                            @elseif($request->status === 'approved')
                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Disetujui</span>
                            @else
                                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            @if($request->status === 'pending')
                                <button wire:click="updateStatus({{ $request->id }}, 'approved')" class="bg-emerald-600 text-white p-2 rounded-xl hover:bg-emerald-700 transition-colors mr-2" title="Setujui">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="updateStatus({{ $request->id }}, 'rejected')" class="bg-red-600 text-white p-2 rounded-xl hover:bg-red-700 transition-colors" title="Tolak">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Terproses</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 font-medium italic">Belum ada permintaan bantuan yang masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-slate-50 bg-slate-50/30">
            {{ $requests->links() }}
        </div>
    </div>
</div>
