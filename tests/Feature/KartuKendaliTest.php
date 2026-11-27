<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuKendaliTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pimpinan;
    protected User $pegawaiSosial;
    protected User $pegawaiProduksi;
    protected Ruangan $ruanganSosial;
    protected Ruangan $ruanganProduksi;
    protected BarangPersediaan $barang;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->admin = User::create([
            'name' => 'Admin Subbag Umum',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Pengadministrasi BMN',
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS',
            'nip' => 'pimpinan',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Kepala Badan Pusat Statistik',
        ]);

        $this->pegawaiSosial = User::create([
            'name' => 'Pegawai Sosial',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $this->pegawaiProduksi = User::create([
            'name' => 'Pegawai Produksi',
            'nip' => 'user_prod',
            'email' => 'user_prod@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruanganProduksi->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Kategori ATK',
        ]);

        $this->barang = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);
    }

    public function test_admin_and_pimpinan_can_access_kartu_kendali_index_ruangan_and_barang(): void
    {
        // Admin
        $respAdminIndex = $this->actingAs($this->admin)->get('/kartu-kendali');
        $respAdminIndex->assertStatus(200);

        $respAdminRuangan = $this->actingAs($this->admin)->get("/kartu-kendali/ruangan/{$this->ruanganSosial->id}");
        $respAdminRuangan->assertStatus(200);
        $respAdminRuangan->assertSee($this->ruanganSosial->nama_ruangan);

        $respAdminBarang = $this->actingAs($this->admin)->get("/kartu-kendali/barang/{$this->barang->id}");
        $respAdminBarang->assertStatus(200);
        $respAdminBarang->assertSee($this->barang->nama_barang);

        // Pimpinan
        $respPimpinanIndex = $this->actingAs($this->pimpinan)->get('/kartu-kendali');
        $respPimpinanIndex->assertStatus(200);
    }

    public function test_pegawai_is_redirected_from_index_to_their_own_ruangan(): void
    {
        $response = $this->actingAs($this->pegawaiSosial)->get('/kartu-kendali');
        $response->assertRedirect("/kartu-kendali/ruangan/{$this->ruanganSosial->id}");
    }

    public function test_pegawai_can_access_their_own_ruangan_buku_kendali_and_print(): void
    {
        $respRuangan = $this->actingAs($this->pegawaiSosial)->get("/kartu-kendali/ruangan/{$this->ruanganSosial->id}");
        $respRuangan->assertStatus(200);
        $respRuangan->assertSee($this->ruanganSosial->nama_ruangan);

        $respCetak = $this->actingAs($this->pegawaiSosial)->get("/kartu-kendali/ruangan/{$this->ruanganSosial->id}/cetak");
        $respCetak->assertStatus(200);
        $respCetak->assertSee('BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS');
    }

    public function test_pegawai_cannot_access_other_ruangan_buku_kendali(): void
    {
        // Pegawai Sosial mencoba mengakses ruangan Produksi -> 403 Forbidden
        $response = $this->actingAs($this->pegawaiSosial)->get("/kartu-kendali/ruangan/{$this->ruanganProduksi->id}");
        $response->assertStatus(403);

        $responseCetak = $this->actingAs($this->pegawaiSosial)->get("/kartu-kendali/ruangan/{$this->ruanganProduksi->id}/cetak");
        $responseCetak->assertStatus(403);
    }

    public function test_admin_can_access_print_views_for_ruangan_and_barang(): void
    {
        $respCetakRuangan = $this->actingAs($this->admin)->get("/kartu-kendali/ruangan/{$this->ruanganSosial->id}/cetak");
        $respCetakRuangan->assertStatus(200);
        $respCetakRuangan->assertSee('BADAN PUSAT STATISTIK KABUPATEN CIREBON');

        $respCetakBarang = $this->actingAs($this->admin)->get("/kartu-kendali/barang/{$this->barang->id}/cetak");
        $respCetakBarang->assertStatus(200);
        $respCetakBarang->assertSee('RINCIAN BUKU PERSEDIAAN (UC_PER53)');
        $respCetakBarang->assertSee('Saldo Persediaan');
        $respCetakBarang->assertSee('Harga');
        $respCetakBarang->assertSee('Jumlah');
        $respCetakBarang->assertSee($this->barang->kode_bmn_lengkap);
    }

    public function test_kartu_kendali_index_supports_month_and_year_period_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.index', [
            'tahun' => 2026,
            'bulan' => '11',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tahun:');
        $response->assertSee('Bulan:');
        $response->assertSee('Semua Bulan (Jan - Des)');
        $response->assertSee('November');
    }

    public function test_kartu_stok_tab_supports_category_filtering(): void
    {
        $katTinta = KategoriBarang::create([
            'kode_kategori' => 'TINTA',
            'nama_kategori' => 'Tinta & Toner Printer',
        ]);

        $barangTinta = BarangPersediaan::create([
            'kode_barang' => 'TIN-001',
            'nama_barang' => 'Tinta Epson 003 Black',
            'kategori_id' => $katTinta->id,
            'satuan' => 'Botol',
            'stok_aktif' => 10,
            'stok_minimum' => 2,
        ]);

        // Filter kategori Tinta
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.index', [
            'tab' => 'barang',
            'kategori_id' => $katTinta->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tinta Epson 003 Black');
        $response->assertDontSee('Kertas HVS A4 80gr');
    }

    public function test_active_tab_persists_when_querying_barang_tab(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.index', [
            'search_barang' => 'HVS',
        ]));

        $response->assertStatus(200);
        $response->assertSee("activeTab: 'barang'", false);
    }

    public function test_kartu_kendali_supports_ajax_live_filter_markup_and_request(): void
    {
        $katKomputer = KategoriBarang::create([
            'kode_kategori' => 'KOMP',
            'nama_kategori' => 'Perlengkapan IT & Komputer',
        ]);

        $barangMouse = BarangPersediaan::create([
            'kode_barang' => 'KOM-001',
            'nama_barang' => 'Mouse Wireless Logitech M170',
            'kategori_id' => $katKomputer->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 15,
            'stok_minimum' => 3,
        ]);

        // 1. Verifikasi atribut live filter Alpine.js & container ID pada render awal
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.index'));
        $response->assertStatus(200);
        $response->assertSee('x-data="kartuKendaliLiveFilter()"', false);
        $response->assertSee('@change="applyFilter()"', false);
        $response->assertSee('id="kartu-kendali-ruangan-container"', false);
        $response->assertSee('id="kartu-stok-barang-container"', false);

        // 2. Verifikasi request AJAX partial filter kategori
        $ajaxResponse = $this->actingAs($this->admin)->get(route('kartu-kendali.index', [
            'tab' => 'barang',
            'kategori_id' => $katKomputer->id,
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'text/html',
        ]);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertSee('id="kartu-stok-barang-container"', false);
        $ajaxResponse->assertSee('Mouse Wireless Logitech M170');
        $ajaxResponse->assertDontSee('Kertas HVS A4 80gr');
    }
}
