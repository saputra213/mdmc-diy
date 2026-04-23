<?php
use Livewire\Component;
use App\Models\Supplier;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with() {
        return [
            'donaturs' => Supplier::latest()->paginate(10),
        ];
    }

    public function delete($id) {
        Supplier::find($id)->delete();
        session()->flash('message', 'Donatur berhasil dihapus.');
    }
};
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Donatur</h1>
            <p class="text-slate-500 text-sm">Kelola daftar donatur dan supplier logistik.</p>
        </div>
        <a href="/donatur/create" wire:navigate class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-red-100 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Donatur
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Nama Donatur</th>
                    <th class="px-6 py-4">Nomor Telepon</th>
                    <th class="px-6 py-4">Alamat</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($donaturs as $donatur)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-700">{{ $donatur->nama_supplier }}</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $donatur->no_telp }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $donatur->alamat }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="/donatur/edit/{{ $donatur->id }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-bold text-xs uppercase mr-3">Edit</a>
                        <button wire:click="delete({{ $donatur->id }})" wire:confirm="Yakin ingin menghapus donatur ini?" class="text-red-600 hover:text-red-700 font-bold text-xs uppercase">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $donaturs->links() }}
        </div>
    </div>
</div>