<?php

namespace Database\Seeders;

use App\Models\BisindoMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BisindoMaterialSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Catatan:
         * - Gambar saat ini memakai placeholder (logo) dan WAJIB diganti dengan dokumentasi gerakan BISINDO yang sudah divalidasi
         *   oleh guru SLB / JBI / komunitas Tuli (mis. GERKATIN). Jangan memakai ilustrasi gerakan asumtif.
         * - Pengelompokan kategori/tingkat mengacu pada rujukan akademik:
         *   Fauziyah (2022); Christianingsih et al. (2025); Fidiyaningrum et al. (2024); Khotijah et al. (2024);
         *   Saraswati (2022); Fajri et al. (2020); Wismany & Ganesya (2024).
         */

        $placeholder = '/images/logo.webp';

        $alphabetRows = [];
        foreach (range('A', 'Z') as $ch) {
            $alphabetRows[] = [
                'judul' => $ch,
                'kategori' => 'Abjad',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan abjad BISINDO: huruf ' . $ch . '.',
                'sumber_jurnal' => 'Saraswati 2022; Khotijah et al. 2024; Fajri et al. 2020; Fidiyaningrum et al. 2024.',
            ];
        }

        $topicRows = [
            [
                'judul' => 'Salam dan Perkenalan Dasar',
                'kategori' => 'Dasar',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan komunikasi awal menggunakan BISINDO, seperti salam, memperkenalkan diri, dan membuka percakapan sederhana. Materi ini digunakan agar relawan dan masyarakat dapat memulai interaksi secara inklusif dengan penyandang tunarungu.',
                'sumber_jurnal' => 'Saraswati 2022; Khotijah et al. 2024; Fajri et al. 2020.',
            ],
            [
                'judul' => 'Angka untuk Pendataan Bantuan',
                'kategori' => 'Dasar',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan angka dasar dalam BISINDO untuk mendukung pendataan jumlah korban, jumlah bantuan, jumlah paket logistik, dan kebutuhan lain saat bencana.',
                'sumber_jurnal' => 'Fidiyaningrum et al. 2024; Khotijah et al. 2024.',
            ],
            [
                'judul' => 'Arah dan Lokasi',
                'kategori' => 'Komunikasi',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi komunikasi visual mengenai arah dan lokasi, seperti kanan, kiri, depan, belakang, dekat, jauh, dan lokasi aman. Materi ini berguna saat penyandang tunarungu membutuhkan arahan menuju titik kumpul atau posko.',
                'sumber_jurnal' => 'Christianingsih et al. 2025; Fauziyah 2022.',
            ],
            [
                'judul' => 'Bantuan',
                'kategori' => 'Bantuan',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan isyarat yang berkaitan dengan permintaan bantuan. Materi ini digunakan untuk membantu pengguna menyampaikan kebutuhan secara sederhana ketika berada dalam situasi darurat.',
                'sumber_jurnal' => 'Fauziyah 2022; Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Makanan dan Minuman',
                'kategori' => 'Logistik',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi BISINDO untuk kebutuhan pangan, seperti makanan, air minum, susu, nasi, roti, dan kebutuhan konsumsi dasar. Materi ini relevan dengan distribusi logistik bencana.',
                'sumber_jurnal' => 'Christianingsih et al. 2025; Fidiyaningrum et al. 2024.',
            ],
            [
                'judul' => 'Obat dan Pertolongan Pertama',
                'kategori' => 'Kesehatan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah pertolongan pertama, obat, luka, sakit, pusing, dan kebutuhan medis dasar. Materi ini membantu komunikasi awal antara relawan dan penyandang tunarungu saat membutuhkan bantuan kesehatan.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Evakuasi',
                'kategori' => 'Evakuasi',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan istilah evakuasi, keluar, ikuti, aman, bahaya, dan titik kumpul. Materi ini penting karena penyandang tunarungu sering mengalami hambatan dalam menerima instruksi darurat berbasis suara.',
                'sumber_jurnal' => 'Fauziyah 2022; Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Bahaya',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan tanda bahaya melalui komunikasi visual, seperti bahaya, hati-hati, jangan masuk, dan segera keluar. Materi ini digunakan untuk mendukung penyampaian peringatan darurat.',
                'sumber_jurnal' => 'Fauziyah 2022; Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Banjir',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan istilah bencana banjir, air naik, hujan deras, dan tempat aman. Materi ini relevan dengan informasi bencana hidrometeorologi.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Gempa Bumi',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan istilah gempa bumi, berlindung, menjauh dari bangunan, dan menuju titik aman. Materi ini mendukung edukasi mitigasi bencana bagi penyandang tunarungu.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Tanah Longsor',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah tanah longsor, lereng, hujan deras, jalan tertutup, dan lokasi berbahaya. Materi ini digunakan untuk memperluas kosakata kebencanaan.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Gunung Meletus',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah erupsi, abu vulkanik, masker, radius aman, dan evakuasi. Materi ini relevan dengan konteks wilayah Indonesia yang rawan bencana vulkanik.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Posko Bantuan',
                'kategori' => 'Posko',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan istilah posko, relawan, tempat bantuan, daftar bantuan, dan lokasi pelayanan. Materi ini mendukung komunikasi di lingkungan posko bencana.',
                'sumber_jurnal' => 'Fauziyah 2022; Wismany dan Ganesya 2024.',
            ],
            [
                'judul' => 'Donasi',
                'kategori' => 'Bantuan',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan istilah donasi, memberi bantuan, menerima bantuan, dan kebutuhan barang. Materi ini berkaitan dengan fitur donasi pada website.',
                'sumber_jurnal' => 'Wismany dan Ganesya 2024; Fajri et al. 2020.',
            ],
            [
                'judul' => 'Paket Logistik',
                'kategori' => 'Logistik',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah paket bantuan, barang masuk, barang keluar, stok, dan distribusi. Materi ini disesuaikan dengan fitur logistik pada sistem MDMC DIY.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Pakaian dan Selimut',
                'kategori' => 'Logistik',
                'tingkat' => 'Pemula',
                'deskripsi' => 'Materi pengenalan kebutuhan sandang seperti pakaian, jaket, selimut, sarung, dan alas tidur. Materi ini mendukung komunikasi kebutuhan dasar pengungsi.',
                'sumber_jurnal' => 'Christianingsih et al. 2025; Fidiyaningrum et al. 2024.',
            ],
            [
                'judul' => 'Anak dan Lansia',
                'kategori' => 'Bantuan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan kelompok rentan seperti anak, bayi, lansia, ibu hamil, dan penyandang disabilitas. Materi ini membantu proses identifikasi prioritas bantuan.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Rumah Sakit dan Ambulans',
                'kategori' => 'Kesehatan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah rumah sakit, ambulans, dokter, perawat, dan rujukan. Materi ini mendukung komunikasi dasar saat ada korban yang membutuhkan layanan kesehatan.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Keluarga Hilang',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah keluarga, hilang, mencari, ditemukan, dan laporan. Materi ini membantu penyampaian informasi dalam kondisi kedaruratan.',
                'sumber_jurnal' => 'Fauziyah 2022.',
            ],
            [
                'judul' => 'Lapor Kondisi Darurat',
                'kategori' => 'Bantuan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan cara menyampaikan kondisi darurat secara visual, seperti butuh bantuan, lokasi saya, jumlah korban, dan jenis bantuan. Materi ini berkaitan langsung dengan fitur request bantuan pada website.',
                'sumber_jurnal' => 'Fauziyah 2022; Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Status Bantuan',
                'kategori' => 'Bantuan',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi pengenalan istilah menunggu, disetujui, ditolak, diproses, dan selesai. Materi ini mendukung pemahaman pengguna terhadap status permintaan bantuan.',
                'sumber_jurnal' => 'Fajri et al. 2020; Wismany dan Ganesya 2024.',
            ],
            [
                'judul' => 'Komunikasi dengan Relawan',
                'kategori' => 'Komunikasi',
                'tingkat' => 'Menengah',
                'deskripsi' => 'Materi komunikasi dasar antara penyandang tunarungu dan relawan, seperti nama saya, saya butuh, tolong tulis, tunjukkan lokasi, dan ulangi.',
                'sumber_jurnal' => 'Wismany dan Ganesya 2024; Khotijah et al. 2024.',
            ],
            [
                'judul' => 'Informasi Visual Bencana',
                'kategori' => 'Kebencanaan',
                'tingkat' => 'Lanjutan',
                'deskripsi' => 'Materi pengenalan informasi bencana berbasis visual, seperti simbol peringatan, warna status, peta lokasi, dan ikon bantuan. Materi ini sesuai dengan kebutuhan pengguna tunarungu yang mengandalkan informasi visual.',
                'sumber_jurnal' => 'Fauziyah 2022; Christianingsih et al. 2025.',
            ],
            [
                'judul' => 'Simulasi Evakuasi Sederhana',
                'kategori' => 'Evakuasi',
                'tingkat' => 'Lanjutan',
                'deskripsi' => 'Materi pengenalan langkah simulasi evakuasi sederhana, mulai dari membaca peringatan visual, mengikuti arah evakuasi, menuju titik kumpul, dan melapor ke posko.',
                'sumber_jurnal' => 'Christianingsih et al. 2025.',
            ],
        ];

        $rows = array_merge($alphabetRows, $topicRows);

        $hasSource = Schema::hasColumn('bisindo_materials', 'sumber_jurnal');

        foreach ($rows as $row) {
            $payload = [
                'kategori' => $row['kategori'],
                'tingkat' => $row['tingkat'],
                'gambar_url' => $placeholder,
                'deskripsi' => $row['deskripsi'],
                'is_active' => true,
            ];

            if ($hasSource) {
                $payload['sumber_jurnal'] = $row['sumber_jurnal'];
            }

            BisindoMaterial::updateOrCreate(
                ['judul' => $row['judul']],
                $payload
            );
        }
    }
}
