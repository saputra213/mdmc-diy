<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public $user;
    public $name;
    public $email;
    public $username;
    public $password;
    public $role;
    public $is_active;

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->username = $this->user->username;
        $this->role = $this->user->role;
        $this->is_active = $this->user->is_active;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'username' => 'required|string|min:4|unique:users,username,' . $this->user->id,
            'role' => 'required|in:admin,masyarakat',
        ];

        if ($this->password) {
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        // Visual Feedback for Deaf Users
        $this->dispatch('visual-feedback', message: 'Data User Berhasil Diperbarui!');

        return $this->redirect('/admin/user', navigate: true);
    }
};
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/admin/user" wire:navigate class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit User</h1>
            <p class="text-slate-500 text-sm">Perbarui informasi akun pengguna.</p>
        </div>
    </div>

    <form wire:submit="save" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap</label>
                <input type="text" wire:model="name" placeholder="Nama Lengkap" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('name') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                <input type="email" wire:model="email" placeholder="email@example.com" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('email') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Username</label>
                <input type="text" wire:model="username" placeholder="username" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('username') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" wire:model="password" placeholder="********" 
                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                @error('password') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Role</label>
                <select wire:model="role" class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 transition-all">
                    <option value="admin">Admin (Akses Penuh)</option>
                    <option value="masyarakat">Masyarakat (Akses Terbatas)</option>
                </select>
                @error('role') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 ml-1">Status Akun</label>
                <div class="flex items-center h-[50px] px-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-600">Akun Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-100 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Simpan Perubahan User
            </button>
        </div>
    </form>
</div>