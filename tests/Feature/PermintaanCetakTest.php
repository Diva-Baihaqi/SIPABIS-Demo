<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\PermintaanApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermintaanCetakTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai1;
    protected User $pegawai2;
    protected Ruangan $ruangan;
    protected BarangPersediaan $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Rina Kusuma Wardani',
        ]);

        $ruanganSosial = Ruangan::create([
            'kode_ruangan' => 'SOSIAL',
            'nama_ruangan' => 'Tim Statistik Sosial',
            'pj_ruangan' => 'PJ Sosial',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Admin Persediaan',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai1 = User::create([
            'nip' => '199001012015011001',
            'name' => 'Pegawai Subbag Umum',
            'email' => 'pegawai1@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai2 = User::create([
            'nip' => '199202022016022002',
            'name' => 'Pegawai Statistik Sosial',
            'email' => 'pegawai2@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $ruanganSosial->id,
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $this->barang = BarangPersediaan::create([
            'kode_barang' => '1.01.03.01.001.000001',
            'kode_sakti' => '1010301001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'rim',
            'stok_minimum' => 5,
            'stok_aktif' => 20,
        ]);
    }

    public function test_service_generates_psd_standard_bps_number(): void
    {
        $service = app(PermintaanApprovalService::class);

        $permintaan = $service->buatPermintaan($this->pegawai1, [
            [
                'barang_id' => $this->barang->id,
                'jumlah_diminta' => 2,
            ],
        ], 'Kebutuhan ATK rutin');

        $year = date('Y');
        $this->assertMatchesRegularExpression("/^PSD-\\d+\\/32091\\/PL\\.711\\/{$year}$/", $permintaan->nomor_permintaan);
    }

    public function test_pegawai_can_view_print_view_of_own_permintaan(): void
    {
        $service = app(PermintaanApprovalService::class);

        $permintaan = $service->buatPermintaan($this->pegawai1, [
            [
                'barang_id' => $this->barang->id,
                'jumlah_diminta' => 3,
            ],
        ], 'Kebutuhan cetak kuesioner');

        $response = $this->actingAs($this->pegawai1)->get(route('permintaan.cetak', $permintaan->id));

        $response->assertStatus(200);
        $response->assertSee('FORMULIR PERMINTAAN BARANG PERSEDIAAN', false);
        $response->assertSee($permintaan->nomor_permintaan, false);
        $response->assertSee('Rina Kusuma Wardani, S.E., M.M.', false);
        $response->assertSee('Bagus Prasetyo, S.E.', false);
        $response->assertSee('BADAN PUSAT STATISTIK KABUPATEN CIREBON', false);
        $response->assertSee('Kertas HVS A4 80gr', false);
    }

    public function test_admin_can_view_print_view_of_any_permintaan(): void
    {
        $service = app(PermintaanApprovalService::class);

        $permintaan = $service->buatPermintaan($this->pegawai1, [
            [
                'barang_id' => $this->barang->id,
                'jumlah_diminta' => 1,
            ],
        ], 'Kebutuhan rapat');

        $response = $this->actingAs($this->admin)->get(route('permintaan.cetak', $permintaan->id));

        $response->assertStatus(200);
        $response->assertSee('FORMULIR PERMINTAAN BARANG PERSEDIAAN', false);
    }

    public function test_pegawai_cannot_view_print_view_of_other_pegawai_permintaan(): void
    {
        $service = app(PermintaanApprovalService::class);

        $permintaan = $service->buatPermintaan($this->pegawai1, [
            [
                'barang_id' => $this->barang->id,
                'jumlah_diminta' => 1,
            ],
        ], 'Kebutuhan tim umum');

        $response = $this->actingAs($this->pegawai2)->get(route('permintaan.cetak', $permintaan->id));

        $response->assertStatus(403);
    }
}
