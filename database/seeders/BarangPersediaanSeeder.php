<?php

namespace Database\Seeders;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BarangPersediaanSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/barang.json');
        if (!File::exists($jsonPath)) {
            return;
        }

        $barangList = json_decode(File::get($jsonPath), true);
        $kategoriMap = KategoriBarang::pluck('id', 'kode_kategori')->toArray();
        $defaultKategoriId = $kategoriMap['UMUM'] ?? array_values($kategoriMap)[0];

        foreach ($barangList as $item) {
            $kategoriId = $kategoriMap[$item['kode_kategori']] ?? $defaultKategoriId;

            BarangPersediaan::updateOrCreate(
                [
                    'kode_barang' => $item['kode_barang'],
                    'kode_sakti' => $item['kode_sakti'] ?: null,
                ],
                [
                    'kategori_id' => $kategoriId,
                    'nama_barang' => $item['nama_barang'],
                    'satuan' => $item['satuan'],
                    'stok_minimum' => $item['stok_minimum'] ?? 5,
                    'stok_aktif' => $item['stok_aktif'] ?? 0,
                    'harga_satuan_estimasi' => $item['harga_satuan_estimasi'] ?? 0,
                    'lokasi_rak' => $item['lokasi_rak'] ?? 'Lemari Persediaan Subbag Umum',
                ]
            );
        }
    }
}
