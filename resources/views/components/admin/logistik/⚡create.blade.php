<?php

use Livewire\Component;
use App\Models\Barang;
use App\Models\DisasterEvent;
use App\Models\Jenis;
use App\Models\Satuan;
use Livewire\Attributes\Validate;

new class extends Component
{
    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    #[Validate('required|unique:barangs,id')]
    public $id_barang;

    #[Validate('required|min:3')]
    public $nama_barang;

    #[Validate('required')]
    public $jenis_id;

    #[Validate('required')]
    public $satuan_id;

    public function mount()
    {
        $this->generateId();
    }

    public function generateId()
    {
        $lastBarang = Barang::orderBy('id', 'desc')->first();
        if ($lastBarang) {
            $lastId = $lastBarang->id;
            $number = (int) substr($lastId, 1);
            $newNumber = $number + 1;
            $this->id_barang = 'B' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        } else {
            $this->id_barang = 'B000001';
        }
    }

    public function with()
    {
        $eventId = session('admin_disaster_event_id');
        $event = $eventId ? DisasterEvent::find((int) $eventId) : DisasterEvent::active()->first();
        if ($event && !$eventId) {
            session(['admin_disaster_event_id' => $event->id]);
        }

        return [
            'event' => $event,
            'jenisList' => Jenis::all(),
            'satuanList' => Satuan::all(),
        ];
    }

    public function save()
    {
        $this->validate();

        $eventId = session('admin_disaster_event_id');
        $event = $eventId ? DisasterEvent::find((int) $eventId) : DisasterEvent::active()->first();
        if (!$event) {
            session()->flash('error', 'Buat dan pilih event bencana terlebih dahulu.');
            return;
        }
        if ($event->status !== 'active') {
            session()->flash('error', 'Event terpilih sudah diarsipkan. Pilih event yang Active untuk input data baru.');
            return;
        }

        Barang::create([
            'id' => $this->id_barang,
            'disaster_event_id' => $event->id,
            'nama_barang' => $this->nama_barang,
            'jenis_id' => $this->jenis_id,
            'satuan_id' => $this->satuan_id,
            'stok' => 0,
        ]);

        \App\Events\BarangAdded::dispatch($this->nama_barang);

        session()->flash('message', 'Logistik berhasil ditambahkan.');
        return $this->redirect('/barang', navigate: true);
    }
};
?>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="/barang" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tambah Logistik</h1>
                <p class="text-slate-500">Masukkan detail barang baru.</p>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if($event)
            <div class="bg-white border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-semibold shadow-sm">
                Event: <span class="font-extrabold">{{ $event->name }}</span>
            </div>
        @endif

        <form wire:submit="save" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-6">
            <div class="space-y-2">
                <label for="id_barang" class="text-sm font-semibold text-slate-700">ID Logistik</label>
                <input wire:model="id_barang" type="text" id="id_barang" readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none font-mono">
                @error('id_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label for="nama_barang" class="text-sm font-semibold text-slate-700">Nama Logistik</label>
                <input wire:model="nama_barang" type="text" id="nama_barang" placeholder="Contoh: Tenda Darurat" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none">
                @error('nama_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="jenis_id" class="text-sm font-semibold text-slate-700">Jenis Logistik</label>
                    <select wire:model="jenis_id" id="jenis_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none bg-white">
                        <option value="">Pilih Jenis</option>
                        @foreach($jenisList as $jenis)
                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                        @endforeach
                    </select>
                    @error('jenis_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label for="satuan_id" class="text-sm font-semibold text-slate-700">Satuan Logistik</label>
                    <select wire:model="satuan_id" id="satuan_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-mdmc-600 focus:border-mdmc-600 transition-all outline-none bg-white">
                        <option value="">Pilih Satuan</option>
                        @foreach($satuanList as $satuan)
                            <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                    @error('satuan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-mdmc-700 hover:bg-mdmc-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-mdmc-200 flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="save" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Simpan Data
                </button>
                <button type="reset" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                    Reset
                </button>
            </div>
        </form>
    </div>
