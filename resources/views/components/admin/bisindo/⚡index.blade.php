<?php
use Livewire\Component;
use App\Models\BisindoMaterial;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'materials' => BisindoMaterial::query()->latest()->paginate(10),
        ];
    }

    public function delete($id)
    {
        BisindoMaterial::query()->whereKey($id)->delete();
        session()->flash('message', 'Materi berhasil dihapus.');
    }
};
?>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Materi Belajar BISINDO</h1>
            <p class="text-slate-500 text-sm">Kelola poster/gambar materi, kategori, dan tingkat kesulitan.</p>
        </div>
        <a href="/admin/bisindo/create" wire:navigate class="bg-mdmc-700 hover:bg-mdmc-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-mdmc-100 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Materi
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
                    <th class="px-6 py-4">Judul</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Tingkat</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($materials as $m)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-700">{{ $m->judul }}</p>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-1">{{ $m->gambar_url }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-bold">{{ $m->kategori }}</td>
                        <td class="px-6 py-4 text-slate-600 font-bold">{{ $m->tingkat }}</td>
                        <td class="px-6 py-4">
                            @if($m->is_active)
                                <span class="inline-flex bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-bold text-xs">Aktif</span>
                            @else
                                <span class="inline-flex bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/bisindo/edit/{{ $m->id }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-bold text-xs uppercase mr-3">Edit</a>
                            <button wire:click="delete({{ $m->id }})" wire:confirm="Yakin ingin menghapus materi ini?" class="text-red-600 hover:text-red-700 font-bold text-xs uppercase">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $materials->links() }}
        </div>
    </div>
</div>
