@php
    use App\Models\BarangMasuk;
    use App\Models\BarangKeluar;
    use App\Models\Barang;

    $jenis = request('jenis');
    $mulai = request('mulai');
    $selesai = request('selesai');

    if ($jenis === 'masuk') {
        $data = BarangMasuk::with(['barang', 'donatur', 'lokasi'])
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->latest()
            ->get();
        $title = "Laporan Logistik Masuk";
    } elseif ($jenis === 'keluar') {
        $data = BarangKeluar::with(['barang', 'lokasi'])
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->latest()
            ->get();
        $title = "Laporan Logistik Keluar";
    } else {
        $data = Barang::with(['jenis', 'satuan'])->get();
        $title = "Laporan Stok Logistik";
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { margin: 2cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-10">
    <div class="max-w-4xl mx-auto">
        <!-- Header Laporan -->
        <div class="flex items-center justify-between border-b-4 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center">
                    <span class="text-white font-black text-2xl">M</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">MDMC DIY</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Muhammadiyah Disaster Management Center</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
                <p class="text-sm text-slate-500">Periode: {{ $mulai }} s/d {{ $selesai }}</p>
            </div>
        </div>

        <!-- Tabel Data -->
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 border-y border-slate-200">
                    @if($jenis === 'masuk')
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Donatur</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-400 uppercase">Jumlah</th>
                    @elseif($jenis === 'keluar')
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Lokasi/Posko</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-400 uppercase">Jumlah</th>
                    @else
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-400 uppercase">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase">Satuan</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($data as $item)
                <tr>
                    @if($jenis === 'masuk')
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $item->barang->nama_barang }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->donatur->nama_donatur }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900 text-right">{{ $item->jumlah }}</td>
                    @elseif($jenis === 'keluar')
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $item->barang->nama_barang }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->lokasi->nama_lokasi }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900 text-right">{{ $item->jumlah }}</td>
                    @else
                        <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->jenis->nama_jenis }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900 text-right">{{ $item->stok }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->satuan->nama_satuan }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Tanda Tangan -->
        <div class="mt-16 flex justify-end">
            <div class="text-center w-64">
                <p class="text-sm text-slate-600 mb-20">Yogyakarta, {{ date('d F Y') }}</p>
                <p class="font-bold text-slate-900 border-b border-slate-900 pb-1">Petugas Logistik</p>
                <p class="text-xs text-slate-500 uppercase font-bold mt-1">MDMC DIY</p>
            </div>
        </div>

        <div class="no-print mt-10 flex justify-center gap-4">
            <button onclick="window.print()" class="bg-red-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg">Cetak Sekarang</button>
            <button onclick="window.close()" class="bg-slate-100 text-slate-600 px-8 py-3 rounded-xl font-bold">Tutup</button>
        </div>
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>