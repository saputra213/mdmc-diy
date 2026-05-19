<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\BisindoMaterial;
use App\Models\BantuanRequest;
use App\Models\DisasterEvent;
use App\Models\DisasterEventPhoto;
use App\Models\Jenis;
use App\Models\Kebutuhan;
use App\Models\Lokasi;
use App\Models\Satuan;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Video;
use App\Models\VideoComment;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'video_comments',
            'videos',
            'bisindo_materials',
            'disaster_event_photos',
            'barang_keluars',
            'barang_masuks',
            'barangs',
            'kebutuhans',
            'bantuan_requests',
            'lokasis',
            'suppliers',
            'jenis',
            'satuan',
            'settings',
            'disaster_events',
            'users',
        ] as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@mdmc.id',
            'password' => Hash::make('sukses12'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $gudang = User::create([
            'name' => 'Petugas Gudang',
            'username' => 'gudang',
            'email' => 'gudang@mdmc.id',
            'password' => Hash::make('sukses12'),
            'role' => 'gudang',
            'is_active' => true,
        ]);

        Setting::set('emergency_mode', 'on', 'string');
        Setting::set('guest.hero_headline', 'Sistem Informasi Logistik Kebencanaan', 'string');
        Setting::set('guest.hero_description', 'Pantau stok logistik, kebutuhan, dan emergency call masyarakat per event bencana.', 'text');
        Setting::set('guest.hero_image_path', 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1920&q=80', 'string');
        Setting::set('guest.gallery_title', 'Galeri Lapangan', 'string');
        Setting::set('guest.gallery_excerpt', 'Dokumentasi per kejadian bencana. Klik folder event untuk melihat foto-foto.', 'text');
        Setting::set('guest.donation_baznas_url', 'https://baznas.go.id/', 'url');
        Setting::set('guest.donation_wa_number', '6281234567890', 'string');
        Setting::set('guest.donation_wa_template', "Halo MDMC DIY, saya ingin donasi barang untuk event {event}.\nSaya tertarik membantu kebutuhan berikut:\n{items}\n\nMohon info lokasi drop-off dan prosedur selanjutnya.", 'text');

        $events = collect([
            DisasterEvent::create(['name' => 'Banjir Jogja 2026', 'location' => 'Kota Yogyakarta', 'affected_houses' => 1125, 'status' => 'active']),
            DisasterEvent::create(['name' => 'Gempa Bantul 2026', 'location' => 'Kab. Bantul', 'affected_houses' => 845, 'status' => 'active']),
            DisasterEvent::create(['name' => 'Longsor Gunungkidul 2026', 'location' => 'Kab. Gunungkidul', 'affected_houses' => 320, 'status' => 'active']),
            DisasterEvent::create(['name' => 'Erupsi Merapi 2025', 'location' => 'Sleman', 'affected_houses' => 2100, 'status' => 'archived']),
            DisasterEvent::create(['name' => 'Angin Kencang Kulon Progo 2024', 'location' => 'Kulon Progo', 'affected_houses' => 540, 'status' => 'archived']),
        ]);

        $makanan = Jenis::create(['nama_jenis' => 'Makanan & Minuman']);
        $medis = Jenis::create(['nama_jenis' => 'Peralatan Medis']);
        $tenda = Jenis::create(['nama_jenis' => 'Tenda & Hunian']);
        $pakaian = Jenis::create(['nama_jenis' => 'Pakaian & Selimut']);
        $logistik = Jenis::create(['nama_jenis' => 'Logistik Umum']);

        $box = Satuan::create(['nama_satuan' => 'Box']);
        $pcs = Satuan::create(['nama_satuan' => 'Pcs']);
        $unit = Satuan::create(['nama_satuan' => 'Unit']);
        $paket = Satuan::create(['nama_satuan' => 'Paket']);

        $supplierPusat = Supplier::create([
            'nama_supplier' => 'Gudang Pusat MDMC',
            'no_telp' => '08123456789',
            'alamat' => 'Yogyakarta',
        ]);

        $supplierRelawan = Supplier::create([
            'nama_supplier' => 'Relawan & Donatur',
            'no_telp' => '08129876543',
            'alamat' => 'DIY',
        ]);

        $lokasiA = Lokasi::create(['nama_lokasi' => 'Posko Bantul', 'alamat' => 'Bantul, DIY']);
        $lokasiB = Lokasi::create(['nama_lokasi' => 'Posko Sleman', 'alamat' => 'Sleman, DIY']);
        $lokasiC = Lokasi::create(['nama_lokasi' => 'Posko Gunungkidul', 'alamat' => 'Gunungkidul, DIY']);

        if (DB::getSchemaBuilder()->hasColumn('lokasis', 'latitude')) {
            DB::table('lokasis')->where('id', $lokasiA->id)->update(['latitude' => '-7.8856', 'longitude' => '110.3304']);
            DB::table('lokasis')->where('id', $lokasiB->id)->update(['latitude' => '-7.7162', 'longitude' => '110.3556']);
            DB::table('lokasis')->where('id', $lokasiC->id)->update(['latitude' => '-7.9675', 'longitude' => '110.6044']);
        }

        $barangCatalog = [
            ['nama' => 'Mie Instan', 'jenis' => $makanan, 'satuan' => $box],
            ['nama' => 'Air Mineral', 'jenis' => $makanan, 'satuan' => $box],
            ['nama' => 'Selimut', 'jenis' => $pakaian, 'satuan' => $pcs],
            ['nama' => 'Tenda Regu', 'jenis' => $tenda, 'satuan' => $unit],
            ['nama' => 'Matras', 'jenis' => $tenda, 'satuan' => $pcs],
            ['nama' => 'Masker', 'jenis' => $medis, 'satuan' => $box],
            ['nama' => 'P3K', 'jenis' => $medis, 'satuan' => $paket],
            ['nama' => 'Paket Sembako', 'jenis' => $logistik, 'satuan' => $paket],
        ];

        $barangIdCounter = 1;
        $bmCounter = 1;
        $bkCounter = 1;

        foreach ($events as $event) {
            $itemsForEvent = collect($barangCatalog)->shuffle()->take(6);

            foreach ($itemsForEvent as $item) {
                $barangId = 'B' . str_pad((string) $barangIdCounter, 6, '0', STR_PAD_LEFT);
                $barangIdCounter++;

                $barang = Barang::create([
                    'id' => $barangId,
                    'disaster_event_id' => $event->id,
                    'nama_barang' => $item['nama'],
                    'stok' => 0,
                    'jenis_id' => $item['jenis']->id,
                    'satuan_id' => $item['satuan']->id,
                ]);

                $qtyIn1 = random_int(10, 80);
                $qtyIn2 = random_int(5, 40);

                BarangMasuk::create([
                    'id' => 'BM' . str_pad((string) $bmCounter, 6, '0', STR_PAD_LEFT),
                    'disaster_event_id' => $event->id,
                    'supplier_id' => $supplierPusat->id,
                    'barang_id' => $barang->id,
                    'user_id' => $gudang->id,
                    'jumlah_masuk' => $qtyIn1,
                    'tanggal_masuk' => now()->subDays(random_int(1, 25))->toDateString(),
                ]);
                $bmCounter++;
                $barang->increment('stok', $qtyIn1);

                BarangMasuk::create([
                    'id' => 'BM' . str_pad((string) $bmCounter, 6, '0', STR_PAD_LEFT),
                    'disaster_event_id' => $event->id,
                    'supplier_id' => $supplierRelawan->id,
                    'barang_id' => $barang->id,
                    'user_id' => $gudang->id,
                    'jumlah_masuk' => $qtyIn2,
                    'tanggal_masuk' => now()->subDays(random_int(1, 20))->toDateString(),
                ]);
                $bmCounter++;
                $barang->increment('stok', $qtyIn2);

                $qtyOut = random_int(0, max(0, (int) floor(($qtyIn1 + $qtyIn2) / 2)));
                if ($qtyOut > 0) {
                    $lokasi = collect([$lokasiA, $lokasiB, $lokasiC])->random();

                    BarangKeluar::create([
                        'id' => 'BK' . str_pad((string) $bkCounter, 6, '0', STR_PAD_LEFT),
                        'disaster_event_id' => $event->id,
                        'user_id' => $admin->id,
                        'barang_id' => $barang->id,
                        'jumlah_keluar' => $qtyOut,
                        'tanggal_keluar' => now()->subDays(random_int(0, 10))->toDateString(),
                        'lokasi_id' => $lokasi->id,
                    ]);
                    $bkCounter++;
                    $barang->decrement('stok', $qtyOut);
                }
            }

            $lokasiLabel = $event->location ?: 'DIY';
            Kebutuhan::create([
                'disaster_event_id' => $event->id,
                'nama_lokasi' => 'Posko ' . $lokasiLabel,
                'jumlah_korban' => random_int(20, 250),
                'nama_barang' => 'Air Mineral',
                'butuh_barang' => random_int(50, 400),
                'status' => false,
            ]);

            Kebutuhan::create([
                'disaster_event_id' => $event->id,
                'nama_lokasi' => 'Posko ' . $lokasiLabel,
                'jumlah_korban' => random_int(20, 250),
                'nama_barang' => 'Selimut',
                'butuh_barang' => random_int(30, 200),
                'status' => (bool) random_int(0, 1),
            ]);

            $jenisOptions = ['Makanan', 'Air Bersih', 'Selimut', 'Obat-obatan', 'Tenda', 'Pakaian'];
            foreach (range(1, 6) as $i) {
                $req = BantuanRequest::create([
                    'disaster_event_id' => $event->id,
                    'nama_pelapor' => fake()->name(),
                    'no_hp' => '08' . random_int(111111111, 999999999),
                    'lokasi_detail' => fake()->address(),
                    'jenis_bantuan' => collect($jenisOptions)->shuffle()->take(random_int(1, 3))->values()->all(),
                    'status' => collect(['pending', 'approved', 'rejected'])->random(),
                    'latitude' => (string) fake()->latitude(-8.2, -7.5),
                    'longitude' => (string) fake()->longitude(110.0, 110.9),
                ]);
            }

            foreach (range(1, 4) as $i) {
                DisasterEventPhoto::create([
                    'disaster_event_id' => $event->id,
                    'image_path' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80&sig=' . ($event->id * 10 + $i),
                    'description' => $event->name . ' — Dokumentasi lapangan #' . $i,
                ]);
            }
        }

        $video1 = Video::create([
            'judul' => 'Panduan Emergency Call',
            'deskripsi' => 'Cara melaporkan kondisi darurat dan menentukan lokasi dengan peta.',
            'video_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'bisindo_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'views_count' => 1250,
            'is_active' => true,
        ]);

        $video2 = Video::create([
            'judul' => 'Distribusi Logistik di Posko',
            'deskripsi' => 'Ringkasan alur penerimaan dan penyaluran logistik per event.',
            'video_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'bisindo_embed_url' => null,
            'views_count' => 840,
            'is_active' => true,
        ]);

        foreach (range(1, 4) as $i) {
            VideoComment::create([
                'video_id' => $video1->id,
                'nama' => fake()->firstName(),
                'komentar' => fake()->sentence(),
                'is_approved' => true,
            ]);
        }

        foreach (range(1, 3) as $i) {
            VideoComment::create([
                'video_id' => $video2->id,
                'nama' => fake()->firstName(),
                'komentar' => fake()->sentence(),
                'is_approved' => true,
            ]);
        }

        foreach ([
            ['judul' => 'Isyarat Darurat', 'kategori' => 'Kebencanaan', 'tingkat' => 'Dasar'],
            ['judul' => 'Isyarat Lokasi', 'kategori' => 'Kebencanaan', 'tingkat' => 'Dasar'],
            ['judul' => 'Isyarat Medis', 'kategori' => 'Kesehatan', 'tingkat' => 'Menengah'],
            ['judul' => 'Isyarat Distribusi', 'kategori' => 'Logistik', 'tingkat' => 'Menengah'],
            ['judul' => 'Isyarat Evakuasi', 'kategori' => 'Kebencanaan', 'tingkat' => 'Lanjut'],
        ] as $m) {
            BisindoMaterial::create([
                'judul' => $m['judul'],
                'kategori' => $m['kategori'],
                'tingkat' => $m['tingkat'],
                'gambar_url' => 'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=1200&q=80&sig=' . crc32($m['judul']),
                'deskripsi' => 'Materi BISINDO untuk ' . strtolower($m['judul']) . '.',
                'is_active' => true,
            ]);
        }
    }
}
