<?php
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required')]
    public $username;

    #[Validate('required')]
    public $password;

    public function login() {
        $this->validate();

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->regenerate();
            return $this->redirectIntended('/dashboard', navigate: true);
        }

        $this->addError('username', 'Username atau password salah.');
    }
};
?>

<div class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <div class=" p-3 rounded-2xl inline-block mb-6 shadow-xl shadow-white-100">
                <img src="{{ asset('images/logo.webp') }}" class="w-10 h-10" alt="Logo">
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Login Internal</h1>
            <p class="text-slate-500 mt-2">Gunakan akun MDMC DIY Anda untuk masuk.</p>
        </div>

        <form wire:submit="login" class="bg-white p-8 lg:p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input wire:model="username" type="text" placeholder="admin" class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                </div>
                @error('username') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input wire:model="password" type="password" placeholder="••••••••" class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                </div>
                @error('password') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 group">
                    <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                    <span wire:loading wire:target="login">Memverifikasi...</span>
                    <svg wire:loading.remove wire:target="login" class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
                <a href="/" class="block text-center text-slate-400 hover:text-red-600 mt-6 font-bold text-[10px] uppercase tracking-widest transition-colors">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
</div>