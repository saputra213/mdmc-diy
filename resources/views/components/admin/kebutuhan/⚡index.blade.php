<?php
use Livewire\Component;
use App\Models\Kebutuhan;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function delete($id)
    {
        $kebutuhan = Kebutuhan::findOrFail($id);
        $kebutuhan->delete();

        // Visual Feedback for Deaf Users
        $this->dispatch('visual-feedback', message: 'Data Kebutuhan Berhasil Dihapus!');
    }

    public function with() {
        return [
            'kebutuhans' => Kebutuhan::latest()->paginate(10),
        ];
    }
};
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Logistik Kebutuhan</h1>
            <p class="text-slate-500 text-sm">Daftar kebutuhan logistik di lokasi bencana.</p>
        </div>
        <a href="/admin/kebutuhan/create" wire:navigate class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-red-100 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kebutuhan
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4 text-center">Jml Korban</th>
                    <th class="px-6 py-4">Barang Dibutuhkan</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($kebutuhans as $kebutuhan)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $kebutuhan->nama_lokasi }}</td>
                    <td class="px-6 py-4 text-center text-slate-500 font-medium">{{ $kebutuhan->jumlah_korban }} Jiwa</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $kebutuhan->nama_barang }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-slate-900">{{ $kebutuhan->butuh_barang }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($kebutuhan->status)
                            <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Terpenuhi</span>
                        @else
                            <span class="bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                        <a href="/admin/kebutuhan/edit/{{ $kebutuhan->id }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-bold text-xs uppercase">Edit</a>
                        <button 
                            wire:click="delete({{ $kebutuhan->id }})" 
                            wire:confirm="Yakin ingin menghapus data ini?"
                            class="text-red-600 hover:text-red-700 font-bold text-xs uppercase"
                        >Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $kebutuhans->links() }}
        </div>
    </div>
</div>