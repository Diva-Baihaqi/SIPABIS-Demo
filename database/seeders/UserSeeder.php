<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/pegawai.json');
        if (!File::exists($jsonPath)) {
            return;
        }

        $pegawaiList = json_decode(File::get($jsonPath), true);
        $ruanganMap = Ruangan::pluck('id', 'kode_ruangan')->toArray();

        // 1. Akun Testing: Admin (admin | admin)
        User::updateOrCreate(
            ['nip' => 'admin'],
            [
                'name' => 'Administrator Testing',
                'email' => 'admin.test@bps.go.id',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'ruangan_id' => $ruanganMap['TU'] ?? null,
                'jabatan' => 'Pengelola Persediaan Subbag Umum',
                'golongan' => 'III/a',
                'status_pegawai' => 'PNS',
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567890',
            ]
        );

        // 2. Akun Testing: User / Pegawai (user | user)
        User::updateOrCreate(
            ['nip' => 'user'],
            [
                'name' => 'Pegawai Testing',
                'email' => 'user.test@bps.go.id',
                'password' => Hash::make('user'),
                'role' => 'pegawai',
                'ruangan_id' => $ruanganMap['SOS'] ?? ($ruanganMap['TU'] ?? null),
                'jabatan' => 'Staf Fungsi Statistik Sosial',
                'golongan' => 'II/c',
                'status_pegawai' => 'PNS',
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567891',
            ]
        );

        // 3. Akun Testing: Pimpinan (pimpinan | pimpinan)
        User::updateOrCreate(
            ['nip' => 'pimpinan'],
            [
                'name' => 'Kepala BPS Testing',
                'email' => 'pimpinan.test@bps.go.id',
                'password' => Hash::make('pimpinan'),
                'role' => 'pimpinan',
                'ruangan_id' => $ruanganMap['TU'] ?? null,
                'jabatan' => 'Kepala BPS Kabupaten Cirebon',
                'golongan' => 'IV/b',
                'status_pegawai' => 'PNS',
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567892',
            ]
        );

        // 4. Impor Pegawai Demo Simulasi (Username: NIP | Password: NIP)
        foreach ($pegawaiList as $item) {
            $ruanganId = $ruanganMap[$item['kode_ruangan']] ?? ($ruanganMap['TU'] ?? null);
            $genderDigit = substr($item['nip'], 14, 1);
            $jenisKelamin = ($genderDigit === '2') ? 'P' : 'L';

            User::updateOrCreate(
                ['nip' => $item['nip']],
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make($item['nip']),
                    'role' => $item['role'],
                    'ruangan_id' => $ruanganId,
                    'jabatan' => $item['jabatan'],
                    'golongan' => $item['golongan'],
                    'status_pegawai' => $item['status_pegawai'],
                    'jenis_kelamin' => $jenisKelamin,
                ]
            );
        }
    }
}
