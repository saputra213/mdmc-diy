<?php

use Livewire\Component;
use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Satuan;
use Livewire\Attributes\Validate;

new class extends Component
{
    public $id;
    
    public $id_barang;

    #[Validate('required|min:3')]
    public $nama_barang;

    #[Validate('required')]
    public $jenis_id;

    #[Validate('required')]
    public $satuan_id;

    public function mount($id)
    {
        $barang = Barang::findOrFail($id);
        $this->id = $id;
        $this->id_barang = $barang->id;
        $this->nama_barang = $barang->nama_barang;
        $this->jenis_id = $barang->jenis_id;
        $this->satuan_id = $barang->satuan_id;
    }

    public function with()
    {
        return [
            'jenisList' => Jenis::all(),
            'satuanList' => Satuan::all(),
        ];
    }

    public function update()
    {
        $this->validate();

        $barang = Barang::findOrFail($this->id);
        $barang->update([
            'nama_barang' => $this->nama_barang,
            'jenis_id' => $this->jenis_id,
            'satuan_id' => $this->satuan_id,
        ]);

        session()->flash('message', 'Logistik berhasil diperbarui.');
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
                <h1 class="text-2xl font-bold text-slate-900">Edit Logistik</h1>
                <p class="text-slate-500">Perbarui detail barang: {{ $id_barang }}</p>
            </div>
        </div>

        <form wire:submit="update" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-6">
            <div class="space-y-2">
                <label for="id_barang" class="text-sm font-semibold text-slate-700">ID Logistik</label>
                <input wire:model="id_barang" type="text" id="id_barang" readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 transition-all outline-none font-mono">
            </div>

            <div class="space-y-2">
                <label for="nama_barang" class="text-sm font-semibold text-slate-700">Nama Logistik</label>
                <input wire:model="nama_barang" type="text" id="nama_barang" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none">
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
                    <select wire:model="satuan_id" id="satuan_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none bg-white">
                        <option value="">Pilih Satuan</option>
                        @foreach($satuanList as $satuan)
                            <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                    @error('satuan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="update" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="/barang" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 transition-colors flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
