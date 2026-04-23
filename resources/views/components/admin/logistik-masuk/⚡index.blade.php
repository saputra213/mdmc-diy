<?php
use Livewire\Component;
use App\Models\BarangMasuk;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with() {
        return [
            'masuks' => BarangMasuk::with(['barang', 'supplier'])->latest()->paginate(10),
        ];
    }
};
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Logistik Masuk</h1>
            <p class="text-slate-500 text-sm">Riwayat penerimaan logistik dari donatur.</p>
        </div>
        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-red-100 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Barang Masuk
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Donatur</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($masuks as $masuk)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $masuk->tanggal_masuk }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $masuk->barang->nama_barang }}</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $masuk->supplier->nama_supplier }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full font-bold">+{{ $masuk->jumlah_masuk }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-red-600 hover:text-red-700 font-bold text-xs uppercase">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $masuks->links() }}
        </div>
    </div>
</div>