<?php

namespace Tests\Unit;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\StockMovementService;
use App\Services\StockOpnameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockOpnameService $service;
    protected StockMovementService $stockService;
    protected User $admin;
    protected User $pimpinan;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = new StockMovementService();
        $this->service = new StockOpnameService($this->stockService);

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R01',
            'nama_ruangan' => 'Subbagian Umum',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Inventaris',
            'email' => 'admin@bps.go.id',
            'nip' => '198501012008011001',
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
            'password' => bcrypt('bps12345'),
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS',
            'email' => 'kepala@bps.go.id',
            'nip' => '197001011995011001',
            'role' => 'pimpinan',
            'ruangan_id' => $ruangan->id,
            'password' => bcrypt('bps12345'),
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-TINTA',
            'nama_kategori' => 'Tinta dan Toner Printer',
            'deskripsi' => 'Tinta printer persediaan',
        ]);

        $this->barang1 = BarangPersediaan::create([
            'kode_barang' => 'TNT-001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Tinta Epson 003 Black',
            'satuan' => 'Botol',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
            'harga_satuan_estimasi' => 90000,
        ]);

        $this->barang2 = BarangPersediaan::create([
            'kode_barang' => 'TNT-002',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Tinta Epson 003 Cyan',
            'satuan' => 'Botol',
            'stok_minimum' => 5,
            'stok_aktif' => 8,
            'harga_satuan_estimasi' => 90000,
        ]);
    }

    public function test_buka_sesi_membuat_snapshot_stok_seluruh_barang(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');

        $this->assertNotNull($opname);
        $this->assertEquals('draft', $opname->status);
        $this->assertStringStartsWith('BA-SO/BPS3209/', $opname->nomor_opname);
        $this->assertCount(2, $opname->details);

        $detail1 = $opname->details->firstWhere('barang_id', $this->barang1->id);
        $this->assertEquals(10, $detail1->stok_sistem);
        $this->assertEquals(10, $detail1->stok_fisik);
        $this->assertEquals(0, $detail1->selisih);
    }

    public function test_catat_fisik_menghitung_selisih_dengan_benar(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');

        // Barang 1 sistem 10, fisik ternyata 8 (hilang / rusak 2 botol)
        $detail = $this->service->catatFisik($opname, $this->barang1->id, 8, 'Rusak tertindih kardus');

        $this->assertEquals(8, $detail->stok_fisik);
        $this->assertEquals(-2, $detail->selisih);
        $this->assertEquals('Rusak tertindih kardus', $detail->keterangan_selisih);

        // Pastikan stok aktif riil BELUM berubah saat masih draft
        $this->barang1->refresh();
        $this->assertEquals(10, $this->barang1->stok_aktif);
    }

    public function test_finalisasi_melakukan_koreksi_stok_otomatis_pada_barang_yang_selisih(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');

        // Barang 1 selisih -2 (fisik 8 vs sistem 10)
        $this->service->catatFisik($opname, $this->barang1->id, 8, '2 botol bocor');

        // Barang 2 tetap sama (fisik 8 vs sistem 8)

        $final = $this->service->finalisasi($opname, $this->admin, 'Opname selesai dengan catatan 1 item selisih');

        $this->assertEquals('final', $final->status);
        $this->assertEquals('Opname selesai dengan catatan 1 item selisih', $final->catatan_kesimpulan);

        // Barang 1 terkoreksi menjadi 8
        $this->barang1->refresh();
        $this->assertEquals(8, $this->barang1->stok_aktif);

        // Barang 2 tetap 8 tanpa koreksi
        $this->barang2->refresh();
        $this->assertEquals(8, $this->barang2->stok_aktif);

        // Transaksi persediaan penyesuaian tercatat untuk barang 1
        $this->assertDatabaseHas('transaksi_persediaan', [
            'stok_opname_id' => $opname->id,
            'barang_id' => $this->barang1->id,
            'jenis_transaksi' => 'penyesuaian',
            'jumlah' => -2,
        ]);
    }

    public function test_sahkan_pimpinan_mengubah_status_menjadi_disahkan(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');
        $this->service->finalisasi($opname, $this->admin);

        $disahkan = $this->service->sahkanPimpinan($opname, $this->pimpinan);

        $this->assertEquals('disahkan', $disahkan->status);
        $this->assertEquals($this->pimpinan->id, $disahkan->pimpinan_id);
    }

    public function test_hapus_sesi_draft_berhasil_menghapus_data(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');
        $opnameId = $opname->id;

        $this->assertDatabaseHas('stok_opname', ['id' => $opnameId]);
        $this->assertDatabaseHas('stok_opname_detail', ['stok_opname_id' => $opnameId]);

        $this->service->hapusSesi($opname, $this->admin);

        $this->assertDatabaseMissing('stok_opname', ['id' => $opnameId]);
        $this->assertDatabaseMissing('stok_opname_detail', ['stok_opname_id' => $opnameId]);

        // Stok barang tidak berubah
        $this->barang1->refresh();
        $this->assertEquals(10, $this->barang1->stok_aktif);
    }

    public function test_hapus_sesi_final_membalikkan_penyesuaian_stok_otomatis(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');
        $opnameId = $opname->id;

        // Ubah stok fisik barang 1 dari 10 menjadi 8 (selisih -2)
        $this->service->catatFisik($opname, $this->barang1->id, 8, '2 botol hilang');

        // Finalisasi opname (stok aktif barang 1 jadi 8, transaksi tercatat)
        $this->service->finalisasi($opname, $this->admin);
        $this->barang1->refresh();
        $this->assertEquals(8, $this->barang1->stok_aktif);
        $this->assertDatabaseHas('transaksi_persediaan', [
            'stok_opname_id' => $opnameId,
            'barang_id' => $this->barang1->id,
            'jumlah' => -2,
        ]);

        // Hapus sesi opname
        $this->service->hapusSesi($opname, $this->admin);

        // Verifikasi sesi dan detail terhapus
        $this->assertDatabaseMissing('stok_opname', ['id' => $opnameId]);
        $this->assertDatabaseMissing('stok_opname_detail', ['stok_opname_id' => $opnameId]);
        $this->assertDatabaseMissing('transaksi_persediaan', ['stok_opname_id' => $opnameId]);

        // Verifikasi stok aktif barang 1 otomatis kembali ke 10 (reversal)
        $this->barang1->refresh();
        $this->assertEquals(10, $this->barang1->stok_aktif);
    }

    public function test_non_admin_cannot_hapus_sesi(): void
    {
        $opname = $this->service->bukaSesi($this->admin, 'November 2026');

        $this->expectException(\RuntimeException::class);
        $this->service->hapusSesi($opname, $this->pimpinan);
    }
}
