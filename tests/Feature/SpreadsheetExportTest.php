<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\StokOpname;
use App\Models\User;
use App\Services\StockOpnameService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpreadsheetExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected Ruangan $ruangan;
    protected Ruangan $ruanganLain;
    protected BarangPersediaan $barang;
    protected StokOpname $opname;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Rina Kusuma Wardani',
        ]);

        $this->ruanganLain = Ruangan::create([
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

        $this->pegawai = User::create([
            'nip' => 'user',
            'name' => 'Pegawai Umum',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
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
            'harga_satuan_estimasi' => 55000,
        ]);

        $service = app(StockOpnameService::class);
        $this->opname = $service->bukaSesi($this->admin, 'Semester I 2026', Carbon::parse('2026-06-30'));
        $service->catatFisik($this->opname, $this->barang->id, 20, 'Sesuai');
        $service->finalisasi($this->opname, $this->admin, 'Selesai');
    }

    public function test_admin_can_export_buku_kendali_ruangan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.ruangan.export', $this->ruangan->id));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(
            $response->headers->get('content-type'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ));
    }

    public function test_admin_can_export_kartu_stok_barang(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.export', $this->barang->id));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(
            $response->headers->get('content-type'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ));
    }

    public function test_admin_can_export_stock_opname_baso_ringkas(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.export', [
            $this->opname->id,
            'filter' => 'ada_stok',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(
            $response->headers->get('content-type'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ));
        $this->assertTrue(str_contains(
            $response->headers->get('content-disposition'),
            '_Ringkas.xlsx'
        ));
    }

    public function test_admin_can_export_stock_opname_baso_lengkap(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.export', [
            $this->opname->id,
            'filter' => 'semua',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(str_contains(
            $response->headers->get('content-type'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ));
        $this->assertTrue(str_contains(
            $response->headers->get('content-disposition'),
            '_Lengkap.xlsx'
        ));
    }

    public function test_pegawai_cannot_export_other_ruangan_buku_kendali(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('kartu-kendali.ruangan.export', $this->ruanganLain->id));

        $response->assertStatus(403);
    }
}
