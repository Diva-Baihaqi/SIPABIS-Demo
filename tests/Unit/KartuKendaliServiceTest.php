<?php

namespace Tests\Unit;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use App\Services\KartuKendaliService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuKendaliServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KartuKendaliService $service;
    protected Ruangan $ruanganSosial;
    protected Ruangan $ruanganProduksi;
    protected User $userSosial;
    protected BarangPersediaan $kertasA4;
    protected BarangPersediaan $spidol;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new KartuKendaliService();

        $this->ruanganSosial = Ruangan::create([
            'kode_ruangan' => 'R-SOSIAL',
            'nama_ruangan' => 'Tim Statistik Sosial',
            'penanggung_jawab' => 'Ketua Tim Sosial',
        ]);

        $this->ruanganProduksi = Ruangan::create([
            'kode_ruangan' => 'R-PRODUKSI',
            'nama_ruangan' => 'Tim Statistik Produksi',
            'penanggung_jawab' => 'Ketua Tim Produksi',
        ]);

        $this->userSosial = User::create([
            'name' => 'Budi Statistisi Sosial',
            'nip' => '198501012010011001',
            'email' => 'budi@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Kategori ATK',
        ]);

        $this->kertasA4 = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 100,
            'stok_minimum' => 10,
        ]);

        $this->spidol = BarangPersediaan::create([
            'kode_barang' => 'ATK-002',
            'nama_barang' => 'Spidol Whiteboard Snowman',
            'kategori_id' => $kategori->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);
    }

    public function test_get_buku_kendali_ruangan_returns_correct_aggregation_and_transactions(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0001',
            'user_id' => $this->userSosial->id,
            'ruangan_id' => $this->ruanganSosial->id,
            'tanggal_pengajuan' => '2026-11-20',
            'status' => 'selesai',
            'catatan_pemohon' => 'Survei Susenas',
        ]);

        TransaksiPersediaan::create([
            'barang_id' => $this->kertasA4->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -10,
            'saldo_sebelum' => 100,
            'saldo_setelah' => 90,
            'tanggal_transaksi' => '2026-11-21',
            'nomor_dokumen' => $permintaan->nomor_permintaan,
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
            'permintaan_id' => $permintaan->id,
            'keterangan' => 'Distribusi ATK Susenas',
        ]);

        TransaksiPersediaan::create([
            'barang_id' => $this->spidol->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -5,
            'saldo_sebelum' => 50,
            'saldo_setelah' => 45,
            'tanggal_transaksi' => '2026-11-21',
            'nomor_dokumen' => $permintaan->nomor_permintaan,
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
            'permintaan_id' => $permintaan->id,
            'keterangan' => 'Briefing Petugas Susenas',
        ]);

        $data = $this->service->getBukuKendaliRuangan($this->ruanganSosial->id, [
            'tahun' => 2026,
            'bulan' => 11,
        ]);

        $this->assertEquals($this->ruanganSosial->id, $data['ruangan']->id);
        $this->assertEquals(2, $data['transaksiList']->count());
        $this->assertEquals(15, $data['totalUnitKeluar']); // 10 + 5
        $this->assertEquals(2, $data['totalJenisBarang']);
        $this->assertCount(2, $data['rekapBarang']);
    }

    public function test_get_kartu_stok_barang_calculates_correct_saldo_awal_and_running_balance(): void
    {
        // 1. Mutasi masuk awal bulan
        TransaksiPersediaan::create([
            'barang_id' => $this->kertasA4->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 50,
            'saldo_sebelum' => 0,
            'saldo_setelah' => 50,
            'tanggal_transaksi' => '2026-11-01',
            'nomor_dokumen' => 'KW-001',
            'sumber_atau_tujuan' => 'CV Sumber Makmur',
            'keterangan' => 'Pengadaan',
        ]);

        // 2. Mutasi keluar tanggal 10
        TransaksiPersediaan::create([
            'barang_id' => $this->kertasA4->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -10,
            'saldo_sebelum' => 50,
            'saldo_setelah' => 40,
            'tanggal_transaksi' => '2026-11-10',
            'nomor_dokumen' => 'REQ-01',
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
            'keterangan' => 'Pengeluaran Tim Sosial',
        ]);

        // 3. Mutasi keluar tanggal 20
        TransaksiPersediaan::create([
            'barang_id' => $this->kertasA4->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -5,
            'saldo_sebelum' => 40,
            'saldo_setelah' => 35,
            'tanggal_transaksi' => '2026-11-20',
            'nomor_dokumen' => 'REQ-02',
            'sumber_atau_tujuan' => $this->ruanganProduksi->nama_ruangan,
            'keterangan' => 'Pengeluaran Tim Produksi',
        ]);

        // Query dengan rentang 2026-11-05 s/d 2026-11-25
        $data = $this->service->getKartuStokBarang($this->kertasA4->id, [
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-25',
        ]);

        // Saldo awal sebelum 2026-11-05 harus 50 (dari transaksi tgl 01)
        $this->assertEquals(50, $data['saldoAwal']);
        $this->assertCount(2, $data['mutasiList']); // Hanya transaksi tgl 10 dan 20
        $this->assertEquals(0, $data['totalMasuk']);
        $this->assertEquals(15, $data['totalKeluar']);
        $this->assertEquals(35, $data['saldoAkhir']);
    }

    public function test_get_rekap_semua_ruangan_aggregates_all_units_properly(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0002',
            'user_id' => $this->userSosial->id,
            'ruangan_id' => $this->ruanganSosial->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'selesai',
        ]);

        TransaksiPersediaan::create([
            'barang_id' => $this->kertasA4->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -20,
            'saldo_sebelum' => 100,
            'saldo_setelah' => 80,
            'tanggal_transaksi' => '2026-11-21',
            'nomor_dokumen' => $permintaan->nomor_permintaan,
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
            'permintaan_id' => $permintaan->id,
        ]);

        $rekap = $this->service->getRekapSemuaRuangan(['tahun' => 2026]);

        $sosialRekap = $rekap->firstWhere('id', $this->ruanganSosial->id);
        $this->assertNotNull($sosialRekap);
        $this->assertEquals(20, $sosialRekap->total_unit);
        $this->assertEquals(1, $sosialRekap->total_transaksi);

        $produksiRekap = $rekap->firstWhere('id', $this->ruanganProduksi->id);
        $this->assertNotNull($produksiRekap);
        $this->assertEquals(0, $produksiRekap->total_unit);
    }

    public function test_barang_persediaan_formats_kode_bmn_lengkap_properly(): void
    {
        $kategoriId = $this->kertasA4->kategori_id;

        $barang1 = BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'kategori_id' => $kategoriId,
            'nama_barang' => 'Ballpoin Balliner',
            'satuan' => 'Batang',
            'stok_aktif' => 10,
            'stok_minimum' => 2,
        ]);

        $this->assertEquals('1.01.03.01.001.000001', $barang1->kode_bmn_lengkap);

        $barang2 = BarangPersediaan::create([
            'kode_barang' => '000568',
            'kode_sakti' => '1010399999',
            'kategori_id' => $kategoriId,
            'nama_barang' => 'Buku Pedoman SPDT',
            'satuan' => 'Set',
            'stok_aktif' => 5,
            'stok_minimum' => 1,
        ]);

        $this->assertEquals('1.01.03.99.999.000568', $barang2->kode_bmn_lengkap);
    }

    public function test_get_kartu_stok_barang_calculates_financial_amounts(): void
    {
        $kategoriId = $this->kertasA4->kategori_id;

        $barang = BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'kategori_id' => $kategoriId,
            'nama_barang' => 'Ballpoin Balliner',
            'satuan' => 'Batang',
            'stok_aktif' => 20,
            'stok_minimum' => 5,
            'harga_satuan_estimasi' => 4000.00,
        ]);

        // Masuk 10 unit
        TransaksiPersediaan::create([
            'barang_id' => $barang->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 10,
            'saldo_sebelum' => 0,
            'saldo_setelah' => 10,
            'tanggal_transaksi' => '2026-03-01',
            'nomor_dokumen' => 'SPK-01',
            'sumber_atau_tujuan' => 'Pengadaan',
        ]);

        // Keluar 3 unit
        TransaksiPersediaan::create([
            'barang_id' => $barang->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -3,
            'saldo_sebelum' => 10,
            'saldo_setelah' => 7,
            'tanggal_transaksi' => '2026-03-05',
            'nomor_dokumen' => 'PSD-01/32091/PL.711/2026',
            'sumber_atau_tujuan' => 'Tim Umum',
        ]);

        $data = $this->service->getKartuStokBarang($barang->id, [
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
        ]);

        $this->assertEquals(4000, $data['hargaSatuan']);
        $this->assertEquals(10, $data['totalMasuk']);
        $this->assertEquals(40000, $data['totalMasukRp']);
        $this->assertEquals(3, $data['totalKeluar']);
        $this->assertEquals(12000, $data['totalKeluarRp']);
        $this->assertEquals(7, $data['saldoAkhir']);
        $this->assertEquals(28000, $data['saldoAkhirRp']);

        $transaksiKeluar = $data['mutasiList']->last();
        $this->assertEquals(3, $transaksiKeluar->keluar);
        $this->assertEquals(12000, $transaksiKeluar->keluar_rp);
        $this->assertEquals(7, $transaksiKeluar->saldo);
        $this->assertEquals(28000, $transaksiKeluar->saldo_rp);
    }

    public function test_kartu_stok_reconciles_pre_existing_initial_stock_when_no_prior_transactions(): void
    {
        $kategori = KategoriBarang::first();

        $pulpen = BarangPersediaan::create([
            'kode_barang' => 'ATK-PULPEN-01',
            'nama_barang' => 'Pulpen Gel Hitam BPS',
            'kategori_id' => $kategori->id,
            'satuan' => 'Buah',
            'stok_aktif' => 7,
            'stok_minimum' => 2,
            'harga_satuan_estimasi' => 5000,
        ]);

        // Transaksi peminjaman / pengeluaran 1 buah pulpen
        TransaksiPersediaan::create([
            'barang_id' => $pulpen->id,
            'user_id' => $this->userSosial->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -1,
            'saldo_sebelum' => 8,
            'saldo_setelah' => 7,
            'tanggal_transaksi' => '2026-11-20',
            'nomor_dokumen' => 'REQ/2026/11/0009',
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
        ]);

        $data = $this->service->getKartuStokBarang($pulpen->id, [
            'tahun' => 2026,
        ]);

        // Verifikasi saldo awal mundur otomatis menjadi 8 (7 - (-1) = 8)
        $this->assertEquals(8, $data['saldoAwal']);
        $this->assertEquals(0, $data['totalMasuk']);
        $this->assertEquals(1, $data['totalKeluar']);
        $this->assertEquals(7, $data['saldoAkhir']);
        // Verifikasi kesetaraan akuntansi: Saldo Awal + Total Masuk - Total Keluar = Saldo Akhir
        $this->assertEquals($data['saldoAkhir'], $data['saldoAwal'] + $data['totalMasuk'] - $data['totalKeluar']);
    }
}
