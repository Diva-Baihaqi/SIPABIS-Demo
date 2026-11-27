<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/ruangan.json');
        if (!File::exists($jsonPath)) {
            return;
        }

        $ruanganList = json_decode(File::get($jsonPath), true);

        foreach ($ruanganList as $item) {
            Ruangan::updateOrCreate(
                ['kode_ruangan' => $item['kode_ruangan']],
                [
                    'nama_ruangan' => $item['nama_ruangan'],
                    'penanggung_jawab' => $item['penanggung_jawab'],
                    'nip_penanggung_jawab' => $item['nip_penanggung_jawab'] ?? null,
                ]
            );
        }
    }
}
