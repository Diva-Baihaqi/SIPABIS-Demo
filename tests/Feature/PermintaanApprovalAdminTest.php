<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermintaanApprovalAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;
    protected Ruangan $ruangan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R-DISTRIBUSI',
            'nama_ruangan' => 'Tim Statistik Distribusi & Jasa',
            'penanggung_jawab' => 'Ketua Tim Distribusi',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Subbag Umum',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pengadministrasi BMN',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai Distribusi',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Kategori ATK',
        ]);

        $this->barang1 = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Kertas Folio F4 70gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 100,
            'stok_minimum' => 10,
        ]);

        $this->barang2 = BarangPersediaan::create([
            'kode_barang' => 'ATK-002',
            'nama_barang' => 'Ballpoint Standard AE7 Hitam',
            'kategori_id' => $kategori->id,
            'satuan' => 'Kotak',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);
    }

    public function test_admin_can_access_permintaan_approval_index_and_show(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0001',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
            'catatan_pemohon' => 'Uji coba permintaan',
        ]);

        $responseIndex = $this->actingAs($this->admin)->get('/admin/permintaan');
        $responseIndex->assertStatus(200);

        $responseShow = $this->actingAs($this->admin)->get("/admin/permintaan/{$permintaan->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee('REQ/BPS3209/2026/11/0001');
    }

    public function test_non_admin_cannot_access_admin_permintaan(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/admin/permintaan');
        $response->assertStatus(403);
    }

    public function test_admin_can_verify_and_partially_approve_permintaan(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0002',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
        ]);

        $detail1 = PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jumlah_diminta' => 10,
            'jumlah_disetujui' => 0,
        ]);

        $detail2 = PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang2->id,
            'jumlah_diminta' => 5,
            'jumlah_disetujui' => 0,
        ]);

        $payload = [
            'catatan_admin' => 'Disetujui sebagian sesuai ketersediaan buffer gudang BPS',
            'items' => [
                $detail1->id => 10, // Full quota
                $detail2->id => 2,  // Partial quota
            ],
        ];

        $response = $this->actingAs($this->admin)->post("/admin/permintaan/{$permintaan->id}/verifikasi", $payload);

        $response->assertRedirect("/admin/permintaan/{$permintaan->id}");
        $response->assertSessionHas('success');

        $this->assertEquals('disetujui', $permintaan->fresh()->status);
        $this->assertEquals(10, $detail1->fresh()->jumlah_disetujui);
        $this->assertEquals(2, $detail2->fresh()->jumlah_disetujui);
        $this->assertEquals($this->admin->id, $permintaan->fresh()->verifikator_id);

        // Stok aktif TIDAK boleh terpotong saat tahap persetujuan
        $this->assertEquals(100, $this->barang1->fresh()->stok_aktif);
        $this->assertEquals(50, $this->barang2->fresh()->stok_aktif);
    }

    public function test_admin_can_reject_permintaan_with_reason(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0003',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
        ]);

        $payload = [
            'alasan' => 'Stok persediaan sedang dialokasikan prioritas untuk Sensus Pertanian.',
        ];

        $response = $this->actingAs($this->admin)->post("/admin/permintaan/{$permintaan->id}/tolak", $payload);

        $response->assertRedirect("/admin/permintaan/{$permintaan->id}");
        $response->assertSessionHas('success');

        $this->assertEquals('ditolak', $permintaan->fresh()->status);
        $this->assertStringContainsString('Sensus Pertanian', $permintaan->fresh()->catatan_admin);
    }

    public function test_admin_can_execute_serah_terima_and_stock_is_deducted_atomically(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0004',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'disetujui',
        ]);

        PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jumlah_diminta' => 15,
            'jumlah_disetujui' => 15,
        ]);

        PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang2->id,
            'jumlah_diminta' => 8,
            'jumlah_disetujui' => 5,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/permintaan/{$permintaan->id}/serah-terima");

        $response->assertRedirect("/admin/permintaan/{$permintaan->id}");
        $response->assertSessionHas('success');

        // Status updated to selesai
        $this->assertEquals('selesai', $permintaan->fresh()->status);
        $this->assertNotNull($permintaan->fresh()->tanggal_serah_terima);

        // Stok fisik resmi terpotong di basis data
        $this->assertEquals(85, $this->barang1->fresh()->stok_aktif); // 100 - 15
        $this->assertEquals(45, $this->barang2->fresh()->stok_aktif); // 50 - 5

        // Ledger mutasi keluar tercatat di transaksi_persediaan
        $this->assertDatabaseHas('transaksi_persediaan', [
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -15,
        ]);

        $this->assertDatabaseHas('transaksi_persediaan', [
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang2->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -5,
        ]);
    }

    public function test_admin_can_override_nomor_permintaan_custom_during_verification(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'PSD-1/32091/PL.711/2026',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
        ]);

        $detail = PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jumlah_diminta' => 5,
        ]);

        $payload = [
            'nomor_permintaan_custom' => 'PSD-133/32091/PL.711/2026',
            'catatan_admin' => 'Sesuai bon fisik 133',
            'items' => [
                $detail->id => 5,
            ],
        ];

        $response = $this->actingAs($this->admin)->post("/admin/permintaan/{$permintaan->id}/verifikasi", $payload);

        $response->assertRedirect("/admin/permintaan/{$permintaan->id}");
        $this->assertEquals('PSD-133/32091/PL.711/2026', $permintaan->fresh()->nomor_permintaan);
    }
}
