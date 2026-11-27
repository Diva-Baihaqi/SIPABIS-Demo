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

class PermintaanPegawaiTest extends TestCase
{
    use RefreshDatabase;

    protected User $pegawai;
    protected User $pegawaiLain;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;
    protected Ruangan $ruangan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R-SOSIAL',
            'nama_ruangan' => 'Tim Statistik Sosial',
            'penanggung_jawab' => 'Ketua Tim Sosial',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai BPS Satu',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $this->pegawaiLain = User::create([
            'name' => 'Pegawai BPS Dua',
            'nip' => 'user2',
            'email' => 'user2@bps.go.id',
            'password' => bcrypt('user2'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pranata Komputer',
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
            'nama_barang' => 'Spidol Snowman Whiteboard',
            'kategori_id' => $kategori->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 30,
            'stok_minimum' => 5,
        ]);
    }

    public function test_pegawai_can_access_permintaan_index_and_create(): void
    {
        $responseIndex = $this->actingAs($this->pegawai)->get('/pegawai/permintaan');
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->pegawai)->get('/pegawai/permintaan/create');
        $responseCreate->assertStatus(200);
    }

    public function test_pegawai_can_submit_permintaan_successfully(): void
    {
        $payload = [
            'catatan_pemohon' => 'Kebutuhan ATK untuk pencacahan Survei Susenas 2026',
            'items' => [
                [
                    'barang_id' => $this->barang1->id,
                    'jumlah_diminta' => 5,
                    'catatan_item' => 'Untuk cetak kuesioner',
                ],
                [
                    'barang_id' => $this->barang2->id,
                    'jumlah_diminta' => 3,
                    'catatan_item' => 'Untuk briefing petugas',
                ],
            ],
        ];

        $response = $this->actingAs($this->pegawai)->post('/pegawai/permintaan', $payload);

        $response->assertRedirect('/pegawai/permintaan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permintaan', [
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'status' => 'diajukan',
            'catatan_pemohon' => 'Kebutuhan ATK untuk pencacahan Survei Susenas 2026',
        ]);

        $this->assertDatabaseHas('permintaan_detail', [
            'barang_id' => $this->barang1->id,
            'jumlah_diminta' => 5,
            'jumlah_disetujui' => 0,
        ]);

        $this->assertDatabaseHas('permintaan_detail', [
            'barang_id' => $this->barang2->id,
            'jumlah_diminta' => 3,
            'jumlah_disetujui' => 0,
        ]);
    }

    public function test_submission_fails_when_items_empty(): void
    {
        $payload = [
            'catatan_pemohon' => 'Permintaan tanpa barang',
            'items' => [],
        ];

        $response = $this->actingAs($this->pegawai)->post('/pegawai/permintaan', $payload);
        $response->assertSessionHasErrors(['items']);
    }

    public function test_pegawai_can_view_own_permintaan_detail(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0001',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
            'catatan_pemohon' => 'Uji coba permintaan',
        ]);

        PermintaanDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $this->barang1->id,
            'jumlah_diminta' => 2,
            'jumlah_disetujui' => 0,
        ]);

        $response = $this->actingAs($this->pegawai)->get("/pegawai/permintaan/{$permintaan->id}");
        $response->assertStatus(200);
        $response->assertSee('REQ/BPS3209/2026/11/0001');
    }

    public function test_pegawai_can_cancel_own_permintaan_when_status_diajukan(): void
    {
        $permintaan = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0002',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
            'catatan_pemohon' => 'Permintaan akan dibatalkan',
        ]);

        $response = $this->actingAs($this->pegawai)->delete("/pegawai/permintaan/{$permintaan->id}", [
            'alasan' => 'Ada perubahan rencana anggaran',
        ]);

        $response->assertRedirect('/pegawai/permintaan');
        $response->assertSessionHas('success');

        $this->assertEquals('dibatalkan', $permintaan->fresh()->status);
    }

    public function test_pegawai_cannot_cancel_other_users_permintaan(): void
    {
        $permintaanLain = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0003',
            'user_id' => $this->pegawaiLain->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->pegawai)->delete("/pegawai/permintaan/{$permintaanLain->id}");
        $response->assertStatus(403);
    }

    public function test_permintaan_create_and_public_pages_prevent_modal_fouc(): void
    {
        $responseCreate = $this->actingAs($this->pegawai)->get('/pegawai/permintaan/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('[x-cloak] { display: none !important; }', false);

        $responsePublic = $this->get('/');
        $responsePublic->assertStatus(200);
        $responsePublic->assertSee('[x-cloak] { display: none !important; }', false);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($responseCreate->getContent());
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $modalRoot = $xpath->query("//div[@x-data='globalConfirmModal()']")->item(0);
        $this->assertNotNull($modalRoot, 'Global confirm modal container must exist.');
        $this->assertTrue($modalRoot->hasAttribute('x-cloak'), 'Global confirm modal root must retain x-cloak.');
        $this->assertStringNotContainsString('display: none', (string) $modalRoot->getAttribute('style'));

        $xShowNodes = $xpath->query(".//div[@x-show='isOpen']", $modalRoot);
        $this->assertSame(3, $xShowNodes->length);
        foreach ($xShowNodes as $node) {
            $normalizedStyle = strtolower(str_replace(' ', '', (string) $node->getAttribute('style')));
            $this->assertStringContainsString('display:none', $normalizedStyle);
        }
    }
}
