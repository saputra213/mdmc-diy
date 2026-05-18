<?php

use Livewire\Component;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\DisasterEvent;
use App\Models\Supplier;
use App\Models\User;
use App\Services\LogistikService;
use Livewire\Attributes\On;

new class extends Component
{
    protected $listeners = [
        'disaster-event-changed' => '$refresh',
    ];

    private function currentEventId(): ?int
    {
        $selected = session('admin_disaster_event_id');
        if ($selected) {
            return (int) $selected;
        }

        return DisasterEvent::active()->value('id') ?: DisasterEvent::orderByDesc('id')->value('id');
    }

    public function with(LogistikService $logistikService)
    {
        $eventId = $this->currentEventId();
        $barangQuery = Barang::query()->where('disaster_event_id', $eventId);
        $masukQuery = BarangMasuk::query()->where('disaster_event_id', $eventId);
        $keluarQuery = BarangKeluar::query()->where('disaster_event_id', $eventId);

        return [
            'activeEvent' => $eventId ? DisasterEvent::find($eventId) : null,
            'totalBarang' => $eventId ? $barangQuery->count() : 0,
            'totalDonatur' => Supplier::count(),
            'totalStok' => $eventId ? $barangQuery->sum('stok') : 0,
            'totalUser' => User::count(),
            'recentMasuk' => $eventId ? $masukQuery->with(['barang', 'supplier'])->latest()->take(5)->get() : collect(),
            'recentKeluar' => $eventId ? $keluarQuery->with(['barang', 'lokasi'])->latest()->take(5)->get() : collect(),
            'stokMinimum' => $eventId ? Barang::where('disaster_event_id', $eventId)->where('stok', '<=', 5)->take(5)->get() : collect(),
            'externalStok' => $logistikService->getStokLogistik(), // Mocking data from service
            
            // Chart Data
            'chartData' => $this->getChartData(),
            'donutData' => [
                'masuk' => $eventId ? $masukQuery->sum('jumlah_masuk') : 0,
                'keluar' => $eventId ? $keluarQuery->sum('jumlah_keluar') : 0,
            ]
        ];
    }

    private function getChartData()
    {
        $eventId = $this->currentEventId();
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $count = BarangMasuk::where('disaster_event_id', $eventId)
                ->whereYear('tanggal_masuk', date('Y'))
                ->whereMonth('tanggal_masuk', $i)
                ->count() + 
                BarangKeluar::where('disaster_event_id', $eventId)
                ->whereYear('tanggal_keluar', date('Y'))
                ->whereMonth('tanggal_keluar', $i)
                ->count();
            $data[] = $count;
        }
        return ['labels' => $months, 'data' => $data];
    }

    #[On('echo:logistik,BarangAdded')]
    public function notifyBarangAdded($event)
    {
        $this->dispatch('show-visual-alert', [
            'title' => 'Logistik Baru!',
            'message' => 'Barang ' . $event['nama_barang'] . ' telah ditambahkan.',
            'type' => 'success'
        ]);
    }
};
?>

