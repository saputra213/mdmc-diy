<?php

use App\Models\DisasterEvent;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|string|min:3|max:120')]
    public $name = '';

    #[Validate('nullable|string|max:160')]
    public $location = '';

    #[Validate('nullable|integer|min:0|max:100000000')]
    public $affected_houses;

    public $set_active = true;

    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    public function mount(): void
    {
        $this->set_active = true;
    }

    private function emergencyMode(): bool
    {
        return Setting::get('emergency_mode', 'off') === 'on';
    }

    public function with(): array
    {
        return [
            'emergencyMode' => $this->emergencyMode(),
            'activeEvent' => DisasterEvent::active()->first(),
            'activeEvents' => DisasterEvent::query()->where('status', 'active')->orderByDesc('updated_at')->get(),
            'archivedCount' => DisasterEvent::query()->where('status', 'archived')->count(),
        ];
    }

    public function createEvent(): void
    {
        $this->validate();

        $event = DisasterEvent::create([
            'name' => $this->name,
            'location' => $this->location ?: null,
            'affected_houses' => $this->affected_houses === null || $this->affected_houses === '' ? null : (int) $this->affected_houses,
            'status' => 'archived',
        ]);

        $this->reset(['name', 'location', 'affected_houses']);

        if ($this->set_active || ($this->emergencyMode() && !DisasterEvent::active()->exists())) {
            $this->activate($event->id);
            return;
        }

        session()->flash('message', 'Event bencana berhasil dibuat.');
        $this->dispatch('disaster-event-changed');
    }

    public function activate(int $id): void
    {
        DisasterEvent::query()->whereKey($id)->update(['status' => 'active']);

        session(['admin_disaster_event_id' => $id]);

        session()->flash('message', 'Event berhasil diaktifkan.');
        $this->dispatch('disaster-event-changed');
    }

    public function archive(int $id): void
    {
        $event = DisasterEvent::findOrFail($id);

        if ($event->status === 'active' && $this->emergencyMode()) {
            $activeCount = DisasterEvent::active()->count();
            if ($activeCount <= 1) {
                session()->flash('error', 'Tidak bisa mengarsipkan event aktif terakhir saat Emergency Mode menyala. Aktifkan minimal 1 event lain atau matikan Emergency Mode terlebih dahulu.');
                return;
            }
        }

        $event->update(['status' => 'archived']);

        if ((int) session('admin_disaster_event_id') === (int) $event->id) {
            $next = DisasterEvent::active()->value('id') ?: DisasterEvent::orderByDesc('id')->value('id');
            session(['admin_disaster_event_id' => $next]);
        }

        session()->flash('message', 'Event berhasil diarsipkan.');
        $this->dispatch('disaster-event-changed');
    }
};

?>

<div class="space-y-8">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Bencana</h1>
            <p class="text-slate-500 text-sm">Buat event bencana baru dan tentukan event mana yang Active. Admin bisa memilih event yang ingin dilihat via dropdown filter.</p>
        </div>
        <a href="/disaster-events/history" class="bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-xl font-bold transition-all shadow-sm border border-slate-200 text-sm">
            Buka History ({{ $archivedCount }})
        </a>
    </div>

    @if($emergencyMode && !$activeEvent)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl font-semibold">
            Emergency Mode sedang aktif, tetapi belum ada event yang berstatus Active. Buat event lalu set sebagai Active.
        </div>
    @endif

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100">
            <h2 class="text-lg font-extrabold text-slate-900">Buat Event Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Nama event contoh: Banjir Jogja 2026.</p>
        </div>
        <form wire:submit="createEvent" class="p-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Event</label>
                <input wire:model="name" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('name') <div class="text-red-600 text-[10px] font-bold uppercase tracking-widest ml-1">{{ $message }}</div> @enderror
            </div>

            <div class="lg:col-span-4 space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Lokasi</label>
                <input wire:model="location" type="text" placeholder="Contoh: Sleman, DIY" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('location') <div class="text-red-600 text-[10px] font-bold uppercase tracking-widest ml-1">{{ $message }}</div> @enderror
            </div>

            <div class="lg:col-span-2 space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Rumah Terdampak</label>
                <input wire:model="affected_houses" type="number" min="0" placeholder="Contoh: 1125" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('affected_houses') <div class="text-red-600 text-[10px] font-bold uppercase tracking-widest ml-1">{{ $message }}</div> @enderror
            </div>

            <div class="lg:col-span-2 flex flex-col justify-end gap-3">
                <label class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-slate-200 bg-white cursor-pointer select-none">
                    <input type="checkbox" wire:model="set_active" class="w-5 h-5 rounded-lg border-slate-300 text-mdmc-700 focus:ring-mdmc-600">
                    <span class="text-sm font-bold text-slate-700">Set Active</span>
                </label>
                <button type="submit" class="bg-mdmc-700 hover:bg-mdmc-800 text-white px-5 py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-mdmc-100">
                    Simpan Event
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Event Aktif</h2>
                <p class="text-sm text-slate-500 mt-1">Bisa lebih dari satu. Publik bisa memilih event saat mengirim Emergency Call.</p>
            </div>
            @if($activeEvent)
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold uppercase tracking-widest">
                    ● Active
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-extrabold uppercase tracking-widest">
                    Tidak ada
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-extrabold">
                    <tr>
                        <th class="px-8 py-4">Nama</th>
                        <th class="px-8 py-4">Lokasi</th>
                        <th class="px-8 py-4">Rumah</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activeEvents as $event)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-8 py-5">
                                <p class="font-extrabold text-slate-900">{{ $event->name }}</p>
                                <p class="text-xs text-slate-400 font-semibold mt-1">ID: {{ $event->id }}</p>
                            </td>
                            <td class="px-8 py-5 text-slate-600 font-semibold">{{ $event->location ?: '-' }}</td>
                            <td class="px-8 py-5 text-slate-600 font-semibold">{{ $event->affected_houses !== null ? number_format($event->affected_houses) : '-' }}</td>
                            <td class="px-8 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold uppercase tracking-widest">Active</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button wire:click="archive({{ $event->id }})" class="text-slate-700 hover:text-slate-900 font-extrabold text-xs uppercase tracking-widest">
                                    Arsipkan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-slate-500 font-semibold">
                                Belum ada event aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
