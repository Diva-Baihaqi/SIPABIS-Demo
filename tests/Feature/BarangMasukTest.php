<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangMasukTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
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
            'name' => 'Administrator BPS',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Pranata Komputer',
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
            'stok_aktif' => 10,
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

    public function test_admin_can_access_barang_masuk_index_and_create(): void
    {
        $responseIndex = $this->actingAs($this->admin)->get('/admin/barang-masuk');
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->admin)->get('/admin/barang-masuk/create');
        $responseCreate->assertStatus(200);
    }

    public function test_non_admin_cannot_access_barang_masuk(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/admin/barang-masuk');
        $response->assertStatus(403);
    }

    public function test_admin_can_store_barang_masuk_with_spj_selesai(): void
    {
        $payload = [
            'tanggal_transaksi' => '2026-11-21',
            'nomor_dokumen' => 'KW-2026-001',
            'sumber_atau_tujuan' => 'CV Sumber Makmur',
            'status_spj' => 'selesai',
            'keterangan' => 'Pengadaan ATK Triwulan IV DIPA 2026',
            'items' => [
                [
                    'barang_id' => $this->barang1->id,
                    'jumlah' => 15,
                ],
                [
                    'barang_id' => $this->barang2->id,
                    'jumlah' => 25,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/barang-masuk', $payload);

        $response->assertRedirect('/admin/barang-masuk');
        $response->assertSessionHas('success');

        // Verify stock increases
        $this->assertDatabaseHas('barang_persediaan', [
            'id' => $this->barang1->id,
            'stok_aktif' => 25, // 10 + 15
        ]);

        $this->assertDatabaseHas('barang_persediaan', [
            'id' => $this->barang2->id,
            'stok_aktif' => 45, // 20 + 25
        ]);

        // Verify transaction ledger entries
        $this->assertDatabaseHas('transaksi_persediaan', [
            'barang_id' => $this->barang1->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 15,
            'status_spj' => 'selesai',
            'nomor_dokumen' => 'KW-2026-001',
            'sumber_atau_tujuan' => 'CV Sumber Makmur',
        ]);
    }

    public function test_admin_can_store_barang_langganan_with_spj_tertunda_and_update_it(): void
    {
        $payload = [
            'tanggal_transaksi' => '2026-11-21',
            'nomor_dokumen' => 'SJ-LANGGANAN-09',
            'sumber_atau_tujuan' => 'Toko Buku Cirebon Jaya',
            'status_spj' => 'tertunda',
            'keterangan' => 'Kebutuhan mendesak survei lapangan (SPJ menyusul)',
            'items' => [
                [
                    'barang_id' => $this->barang1->id,
                    'jumlah' => 30,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/barang-masuk', $payload);
        $response->assertRedirect('/admin/barang-masuk');

        $transaksi = TransaksiPersediaan::where('nomor_dokumen', 'SJ-LANGGANAN-09')->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals('tertunda', $transaksi->status_spj);

        // Update status SPJ to selesai
        $updateResponse = $this->actingAs($this->admin)->patch("/admin/barang-masuk/{$transaksi->id}/update-spj");
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('selesai', $transaksi->fresh()->status_spj);
    }

    public function test_store_validation_fails_with_invalid_data(): void
    {
        $payload = [
            'tanggal_transaksi' => 'not-a-date',
            'items' => [],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/barang-masuk', $payload);
        $response->assertSessionHasErrors(['tanggal_transaksi', 'items']);
    }
}
