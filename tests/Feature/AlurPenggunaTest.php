<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AlurPenggunaTest extends TestCase
{
    use RefreshDatabase;

    protected Ruangan $ruangan;
    protected User $adminUser;
    protected User $pegawaiUser;
    protected User $pimpinanUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'TU',
            'nama_ruangan' => 'Subbagian Umum',
            'penanggung_jawab' => 'Rina Kusuma Wardani, S.E., M.M.',
            'nip_penanggung_jawab' => '198003202005012002',
        ]);

        $this->adminUser = User::create([
            'name' => 'Administrator Testing',
            'email' => 'admin.test@bps.go.id',
            'nip' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawaiUser = User::create([
            'name' => 'Pegawai Testing',
            'email' => 'user.test@bps.go.id',
            'nip' => 'user',
            'password' => Hash::make('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pimpinanUser = User::create([
            'name' => 'Kepala BPS Testing',
            'email' => 'pimpinan.test@bps.go.id',
            'nip' => 'pimpinan',
            'password' => Hash::make('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_alur_pengguna(): void
    {
        $response = $this->get(route('alur-pengguna.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pegawai_sees_only_pegawai_workflow(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->get(route('alur-pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Diagram Alur Kerja: Pegawai Organik BPS');
        $response->assertSee('5 Tahap Alur Kerja Pegawai');
        $response->assertSee('Cek Ketersediaan Barang');
        $response->assertSee('Buat Permintaan ATK (PSD)');
        $response->assertDontSee('Diagram Alur Kerja: Administrator Persediaan');
        $response->assertDontSee('Diagram Alur Kerja: Pimpinan');
    }

    public function test_admin_sees_only_admin_workflow(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('alur-pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Diagram Alur Kerja: Administrator Persediaan');
        $response->assertSee('4 Pilar Alur Kerja Administrator');
        $response->assertSee('Verifikasi Kuota Permintaan');
        $response->assertDontSee('Diagram Alur Kerja: Pegawai Organik BPS');
        $response->assertDontSee('Diagram Alur Kerja: Pimpinan');
    }

    public function test_pimpinan_sees_only_pimpinan_workflow(): void
    {
        $response = $this->actingAs($this->pimpinanUser)->get(route('alur-pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Diagram Alur Kerja: Pimpinan (Kepala BPS Kabupaten Cirebon)');
        $response->assertSee('3 Tahap Pengawasan Eksekutif');
        $response->assertSee('Dashboard Eksekutif Terpadu');
        $response->assertDontSee('Diagram Alur Kerja: Pegawai Organik BPS');
        $response->assertDontSee('Diagram Alur Kerja: Administrator Persediaan');
    }
}
