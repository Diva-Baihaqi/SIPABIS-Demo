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

class PermintaanLiveFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $pegawai;
    protected User $pimpinan;
    protected Ruangan $ruangan;
    protected KategoriBarang $kategoriKertas;
    protected KategoriBarang $kategoriTulis;
    protected BarangPersediaan $barangKertas;
    protected BarangPersediaan $barangSpidol;
    protected Permintaan $permintaan1;
    protected Permintaan $permintaan2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R-PIMPINAN',
            'nama_ruangan' => 'Ruang Pimpinan BPS',
            'penanggung_jawab' => 'Kepala BPS',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai Staf BPS',
            'nip' => '198501012010011001',
            'email' => 'staf@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Statistisi Ahli Pertama',
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS Kabupaten',
            'nip' => '197801152002121001',
            'email' => 'kepala@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Kepala BPS Kabupaten/Kota',
        ]);

        $this->kategoriKertas = KategoriBarang::create([
            'kode_kategori' => 'KAT-KERTAS',
            'nama_kategori' => 'Kertas & Amplop',
            'deskripsi' => 'Perlengkapan cetak dan surat',
        ]);

        $this->kategoriTulis = KategoriBarang::create([
            'kode_kategori' => 'KAT-TULIS',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Pena, pensil, spidol',
        ]);

        $this->barangKertas = BarangPersediaan::create([
            'kode_barang' => 'KRT-001',
            'nama_barang' => 'Kertas HVS A4 80gsm SIDU',
            'kategori_id' => $this->kategoriKertas->id,
            'satuan' => 'Rim',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);

        $this->barangSpidol = BarangPersediaan::create([
            'kode_barang' => 'SPL-001',
            'nama_barang' => 'Spidol Snowman Whiteboard Hitam',
            'kategori_id' => $this->kategoriTulis->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 20,
            'stok_minimum' => 3,
        ]);

        // Permintaan 1: Kertas HVS, status 'diajukan'
        $this->permintaan1 = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0101',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-20',
            'status' => 'diajukan',
            'catatan_pemohon' => 'Kebutuhan cetak dokumen publikasi BPS',
        ]);

        PermintaanDetail::create([
            'permintaan_id' => $this->permintaan1->id,
            'barang_id' => $this->barangKertas->id,
            'jumlah_diminta' => 5,
            'jumlah_disetujui' => 0,
        ]);

        // Permintaan 2: Spidol, status 'selesai'
        $this->permintaan2 = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS3209/2026/11/0102',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => '2026-11-21',
            'status' => 'selesai',
            'catatan_pemohon' => 'Kebutuhan rapat koordinasi pimpinan',
        ]);

        PermintaanDetail::create([
            'permintaan_id' => $this->permintaan2->id,
            'barang_id' => $this->barangSpidol->id,
            'jumlah_diminta' => 2,
            'jumlah_disetujui' => 2,
        ]);
    }

    public function test_pimpinan_can_access_permintaan_index_and_create(): void
    {
        $responseIndex = $this->actingAs($this->pimpinan)->get('/pegawai/permintaan');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Permintaan ATK Saya');

        $responseCreate = $this->actingAs($this->pimpinan)->get('/pegawai/permintaan/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Ajukan Permintaan ATK');
    }

    public function test_black_button_is_removed_and_lime_button_remains(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/pegawai/permintaan');
        $response->assertStatus(200);

        // Tombol hijau di banner harus tetap ada
        $response->assertSee('Buat Pengajuan');

        // Pastikan view index tidak lagi memuat tombol hitam
        $indexViewContent = file_get_contents(resource_path('views/pegawai/permintaan/index.blade.php'));
        $this->assertStringNotContainsString('Ajukan ATK Baru', $indexViewContent);
        $this->assertStringNotContainsString('bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition', $indexViewContent);
    }

    public function test_create_view_has_clean_green_button_and_no_bottom_button(): void
    {
        // Buat 1 barang stok 0 untuk memastikan tidak muncul di katalog ajukan
        $barangHabis = BarangPersediaan::create([
            'kode_barang' => 'HBS-999',
            'nama_barang' => 'Barang Stok Kosong Uji',
            'kategori_id' => $this->kategoriKertas->id,
            'satuan' => 'Unit',
            'stok_aktif' => 0,
            'stok_minimum' => 1,
        ]);

        $response = $this->actingAs($this->pegawai)->get('/pegawai/permintaan/create');
        $response->assertStatus(200);

        // Tombol hijau atas harus rapi bertuliskan Tambah Baris Barang (tanpa double +)
        $response->assertSee('Tambah Baris Barang');
        $response->assertDontSee('+ Tambah Baris Barang');

        // Tombol bawah "+ Tambah Barang Lainnya" harus sudah dihapus
        $response->assertDontSee('Tambah Barang Lainnya');

        // Katalog visual bergambar dan filter kategori aktif harus tampil
        $response->assertSee('Pilih Barang dari Katalog Bergambar');
        $response->assertSee('Semua Kategori (2)');
        $response->assertSee('Kertas & Amplop', true);
        $response->assertSee('Alat Tulis Kantor');
        $response->assertSee('foto_url', false);
        $response->assertSee('Kertas HVS A4 80gsm SIDU');
        $response->assertDontSee('Barang Stok Kosong Uji');
    }

    public function test_pimpinan_can_submit_permintaan_atk(): void
    {
        $response = $this->actingAs($this->pimpinan)->post('/pegawai/permintaan', [
            'catatan_pemohon' => 'Kebutuhan ATK rapat pimpinan BPS',
            'items' => [
                [
                    'barang_id' => $this->barangKertas->id,
                    'jumlah_diminta' => 2,
                    'catatan_item' => 'Untuk bahan paparan',
                ],
            ],
        ]);

        $response->assertRedirect('/pegawai/permintaan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permintaan', [
            'user_id' => $this->pimpinan->id,
            'catatan_pemohon' => 'Kebutuhan ATK rapat pimpinan BPS',
            'status' => 'diajukan',
        ]);
    }

    public function test_filter_by_status_diajukan(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?status=diajukan');
        $response->assertStatus(200);
        $response->assertSee('REQ/BPS3209/2026/11/0101');
        $response->assertDontSee('REQ/BPS3209/2026/11/0102');
    }

    public function test_filter_by_status_selesai(): void
    {
        $response = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?status=selesai');
        $response->assertStatus(200);
        $response->assertDontSee('REQ/BPS3209/2026/11/0101');
        $response->assertSee('REQ/BPS3209/2026/11/0102');
    }

    public function test_filter_by_kategori(): void
    {
        // Filter kategori Kertas
        $responseKertas = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?kategori_id=' . $this->kategoriKertas->id);
        $responseKertas->assertStatus(200);
        $responseKertas->assertSee('REQ/BPS3209/2026/11/0101');
        $responseKertas->assertDontSee('REQ/BPS3209/2026/11/0102');

        // Filter kategori Alat Tulis
        $responseTulis = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?kategori_id=' . $this->kategoriTulis->id);
        $responseTulis->assertStatus(200);
        $responseTulis->assertDontSee('REQ/BPS3209/2026/11/0101');
        $responseTulis->assertSee('REQ/BPS3209/2026/11/0102');
    }

    public function test_filter_by_search_keyword(): void
    {
        // Cari nomor dokumen
        $resDoc = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?search=0101');
        $resDoc->assertStatus(200);
        $resDoc->assertSee('REQ/BPS3209/2026/11/0101');
        $resDoc->assertDontSee('REQ/BPS3209/2026/11/0102');

        // Cari peruntukan / catatan
        $resCatatan = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?search=publikasi');
        $resCatatan->assertStatus(200);
        $resCatatan->assertSee('REQ/BPS3209/2026/11/0101');
        $resCatatan->assertDontSee('REQ/BPS3209/2026/11/0102');

        // Cari nama barang
        $resBarang = $this->actingAs($this->pegawai)->get('/pegawai/permintaan?search=Snowman');
        $resBarang->assertStatus(200);
        $resBarang->assertDontSee('REQ/BPS3209/2026/11/0101');
        $resBarang->assertSee('REQ/BPS3209/2026/11/0102');
    }

    public function test_ajax_request_returns_table_and_reset_containers(): void
    {
        $response = $this->actingAs($this->pegawai)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/pegawai/permintaan?status=diajukan');

        $response->assertStatus(200);
        $response->assertSee('id="permintaan-table-container"', false);
        $response->assertSee('id="permintaan-reset-container"', false);
        $response->assertSee('Reset Filter');
        $response->assertSee('REQ/BPS3209/2026/11/0101');
        $response->assertDontSee('REQ/BPS3209/2026/11/0102');
    }

    public function test_sidebar_active_menu_highlight_distinguishes_index_and_create(): void
    {
        // 1. Pada halaman Ajukan ATK Baru (/pegawai/permintaan/create)
        $responseCreate = $this->actingAs($this->pegawai)->get('/pegawai/permintaan/create');
        $responseCreate->assertStatus(200);

        $domCreate = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domCreate->loadHTML($responseCreate->getContent());
        libxml_clear_errors();
        $xpathCreate = new \DOMXPath($domCreate);

        $linkPermintaanCreate = $xpathCreate->query("//nav//a[contains(., 'Permintaan ATK Saya')]")->item(0);
        $linkAjukanCreate = $xpathCreate->query("//nav//a[contains(., 'Ajukan ATK Baru')]")->item(0);

        $this->assertNotNull($linkPermintaanCreate);
        $this->assertNotNull($linkAjukanCreate);

        // Pada halaman create: Ajukan ATK Baru AKTIF, Permintaan ATK Saya TIDAK aktif
        $this->assertStringContainsString('border-bps-orange-400', (string) $linkAjukanCreate->getAttribute('class'));
        $this->assertStringNotContainsString('border-bps-orange-400', (string) $linkPermintaanCreate->getAttribute('class'));

        // 2. Pada halaman Permintaan ATK Saya (/pegawai/permintaan)
        $responseIndex = $this->actingAs($this->pegawai)->get('/pegawai/permintaan');
        $responseIndex->assertStatus(200);

        $domIndex = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domIndex->loadHTML($responseIndex->getContent());
        libxml_clear_errors();
        $xpathIndex = new \DOMXPath($domIndex);

        $linkPermintaanIndex = $xpathIndex->query("//nav//a[contains(., 'Permintaan ATK Saya')]")->item(0);
        $linkAjukanIndex = $xpathIndex->query("//nav//a[contains(., 'Ajukan ATK Baru')]")->item(0);

        $this->assertStringContainsString('border-bps-orange-400', (string) $linkPermintaanIndex->getAttribute('class'));
        $this->assertStringNotContainsString('border-bps-orange-400', (string) $linkAjukanIndex->getAttribute('class'));

        // 3. Pada halaman Detail Permintaan (/pegawai/permintaan/{id})
        $responseShow = $this->actingAs($this->pegawai)->get("/pegawai/permintaan/{$this->permintaan1->id}");
        $responseShow->assertStatus(200);

        $domShow = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domShow->loadHTML($responseShow->getContent());
        libxml_clear_errors();
        $xpathShow = new \DOMXPath($domShow);

        $linkPermintaanShow = $xpathShow->query("//nav//a[contains(., 'Permintaan ATK Saya')]")->item(0);
        $linkAjukanShow = $xpathShow->query("//nav//a[contains(., 'Ajukan ATK Baru')]")->item(0);

        $this->assertStringContainsString('border-bps-orange-400', (string) $linkPermintaanShow->getAttribute('class'));
        $this->assertStringNotContainsString('border-bps-orange-400', (string) $linkAjukanShow->getAttribute('class'));
    }
}
