<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public $name;
    public $email;
    public $username;
    public $password;
    public $role = 'masyarakat';
    public $is_active = true;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:4',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,masyarakat',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_active' => $this->is_active,
        ]);

        // Visual Feedback for Deaf Users
        $this->dispatch('visual-feedback', message: 'User Baru Berhasil Dibuat!');

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
            <h1 class="text-2xl font-bold text-slate-900">Tambah User</h1>
            <p class="text-slate-500 text-sm">Buat akun pengguna baru dalam sistem.</p>
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
                <label class="text-sm font-bold text-slate-700 ml-1">Password</label>
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
            <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white font-bold py-4 rounded-2xl shadow-lg shadow-mdmc-100 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Buat Akun User
            </button>
        </div>
    </form>
</div>
