<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pimpinan;
    protected User $pegawai;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;

    protected function setUp(): void
    {
        parent::setUp();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'penanggung_jawab' => 'Kasubbag Umum',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Subbag Umum',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Pengadministrasi BMN',
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS Kabupaten Cirebon',
            'nip' => 'pimpinan',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Kepala BPS',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai BPS',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Kategori ATK',
        ]);

        $this->barang1 = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);

        $this->barang2 = BarangPersediaan::create([
            'kode_barang' => 'ATK-002',
            'nama_barang' => 'Spidol Whiteboard Snowman',
            'kategori_id' => $kategori->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 20,
            'stok_minimum' => 5,
        ]);
    }

    public function test_admin_can_access_stock_opname_index_and_create(): void
    {
        $responseIndex = $this->actingAs($this->admin)->get('/stok-opname');
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->admin)->get('/stok-opname/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Inisiasi Sesi Stock Opname', false);
        $responseCreate->assertSee('Alur Tahapan Audit Stock Opname', false);
        $responseCreate->assertSee('Pilihan Cepat Siklus Audit BPS', false);
        $responseCreate->assertSee('Katalog Barang Aktif', false);
    }

    public function test_non_admin_cannot_access_stock_opname_create(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/stok-opname/create');
        $response->assertStatus(403);
    }

    public function test_admin_can_open_new_opname_session_and_freeze_snapshot(): void
    {
        $payload = [
            'periode' => 'Semester II 2026',
            'tanggal_opname' => '2026-11-21',
        ];

        $response = $this->actingAs($this->admin)->post('/stok-opname', $payload);

        $opname = StokOpname::where('periode', 'Semester II 2026')->first();
        $this->assertNotNull($opname);
        $this->assertEquals('draft', $opname->status);

        $response->assertRedirect("/stok-opname/{$opname->id}");

        // Snapshot details must exist for both items
        $this->assertDatabaseHas('stok_opname_detail', [
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'stok_sistem' => 50,
            'stok_fisik' => 50,
            'selisih' => 0,
        ]);

        $this->assertDatabaseHas('stok_opname_detail', [
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang2->id,
            'stok_sistem' => 20,
            'stok_fisik' => 20,
            'selisih' => 0,
        ]);
    }

    public function test_admin_can_update_physical_counting_and_finalize_session(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0001',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'draft',
            'user_id' => $this->admin->id,
        ]);

        StokOpnameDetail::create([
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'stok_sistem' => 50,
            'stok_fisik' => 50,
            'selisih' => 0,
        ]);

        StokOpnameDetail::create([
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang2->id,
            'stok_sistem' => 20,
            'stok_fisik' => 20,
            'selisih' => 0,
        ]);

        // 1. Update physical count on barang1 (found 45, -5 difference)
        $updatePayload = [
            'barang_id' => $this->barang1->id,
            'stok_fisik' => 45,
            'keterangan_selisih' => '5 rim rusak terkena tetesan air',
        ];

        $respUpdate = $this->actingAs($this->admin)->post("/stok-opname/{$opname->id}/update-fisik", $updatePayload);
        $respUpdate->assertRedirect();

        $detail1 = StokOpnameDetail::where('stok_opname_id', $opname->id)->where('barang_id', $this->barang1->id)->first();
        $this->assertEquals(45, $detail1->stok_fisik);
        $this->assertEquals(-5, $detail1->selisih);

        // Stok aktif riil di barang_persediaan BELUM berubah saat fase draft
        $this->assertEquals(50, $this->barang1->fresh()->stok_aktif);

        // 2. Finalisasi opname
        $respFinal = $this->actingAs($this->admin)->post("/stok-opname/{$opname->id}/finalisasi", [
            'catatan_kesimpulan' => 'Opname fisik selesai. Ditemukan 5 rim kertas rusak.',
        ]);

        $respFinal->assertRedirect("/stok-opname/{$opname->id}");
        $this->assertEquals('final', $opname->fresh()->status);

        // Stok aktif riil SEKARANG resmi terkoreksi menjadi 45
        $this->assertEquals(45, $this->barang1->fresh()->stok_aktif);

        // Mutasi koreksi jurnal tercatat di transaksi_persediaan
        $this->assertDatabaseHas('transaksi_persediaan', [
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'jenis_transaksi' => 'penyesuaian',
            'jumlah' => -5,
            'saldo_setelah' => 45,
        ]);

        // Barang 2 yang tidak selisih TIDAK dibuatkan transaksi penyesuaian
        $this->assertDatabaseMissing('transaksi_persediaan', [
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang2->id,
        ]);
    }

    public function test_pimpinan_can_endorse_finalized_opname_session(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0002',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'final',
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pimpinan)->post("/stok-opname/{$opname->id}/sahkan");
        $response->assertRedirect("/stok-opname/{$opname->id}");
        $response->assertSessionHas('success');

        $this->assertEquals('disahkan', $opname->fresh()->status);
        $this->assertEquals($this->pimpinan->id, $opname->fresh()->pimpinan_id);
    }

    public function test_pegawai_cannot_endorse_opname_session(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0003',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'final',
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pegawai)->post("/stok-opname/{$opname->id}/sahkan");
        $response->assertStatus(403);
    }

    public function test_cetak_baso_can_be_rendered(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0004',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'disahkan',
            'user_id' => $this->admin->id,
            'pimpinan_id' => $this->pimpinan->id,
        ]);

        StokOpnameDetail::create([
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'stok_sistem' => 50,
            'stok_fisik' => 50,
            'selisih' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get("/stok-opname/{$opname->id}/cetak");
        $response->assertStatus(200);
        $response->assertSee('BERITA ACARA HASIL OPNAME PHISIK (STOCK OPNAME) PERSEDIAAN');
        $response->assertSee('BA-SO/BPS3209/2026/11/0004');
    }

    public function test_admin_can_delete_stock_opname_session(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0005',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'draft',
            'user_id' => $this->admin->id,
        ]);

        StokOpnameDetail::create([
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'stok_sistem' => 50,
            'stok_fisik' => 50,
            'selisih' => 0,
        ]);

        $response = $this->actingAs($this->admin)->delete("/stok-opname/{$opname->id}");

        $response->assertRedirect('/stok-opname');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('stok_opname', ['id' => $opname->id]);
        $this->assertDatabaseMissing('stok_opname_detail', ['stok_opname_id' => $opname->id]);
    }

    public function test_non_admin_cannot_delete_stock_opname_session(): void
    {
        $opname = StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS3209/2026/11/0006',
            'tanggal_opname' => '2026-11-21',
            'periode' => 'November 2026',
            'status' => 'draft',
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pegawai)->delete("/stok-opname/{$opname->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('stok_opname', ['id' => $opname->id]);
    }
}
