<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientStockException;
use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StockMovementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockMovementService $service;
    protected User $user;
    protected BarangPersediaan $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StockMovementService();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R01',
            'nama_ruangan' => 'Subbagian Umum',
        ]);

        $this->user = User::create([
            'name' => 'Admin Pengelola',
            'email' => 'admin@bps.go.id',
            'nip' => '198901012010011001',
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
            'password' => bcrypt('bps12345'),
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Perlengkapan ATK harian',
        ]);

        $this->barang = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kertas A4 70gr',
            'satuan' => 'Rim',
            'stok_minimum' => 10,
            'stok_aktif' => 20,
            'harga_satuan_estimasi' => 50000,
        ]);
    }

    public function test_tambah_stok_berhasil_menambah_stok_dan_mencatat_transaksi(): void
    {
        $transaksi = $this->service->tambahStok($this->barang, 15, $this->user, [
            'nomor_dokumen' => 'SJ-2026-001',
            'sumber_atau_tujuan' => 'CV Sumber Makmur',
            'keterangan' => 'Pengadaan triwulan 1',
        ]);

        $this->barang->refresh();

        $this->assertEquals(35, $this->barang->stok_aktif);
        $this->assertEquals(15, $transaksi->jumlah);
        $this->assertEquals(20, $transaksi->saldo_sebelum);
        $this->assertEquals(35, $transaksi->saldo_setelah);
        $this->assertEquals('masuk', $transaksi->jenis_transaksi);
        $this->assertEquals('SJ-2026-001', $transaksi->nomor_dokumen);
        $this->assertDatabaseHas('transaksi_persediaan', [
            'id' => $transaksi->id,
            'barang_id' => $this->barang->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 15,
        ]);
    }

    public function test_tambah_stok_dengan_jumlah_nol_atau_negatif_melempar_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->tambahStok($this->barang, 0, $this->user);
    }

    public function test_kurangi_stok_berhasil_mengurangi_stok_dan_mencatat_transaksi_negatif(): void
    {
        $transaksi = $this->service->kurangiStok($this->barang, 8, $this->user, [
            'nomor_dokumen' => 'REQ-001',
            'sumber_atau_tujuan' => 'Tim Nerwilis',
            'keterangan' => 'Kebutuhan survei neraca',
        ]);

        $this->barang->refresh();

        $this->assertEquals(12, $this->barang->stok_aktif);
        $this->assertEquals(-8, $transaksi->jumlah);
        $this->assertEquals(20, $transaksi->saldo_sebelum);
        $this->assertEquals(12, $transaksi->saldo_setelah);
        $this->assertEquals('keluar', $transaksi->jenis_transaksi);
        $this->assertDatabaseHas('transaksi_persediaan', [
            'id' => $transaksi->id,
            'barang_id' => $this->barang->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -8,
        ]);
    }

    public function test_kurangi_stok_melebihi_ketersediaan_melempar_insufficient_stock_exception(): void
    {
        $this->expectException(InsufficientStockException::class);

        try {
            $this->service->kurangiStok($this->barang, 25, $this->user);
        } finally {
            $this->barang->refresh();
            // Pastikan stok tidak berubah (rollback)
            $this->assertEquals(20, $this->barang->stok_aktif);
        }
    }

    public function test_koreksi_stok_menyesuaikan_stok_aktif_dan_mencatat_selisih(): void
    {
        // Stok awal 20, fisik ternyata 18 (selisih -2)
        $transaksi = $this->service->koreksiStok($this->barang, 18, $this->user, [
            'nomor_dokumen' => 'BA-SO-001',
            'keterangan' => 'Selisih opname November',
        ]);

        $this->barang->refresh();

        $this->assertNotNull($transaksi);
        $this->assertEquals(18, $this->barang->stok_aktif);
        $this->assertEquals(-2, $transaksi->jumlah);
        $this->assertEquals(20, $transaksi->saldo_sebelum);
        $this->assertEquals(18, $transaksi->saldo_setelah);
        $this->assertEquals('penyesuaian', $transaksi->jenis_transaksi);
    }

    public function test_koreksi_stok_tanpa_selisih_mengembalikan_null_tanpa_transaksi(): void
    {
        // Stok fisik sama dengan stok sistem (20)
        $transaksi = $this->service->koreksiStok($this->barang, 20, $this->user);

        $this->assertNull($transaksi);
        $this->assertEquals(0, TransaksiPersediaan::where('barang_id', $this->barang->id)->count());
    }
}
