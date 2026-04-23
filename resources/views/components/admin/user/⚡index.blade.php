<?php
use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function delete($id)
    {
        if (auth()->id() === $id) {
            $this->dispatch('visual-feedback', message: 'Anda tidak bisa menghapus akun sendiri!');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        // Visual Feedback for Deaf Users
        $this->dispatch('visual-feedback', message: 'User Berhasil Dihapus!');
    }

    public function with() {
        return [
            'users' => User::latest()->paginate(10),
        ];
    }
};
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
            <p class="text-slate-500 text-sm">Kelola akun pengguna dan hak akses sistem.</p>
        </div>
        <a href="/admin/user/create" wire:navigate class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-red-100 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah User
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4 text-center">Role</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img class="h-8 w-8 rounded-lg bg-slate-100" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=64748b&background=f1f5f9" alt="">
                            <div>
                                <p class="font-bold text-slate-700 leading-none">{{ $user->name }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tight">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span @class(['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider', 'bg-blue-50 text-blue-600' => $user->role === 'admin', 'bg-slate-50 text-slate-600' => $user->role !== 'admin'])>
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5">
                            @if($user->is_active)
                                <span class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Aktif</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-slate-300 ring-4 ring-slate-50"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Nonaktif</span>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                        <a href="/admin/user/edit/{{ $user->id }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-bold text-xs uppercase">Edit</a>
                        @if(auth()->id() !== $user->id)
                            <button 
                                wire:click="delete({{ $user->id }})" 
                                wire:confirm="Yakin ingin menghapus user ini?"
                                class="text-red-600 hover:text-red-700 font-bold text-xs uppercase"
                            >Hapus</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-slate-50">
            {{ $users->links() }}
        </div>
    </div>
</div>