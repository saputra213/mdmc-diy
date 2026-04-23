<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@mdmc.id',
            'password' => bcrypt('sukses12'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Jenis
        $makanan = \App\Models\Jenis::create(['nama_jenis' => 'Makanan & Minuman']);
        $medis = \App\Models\Jenis::create(['nama_jenis' => 'Peralatan Medis']);
        $tenda = \App\Models\Jenis::create(['nama_jenis' => 'Tenda & Hunian']);
        $pakaian = \App\Models\Jenis::create(['nama_jenis' => 'Pakaian & Selimut']);

        // Satuan
        $box = \App\Models\Satuan::create(['nama_satuan' => 'Box']);
        $pcs = \App\Models\Satuan::create(['nama_satuan' => 'Pcs']);
        $unit = \App\Models\Satuan::create(['nama_satuan' => 'Unit']);
        $paket = \App\Models\Satuan::create(['nama_satuan' => 'Paket']);

        // Suppliers
        \App\Models\Supplier::create([
            'nama_supplier' => 'Gudang Pusat MDMC',
            'no_telp' => '08123456789',
            'alamat' => 'Yogyakarta',
        ]);

        // Lokasi
        \App\Models\Lokasi::create([
            'nama_lokasi' => 'Posko Bantul',
            'alamat' => 'Bantul, DIY',
        ]);

        // Barangs
        \App\Models\Barang::create([
            'id' => 'B000001',
            'nama_barang' => 'Tenda Regu',
            'stok' => 10,
            'jenis_id' => $tenda->id,
            'satuan_id' => $unit->id,
        ]);

        \App\Models\Barang::create([
            'id' => 'B000002',
            'nama_barang' => 'Mie Instan',
            'stok' => 50,
            'jenis_id' => $makanan->id,
            'satuan_id' => $box->id,
        ]);
    }
}
