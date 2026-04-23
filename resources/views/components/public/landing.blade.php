<?php
use Livewire\Component;
use App\Models\Setting;

new class extends Component {
    public function with() {
        return [
            'emergencyMode' => Setting::get('emergency_mode', 'off') === 'on',
            'articles' => [
                ['title' => 'Kesiapsiagaan Menghadapi Gempa Bumi', 'desc' => 'Langkah-langkah penting yang harus dilakukan saat terjadi gempa.', 'img' => 'https://images.unsplash.com/photo-1544724569-5f546fd6f2b5?w=500&q=80'],
                ['title' => 'Manajemen Logistik Efektif', 'desc' => 'Bagaimana MDMC mengelola bantuan untuk efisiensi maksimal.', 'img' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=500&q=80'],
                ['title' => 'Update Cuaca DIY Pekan Ini', 'desc' => 'Prakiraan cuaca dari BMKG untuk wilayah Yogyakarta.', 'img' => 'https://images.unsplash.com/photo-1534088568595-a066f410bcda?w=500&q=80']
            ],
            'videos' => [
                ['title' => 'Mitigasi Banjir', 'url' => '#', 'thumb' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=300&q=80'],
                ['title' => 'P3K Dasar', 'url' => '#', 'thumb' => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=300&q=80']
            ]
        ];
    }
};
?>

<div class="min-h-screen bg-slate-50">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.webp') }}" class="w-16 h-16" alt="Logo">
                <span class="font-bold text-xl tracking-tight text-slate-900">MDMC <span class="text-red-600">DIY</span></span>
            </div>
            <div class="flex items-center gap-6">
                <a href="/login" class="text-slate-500 hover:text-red-600 font-bold text-sm uppercase tracking-wider transition-colors">Login Internal</a>
                @if($emergencyMode)
                    <a href="/request-bantuan" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-red-100">MINTA BANTUAN</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 px-6 lg:px-12 text-center max-w-4xl mx-auto">
        <span class="inline-block bg-red-50 text-red-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Sistem Informasi Logistik Bencana</span>
        <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 mb-6 leading-tight">Membangun Kesiapsiagaan, <span class="text-red-600">Menjangkau Kemanusiaan.</span></h1>
        <p class="text-lg text-slate-500 mb-10">Pusat informasi mitigasi dan manajemen logistik tanggap darurat MDMC DIY untuk masyarakat Yogyakarta.</p>
        
        @if($emergencyMode)
            <a href="/request-bantuan" class="inline-flex items-center gap-3 bg-red-600 hover:bg-red-700 text-white px-10 py-5 rounded-2xl font-bold text-xl transition-all shadow-2xl shadow-red-200 group">
                Minta Bantuan Logistik
                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        @else
            <div class="bg-blue-50 border border-blue-100 text-blue-700 px-8 py-4 rounded-2xl font-medium inline-block">
                Sistem saat ini dalam mode normal. Form bantuan dinonaktifkan.
            </div>
        @endif
    </section>

    <!-- News Section -->
    <section class="py-20 bg-white px-6 lg:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Berita & Informasi</h2>
                    <p class="text-slate-500">Update terbaru seputar kebencanaan di DIY.</p>
                </div>
                <a href="#" class="text-red-600 font-bold hover:underline">Lihat Semua</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($articles as $article)
                <div class="group cursor-pointer">
                    <div class="aspect-video rounded-3xl overflow-hidden mb-6">
                        <img src="{{ $article['img'] }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-red-600 transition-colors">{{ $article['title'] }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">{{ $article['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section class="py-20 px-6 lg:px-12 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <span class="text-red-500 font-bold uppercase tracking-widest text-xs mb-4 block">Edukasi & Mitigasi</span>
                    <h2 class="text-4xl font-bold mb-6">Siap Siaga Sebelum Bencana Datang</h2>
                    <p class="text-slate-400 text-lg mb-10 leading-relaxed">Pengetahuan adalah perlindungan terbaik. Pelajari cara menghadapi berbagai situasi darurat melalui panduan visual kami.</p>
                    
                    <div class="space-y-6">
                        @foreach($videos as $video)
                        <a href="{{ $video['url'] }}" class="flex items-center gap-6 p-4 rounded-3xl hover:bg-white/5 transition-colors border border-white/5 group">
                            <div class="w-24 h-16 rounded-2xl overflow-hidden flex-shrink-0 relative">
                                <img src="{{ $video['thumb'] }}" alt="" class="w-full h-full object-cover opacity-60">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.841z"/></svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold group-hover:text-red-500 transition-colors">{{ $video['title'] }}</h4>
                                <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">Video Tutorial</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-[3rem] bg-gradient-to-br from-red-600 to-red-800 rotate-3 absolute inset-0 opacity-20"></div>
                    <div class="aspect-square rounded-[3rem] bg-white/10 backdrop-blur-xl border border-white/10 relative overflow-hidden flex items-center justify-center p-12">
                        <div class="text-center">
                            <svg class="w-32 h-32 text-red-500 mx-auto mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <h3 class="text-2xl font-bold mb-4 italic">Butuh Bantuan Segera?</h3>
                            <p class="text-slate-400 mb-8 leading-relaxed italic">Jika Anda berada di daerah bencana dan membutuhkan logistik mendesak, hubungi kami melalui form resmi.</p>
                            <a href="/request-bantuan" class="block w-full bg-white text-slate-900 py-4 rounded-2xl font-bold hover:bg-red-50 transition-colors uppercase tracking-widest text-sm">Form Request Bantuan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} MDMC DIY - Muhammadiyah Disaster Management Center</p>
        </div>
    </footer>
</div>