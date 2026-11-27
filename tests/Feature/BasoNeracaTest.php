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

class BasoNeracaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pimpinan;
    protected StokOpname $opname;

    protected function setUp(): void
    {
        parent::setUp();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Rina Kusuma Wardani',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Admin Persediaan',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->pimpinan = User::create([
            'nip' => 'pimpinan',
            'name' => 'Hendra Wijaya',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pimpinan',
            'ruangan_id' => $ruangan->id,
        ]);

        $kat = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $barang = BarangPersediaan::create([
            'kode_barang' => '1.01.03.01.001.000001',
            'kode_sakti' => '1010301001',
            'nama_barang' => 'Ballpoint Balliner',
            'kategori_id' => $kat->id,
            'satuan' => 'batang',
            'stok_minimum' => 5,
            'stok_aktif' => 12,
            'harga_satuan_estimasi' => 4000,
        ]);

        $service = app(StockOpnameService::class);

        $barangKosong = BarangPersediaan::create([
            'kode_barang' => '1.01.03.01.001.000002',
            'kode_sakti' => '1010301002',
            'nama_barang' => 'Kertas Bufallo Merah Kosong',
            'kategori_id' => $kat->id,
            'satuan' => 'rim',
            'stok_minimum' => 5,
            'stok_aktif' => 0,
            'harga_satuan_estimasi' => 35000,
        ]);

        $this->opname = $service->bukaSesi($this->admin, 'Semester I 2026', Carbon::parse('2026-06-30'));
        $service->catatFisik($this->opname, $barang->id, 12, 'Sesuai');
        $service->catatFisik($this->opname, $barangKosong->id, 0, 'Sesuai');
        $service->finalisasi($this->opname, $this->admin, 'Opname fisik selesai');
    }

    public function test_cetak_baso_format_neraca_sakti(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.cetak', [
            'id' => $this->opname->id,
            'format' => 'neraca'
        ]));

        $response->assertStatus(200);
        $response->assertSee('TABEL 1', false);
        $response->assertSee('BERITA ACARA HASIL OPNAME PHISIK (STOCK OPNAME) PERSEDIAAN', false);
        $response->assertSee('Harga Beli Satuan (Rupiah)', false);
        $response->assertSee('Total Persediaan', false);
        $response->assertSee('Barang Rusak', false);
        $response->assertSee('Barang Usang', false);
        $response->assertSee('Saldo Persediaan di Neraca', false);
        $response->assertSee('Penyimpan Barang', false);
        $response->assertSee('Bagus Prasetyo, S.E.', false);
    }

    public function test_cetak_baso_filter_ada_stok_hanya_menampilkan_barang_bersaldo(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.cetak', [
            'id' => $this->opname->id,
            'format' => 'neraca',
            'filter' => 'ada_stok',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ballpoint Balliner', false);
        $response->assertDontSee('Kertas Bufallo Merah Kosong', false);
        $response->assertDontSee('Format Ringkas', false);
        $response->assertDontSee('Format Lengkap', false);
        $response->assertSee('Persediaan Reguler', false);
    }

    public function test_cetak_baso_filter_semua_menampilkan_seluruh_barang(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.cetak', [
            'id' => $this->opname->id,
            'format' => 'neraca',
            'filter' => 'semua',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ballpoint Balliner', false);
        $response->assertSee('Kertas Bufallo Merah Kosong', false);
        $response->assertDontSee('Format Ringkas', false);
        $response->assertDontSee('Format Lengkap', false);
        $response->assertSee('Persediaan Reguler', false);
    }

    public function test_cetak_baso_format_fisik_internal(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stok-opname.cetak', [
            'id' => $this->opname->id,
            'format' => 'fisik'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Stok Sistem', false);
        $response->assertSee('Opname Fisik', false);
        $response->assertSee('Selisih', false);
        $response->assertSee('Rina Kusuma Wardani, S.E., M.M.', false);
    }
}
