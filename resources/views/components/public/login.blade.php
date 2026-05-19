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

<div class="min-h-screen bg-[#F6F9FF]">
    <nav class="bg-gradient-to-b from-[#0B1B43]/95 via-[#162E67]/95 to-[#233876]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.webp') }}';" class="w-12 h-12" alt="Logo">
                <div class="leading-tight">
                    <p class="font-extrabold text-white text-lg tracking-tight">mdmc <span class="text-blue-200">DIY</span></p>
                    <p class="text-[10px] text-blue-100/80 font-bold uppercase tracking-widest">Muhammadiyah Disaster Management Center</p>
                </div>
            </a>
            <a href="/" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors">
                Kembali
            </a>
        </div>
    </nav>

    <header class="bg-gradient-to-br from-[#142B63] via-[#233876] to-[#142B63]">
        <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-10">
            <p class="text-blue-100/80 text-xs font-extrabold uppercase tracking-widest">Login</p>
            <h1 class="mt-2 text-3xl lg:text-4xl font-extrabold text-white">Login Internal</h1>
            <p class="mt-3 text-blue-100/90 font-semibold max-w-3xl">Gunakan akun MDMC DIY Anda untuk masuk.</p>
        </div>
    </header>

    <main class="px-6 lg:px-12 py-10">
        <div class="max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Akses Panel</p>
                    <p class="mt-2 text-xl font-extrabold text-slate-900">Masuk ke Dashboard</p>
                </div>

                <form wire:submit="login" class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input wire:model="username" type="text" placeholder="admin" class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                        </div>
                        @error('username') <span class="text-red-600 text-[10px] font-extrabold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input wire:model="password" type="password" placeholder="••••••••" class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-[#1E66FF]/10 focus:border-[#1E66FF] outline-none transition-all">
                        </div>
                        @error('password') <span class="text-red-600 text-[10px] font-extrabold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#1E66FF] hover:bg-[#175AE2] text-white py-4 rounded-2xl font-extrabold transition-all shadow-lg shadow-blue-900/15 flex items-center justify-center gap-3">
                        <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                        <span wire:loading wire:target="login">Memverifikasi...</span>
                        <svg wire:loading.remove wire:target="login" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Aksesibilitas</p>
                    <p class="mt-2 text-xl font-extrabold text-slate-900">Ramah Tunarungu</p>
                </div>
                <div class="p-6 space-y-3 text-slate-600 font-semibold">
                    <p>Gunakan fitur aksesibilitas untuk mengatur zoom, kontras, dan tipografi.</p>
                    <p>Untuk masyarakat umum, kembali ke beranda untuk mengakses menu publik.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-[#233876] text-white">
        <div class="border-t border-white/10">
            <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-6 text-blue-100/80 text-sm font-semibold flex flex-col md:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} MDMC DIY</p>
                <p>Ramah difabel tunarungu • Visual-first</p>
            </div>
        </div>
    </footer>
</div>
