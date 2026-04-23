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
    <style>
        body {
            font-family: 'Geist', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased overflow-hidden">
    <div class="min-h-screen flex items-center justify-center p-6 lg:p-12 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-blue-100 rounded-full blur-3xl opacity-50"></div>

        <main class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden flex flex-col md:flex-row relative z-10">
            <div class="flex-1 p-8 lg:p-16 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-8">
                    <div class="bg-red-600 p-2 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900">MDMC <span class="text-red-600">DIY</span></span>
                </div>

                <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4 italic">
                    Logistik Tanggap <span class="text-red-600 underline decoration-red-200 underline-offset-8">Darurat</span>.
                </h1>
                
                <p class="text-lg text-slate-500 mb-10 max-w-md">
                    Sistem manajemen logistik modern, ringan, dan aksesibel untuk mendukung kemanusiaan.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/dashboard" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-red-200 flex items-center justify-center gap-2 group">
                        Mulai Sekarang
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="https://mdmc.or.id" target="_blank" class="px-8 py-4 border border-slate-200 text-slate-600 rounded-2xl font-bold text-lg hover:bg-slate-50 transition-colors text-center">
                        Tentang MDMC
                    </a>
                </div>

                <div class="mt-12 flex items-center gap-4 border-t border-slate-100 pt-8">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Admin&background=FEE2E2&color=B91C1C" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User&background=DBEAFE&color=1D4ED8" alt="">
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-400">+5</div>
                    </div>
                    <p class="text-sm text-slate-400">Dipercaya oleh tim relawan DIY</p>
                </div>
            </div>

            <div class="hidden md:flex md:w-2/5 bg-slate-900 p-8 flex-col justify-end relative">
                <div class="absolute inset-0 opacity-20 overflow-hidden">
                    <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 L100 0 L100 100 Z" />
                    </svg>
                </div>
                
                <div class="relative z-10 space-y-4">
                    <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                        <p class="text-white font-medium text-sm mb-1 italic">"Sangat cepat dan mudah digunakan saat di lapangan."</p>
                        <p class="text-slate-400 text-xs">- Tim Logistik Bantul</p>
                    </div>
                    
                    <div class="flex items-center gap-3 text-slate-400 text-xs">
                        <div class="flex gap-1">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span>v{{ app()->version() }}</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>