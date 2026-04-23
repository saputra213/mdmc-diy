<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MDMC DIY') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body {
            font-family: 'Geist', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased h-full overflow-hidden">
    <div class="flex h-screen overflow-hidden" x-data="{ 
        sidebarOpen: {{ auth()->check() ? 'true' : 'false' }},
        visualMode: localStorage.getItem('visual-mode') === 'true',
        flash: false,
        message: '',
        
        toggleVisual() {
            this.visualMode = !this.visualMode;
            localStorage.setItem('visual-mode', this.visualMode);
            Swal.fire({
                title: 'Mode Visual ' + (this.visualMode ? 'Aktif' : 'Nonaktif'),
                text: this.visualMode ? 'Layar akan berkedip kuning saat ada notifikasi baru.' : 'Notifikasi akan berjalan normal.',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        },

        triggerFlash(msg) {
            this.message = msg;
            if (this.visualMode) {
                this.flash = true;
                setTimeout(() => this.flash = false, 500);
            }
            
            Swal.fire({
                title: 'Notifikasi',
                text: msg,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
        },

        logout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Anda akan keluar dari sistem manajemen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/logout';
                }
            })
        }
    }" @visual-feedback.window="triggerFlash($event.detail.message)">
        
        <!-- Flash Overlay for Deaf Accessibility -->
        <div x-show="flash" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-yellow-400/40 z-[9999] pointer-events-none" 
             x-cloak>
        </div>

        <!-- Sidebar -->
        @auth
        <aside class="w-64 bg-white border-r border-slate-200 flex-shrink-0 flex flex-col hidden lg:flex">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="bg-red-600 p-1.5 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight leading-none">MANAJEMEN</h1>
                    <h1 class="font-bold text-lg tracking-tight text-red-600 leading-tight">LOGISTIK</h1>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow py-6 overflow-y-auto px-4 space-y-8">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <a href="/dashboard" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('dashboard') ? 'bg-slate-50 text-red-600' : '' }}">
                        <svg class="w-5 h-5 {{ request()->is('dashboard') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </div>

                <!-- Data Master -->
                <div class="space-y-2">
                    <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Master</p>
                    <div class="space-y-1">
                        <a href="/donatur" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('donatur*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('donatur*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Donatur
                        </a>
                        <a href="/barang" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('barang') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('barang') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Logistik
                        </a>
                        <a href="/lokasi" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('lokasi*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('lokasi*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lokasi Bencana
                        </a>
                    </div>
                </div>

                <!-- Manajemen Logistik -->
                <div class="space-y-2">
                    <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Manajemen Logistik</p>
                    <div class="space-y-1">
                        <a href="/barang-masuk" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('barang-masuk*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('barang-masuk*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                            Logistik Masuk
                        </a>
                        <a href="/barang-keluar" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('barang-keluar*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('barang-keluar*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                            </svg>
                            Logistik Keluar
                        </a>
                        <a href="/kebutuhan" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('kebutuhan*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('kebutuhan*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Logistik Kebutuhan
                        </a>
                        <a href="/admin/bantuan-request" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('admin/bantuan-request*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('admin/bantuan-request*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Permintaan Masyarakat
                        </a>
                    </div>
                </div>

                <!-- Report -->
                <div class="space-y-2">
                    <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Report</p>
                    <div class="space-y-1">
                        <a href="/laporan" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('laporan*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('laporan*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Laporan
                        </a>
                    </div>
                </div>

                <!-- Settings -->
                <div class="space-y-2">
                    <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Settings</p>
                    <div class="space-y-1">
                        <a href="/user" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-slate-50 rounded-xl transition-colors font-medium {{ request()->is('user*') ? 'bg-slate-50 text-red-600' : '' }}">
                            <svg class="w-5 h-5 {{ request()->is('user*') ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            User Management
                        </a>
                    </div>
                </div>
            </nav>
        </aside>
        @endauth

        <!-- Main Content -->
        <div class="flex-grow flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            @auth
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0">
                <div class="flex items-center gap-4 lg:hidden">
                    <!-- Mobile Menu Button could go here -->
                </div>
                
                <div class="flex items-center gap-6 ml-auto">
                    <!-- Accessibility Toggle -->
                    <button @click="toggleVisual()" 
                        class="p-2 rounded-full transition-colors relative group" 
                        :class="visualMode ? 'text-yellow-600 bg-yellow-50 hover:bg-yellow-100' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                        title="Visual Feedback Mode">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="absolute hidden group-hover:block w-max bg-slate-800 text-white text-[10px] p-1.5 rounded -bottom-10 left-1/2 -translate-x-1/2 z-50">Mode Visual (Tunarungu): <span x-text="visualMode ? 'AKTIF' : 'NONAKTIF'"></span></span>
                    </button>

                    <div class="flex items-center gap-3 border-l pl-6 border-slate-200">
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-700 leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ auth()->user()->email ?? 'admin@mdmc.id' }}</p>
                        </div>
                        <button @click="logout()" class="relative group">
                            <img class="h-10 w-10 rounded-xl bg-slate-100 border border-slate-200 group-hover:opacity-50 transition-opacity" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&color=ef4444&background=fee2e2" alt="">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </div>
                        </button>
                    </div>
                </div>
            </header>
            @endauth

            <!-- Page Content -->
            <main @class(['flex-grow overflow-y-auto', 'p-8' => auth()->check()])>
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-100 py-4 px-8">
                <p class="text-center text-slate-400 text-xs font-medium">
                    &copy; {{ date('Y') }} MDMC DIY - Sistem Manajemen Logistik Tanggap Darurat
                </p>
            </footer>
        </div>
    </div>

    @livewireScripts
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>