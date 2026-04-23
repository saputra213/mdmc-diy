<?php

namespace App\Services;

class LogistikService
{
    /**
     * Get logistics stock data.
     * Currently returns dummy data for API mocking.
     * Replace the return value with actual API call later.
     */
    public function getStokLogistik()
    {
        // Mocking external API data
        return [
            [
                'id' => 'API-001',
                'nama' => 'Beras Premium',
                'stok' => 500,
                'satuan' => 'Kg',
                'sumber' => 'Gudang Pusat'
            ],
            [
                'id' => 'API-002',
                'nama' => 'Air Mineral 600ml',
                'stok' => 1200,
                'satuan' => 'Botol',
                'sumber' => 'Donatur A'
            ],
            [
                'id' => 'API-003',
                'nama' => 'Masker Medis',
                'stok' => 2500,
                'satuan' => 'Pcs',
                'sumber' => 'Dinas Kesehatan'
            ],
            [
                'id' => 'API-004',
                'nama' => 'Selimut Wol',
                'stok' => 150,
                'satuan' => 'Pcs',
                'sumber' => 'Gudang Bantul'
            ]
        ];
    }
}
