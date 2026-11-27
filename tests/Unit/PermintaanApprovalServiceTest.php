<?php

namespace Tests\Unit;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\PermintaanApprovalService;
use App\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermintaanApprovalServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermintaanApprovalService $service;
    protected StockMovementService $stockService;
    protected User $admin;
    protected User $pemohon;
    protected Ruangan $ruangan;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = new StockMovementService();
        $this->service = new PermintaanApprovalService($this->stockService);

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R02',
            'nama_ruangan' => 'Tim Statistik Sosial',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@bps.go.id',
            'nip' => '198501012008011001',
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
            'password' => bcrypt('bps12345'),
        ]);

        $this->pemohon = User::create([
            'name' => 'Staf Sosial',
            'email' => 'staf@bps.go.id',
            'nip' => '199501012020011002',
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'password' => bcrypt('bps12345'),
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Perlengkapan ATK harian',
        ]);

        $this->barang1 = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kertas F4 70gr',
            'satuan' => 'Rim',
            'stok_minimum' => 5,
            'stok_aktif' => 20,
            'harga_satuan_estimasi' => 55000,
        ]);

        $this->barang2 = BarangPersediaan::create([
            'kode_barang' => 'ATK-002',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Ballpoint Standard AE7 Hitam',
            'satuan' => 'Lusin',
            'stok_minimum' => 5,
            'stok_aktif' => 15,
            'harga_satuan_estimasi' => 24000,
        ]);
    }

    public function test_buat_permintaan_berhasil_membuat_record_dan_nomor_dokumen_bps(): void
    {
        $items = [
            ['barang_id' => $this->barang1->id, 'jumlah_diminta' => 3, 'catatan_item' => 'Survei Susenas'],
            ['barang_id' => $this->barang2->id, 'jumlah_diminta' => 2, 'catatan_item' => 'Survei Sakernas'],
        ];

        $permintaan = $this->service->buatPermintaan($this->pemohon, $items, 'Kebutuhan survei lapangan');

        $this->assertNotNull($permintaan);
        $this->assertEquals('diajukan', $permintaan->status);
        $this->assertStringStartsWith('PSD-', $permintaan->nomor_permintaan);
        $this->assertStringContainsString('/32091/PL.711/', $permintaan->nomor_permintaan);
        $this->assertCount(2, $permintaan->details);
        $this->assertEquals(0, $permintaan->details[0]->jumlah_disetujui);
    }

    public function test_verifikasi_permintaan_menyetujui_kuota_dan_mengubah_status_disetujui(): void
    {
        $items = [
            ['barang_id' => $this->barang1->id, 'jumlah_diminta' => 5],
            ['barang_id' => $this->barang2->id, 'jumlah_diminta' => 4],
        ];
        $permintaan = $this->service->buatPermintaan($this->pemohon, $items);

        $detail1 = $permintaan->details[0];
        $detail2 = $permintaan->details[1];

        // Admin menyetujui sebagian: item 1 disetujui 3 (dari 5), item 2 disetujui 2 (dari 4)
        $persetujuan = [
            $detail1->id => 3,
            $detail2->id => 2,
        ];

        $verified = $this->service->verifikasi($permintaan, $this->admin, $persetujuan, 'Disetujui sebagian sesuai kuota');

        $this->assertEquals('disetujui', $verified->status);
        $this->assertEquals($this->admin->id, $verified->verifikator_id);
        $this->assertEquals(3, $verified->details->firstWhere('id', $detail1->id)->jumlah_disetujui);
        $this->assertEquals(2, $verified->details->firstWhere('id', $detail2->id)->jumlah_disetujui);

        // Pastikan stok fisik BELUM berkurang saat baru disetujui
        $this->barang1->refresh();
        $this->barang2->refresh();
        $this->assertEquals(20, $this->barang1->stok_aktif);
        $this->assertEquals(15, $this->barang2->stok_aktif);
    }

    public function test_serah_terima_memotong_stok_aktif_dan_mencatat_status_selesai(): void
    {
        $items = [
            ['barang_id' => $this->barang1->id, 'jumlah_diminta' => 4],
        ];
        $permintaan = $this->service->buatPermintaan($this->pemohon, $items);
        $detail = $permintaan->details[0];

        $this->service->verifikasi($permintaan, $this->admin, [$detail->id => 4]);

        $selesai = $this->service->serahTerima($permintaan, $this->admin, 'bukti/ttd-serah-terima.png');

        $this->assertEquals('selesai', $selesai->status);
        $this->assertNotNull($selesai->tanggal_serah_terima);
        $this->assertEquals('bukti/ttd-serah-terima.png', $selesai->file_bukti_serah_terima);

        // Stok fisik berkurang secara resmi
        $this->barang1->refresh();
        $this->assertEquals(16, $this->barang1->stok_aktif); // 20 - 4 = 16

        // Kartu kendali tercatat
        $this->assertDatabaseHas('transaksi_persediaan', [
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -4,
        ]);
    }

    public function test_pembatalan_dan_penolakan_permintaan(): void
    {
        $items = [
            ['barang_id' => $this->barang1->id, 'jumlah_diminta' => 2],
        ];
        $permintaan = $this->service->buatPermintaan($this->pemohon, $items);

        // Uji tolak oleh admin
        $ditolak = $this->service->tolak($permintaan, $this->admin, 'Stok difokuskan untuk sensus');
        $this->assertEquals('ditolak', $ditolak->status);
        $this->assertEquals('Stok difokuskan untuk sensus', $ditolak->catatan_admin);

        // Buat permintaan baru untuk uji batalkan oleh pemohon
        $permintaan2 = $this->service->buatPermintaan($this->pemohon, $items);
        $dibatalkan = $this->service->batalkan($permintaan2, $this->pemohon, 'Tidak jadi karena kegiatan dibatalkan');
        $this->assertEquals('dibatalkan', $dibatalkan->status);
    }
}
