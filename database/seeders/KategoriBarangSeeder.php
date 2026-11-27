<?php

namespace Database\Seeders;

use App\Models\KategoriBarang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/kategori.json');
        if (!File::exists($jsonPath)) {
            return;
        }

        $kategoriList = json_decode(File::get($jsonPath), true);

        foreach ($kategoriList as $item) {
            KategoriBarang::updateOrCreate(
                ['kode_kategori' => $item['kode_kategori']],
                [
                    'nama_kategori' => $item['nama_kategori'],
                    'deskripsi' => $item['deskripsi'],
                ]
            );
        }
    }
}