<div x-data="{ 
        visualAlert: null, 
        showFlash: false,
        accessibilityMode: localStorage.getItem('accessibilityMode') === 'true'
    }" 
    x-on:show-visual-alert.window="
        visualAlert = $event.detail;
        if(accessibilityMode) {
            showFlash = true;
            setTimeout(() => showFlash = false, 500);
        }
        setTimeout(() => visualAlert = null, 5000);
    "
    class="space-y-8 relative">
    
    <!-- Accessibility Flash Overlay -->
    <div x-show="showFlash" 
        x-transition:enter="transition opacity-0 duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-30"
        x-transition:leave="transition opacity-30 duration-300"
        x-transition:leave-start="opacity-30"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-yellow-400 z-[9999] pointer-events-none" 
        style="display: none;"></div>

    <!-- Visual Notification Toast -->
    <div x-show="visualAlert" 
        x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-20 right-8 z-50 w-full max-w-sm bg-white rounded-2xl shadow-2xl border-l-4 border-green-500 p-4"
        style="display: none;">
        <div class="flex items-start gap-3">
            <div class="bg-green-100 p-2 rounded-lg text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-slate-900" x-text="visualAlert?.title"></h4>
                <p class="text-sm text-slate-600" x-text="visualAlert?.message"></p>
            </div>
            <button @click="visualAlert = null" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-500">Ringkasan operasional logistik MDMC DIY.</p>
            @if($activeEvent)
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Event: {{ $activeEvent->name }}</p>
            @else
                <p class="text-xs font-bold text-amber-600 uppercase tracking-widest mt-2">Belum ada event bencana. Buat event untuk mulai input data.</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aksesibilitas</span>
                <button @click="accessibilityMode = !accessibilityMode; localStorage.setItem('accessibilityMode', accessibilityMode)" 
                    :class="accessibilityMode ? 'bg-blue-600' : 'bg-slate-200'"
                    class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                    <span :class="accessibilityMode ? 'translate-x-5' : 'translate-x-0'"
                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Barang -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600"></div>
            <div>
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">Total Data Logistik</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ $totalBarang }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl text-slate-200 group-hover:text-blue-100 transition-colors">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
        </div>

        <!-- Data Donatur -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
            <div>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider mb-1">Data Donatur</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ $totalDonatur }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl text-slate-200 group-hover:text-emerald-100 transition-colors">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        <!-- Total Stok -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-cyan-500"></div>
            <div>
                <p class="text-[10px] font-bold text-cyan-500 uppercase tracking-wider mb-1">Total Stok Logistik</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ $totalStok }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl text-slate-200 group-hover:text-cyan-100 transition-colors">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- Total User -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
            <div>
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mb-1">Total User</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ $totalUser }}</p>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl text-slate-200 group-hover:text-amber-100 transition-colors">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Line Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                <h3 class="font-bold text-slate-700 text-sm">Total Manajemen Logistik Perbulan pada Tahun {{ date('Y') }}</h3>
            </div>
            <div class="p-6">
                <canvas id="lineChart" height="300"></canvas>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-blue-600">
                <h3 class="font-bold text-white text-sm">Manajemen Logistik</h3>
            </div>
            <div class="p-6">
                <canvas id="donutChart" height="300"></canvas>
                <div class="mt-6 flex justify-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                        <span class="text-xs font-medium text-slate-500">Logistik Masuk</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-xs font-medium text-slate-500">Logistik Keluar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Data Stok Eksternal (Mocking API) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-4 bg-slate-900 text-center">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Stok Eksternal (API Mock)</h3>
            </div>
            <div class="flex-grow">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3 text-center">Stok</th>
                            <th class="px-4 py-3 text-right">Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($externalStok as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $item['nama'] }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $item['stok'] }} {{ $item['satuan'] }}</td>
                                <td class="px-4 py-3 text-right text-slate-400 italic text-[10px]">{{ $item['sumber'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Minimum -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-4 bg-amber-400 text-center">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Stok Logistik Minimum</h3>
            </div>
            <div class="flex-grow">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3 text-center">Stok</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($stokMinimum as $barang)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $barang->nama_barang }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded-full font-bold">{{ $barang->stok }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="/barang-masuk" class="text-amber-500 hover:text-amber-600 font-bold uppercase text-[10px]">Pasok</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Semua stok mencukupi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Masuk -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-4 bg-emerald-500 text-center">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">5 Pengelolaan Terakhir Logistik Masuk</h3>
            </div>
            <div class="flex-grow">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentMasuk as $masuk)
                            <tr>
                                <td class="px-4 py-3 text-slate-500">{{ $masuk->tanggal_masuk }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $masuk->barang->nama_barang }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">{{ $masuk->jumlah_masuk }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Keluar -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-4 bg-red-500 text-center">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">5 Pengelolaan Terakhir Logistik Keluar</h3>
            </div>
            <div class="flex-grow">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentKeluar as $keluar)
                            <tr>
                                <td class="px-4 py-3 text-slate-500">{{ $keluar->tanggal_keluar }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $keluar->barang->nama_barang }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">{{ $keluar->jumlah_keluar }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: 'Total Transaksi',
                            data: @json($chartData['data']),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            const donutCtx = document.getElementById('donutChart');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [@json($donutData['masuk']), @json($donutData['keluar'])],
                            backgroundColor: ['#2563eb', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
</div>
