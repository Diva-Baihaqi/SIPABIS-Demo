<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangKatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected User $pimpinan;
    protected KategoriBarang $kategori;
    protected BarangPersediaan $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Rina Kusuma Wardani',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Administrator Persediaan',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->pegawai = User::create([
            'nip' => 'user',
            'name' => 'Pegawai Tim Kerja',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->pimpinan = User::create([
            'nip' => 'pimpinan',
            'name' => 'Kepala BPS',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->kategori = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $this->barang = BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Ballpoin Balliner',
            'satuan' => 'batang',
            'stok_minimum' => 5,
            'stok_aktif' => 12,
            'harga_satuan_estimasi' => 4000,
        ]);
    }

    public function test_all_authenticated_roles_can_view_katalog(): void
    {
        // 1. Admin
        $this->actingAs($this->admin)->get(route('barang.index'))
            ->assertStatus(200)
            ->assertSee('Katalog Persediaan ATK', false)
            ->assertSee('Ballpoin Balliner');

        // 2. Pegawai
        $this->actingAs($this->pegawai)->get(route('barang.index'))
            ->assertStatus(200)
            ->assertSee('Ballpoin Balliner');

        // 3. Pimpinan
        $this->actingAs($this->pimpinan)->get(route('barang.index'))
            ->assertStatus(200)
            ->assertSee('Ballpoin Balliner');
    }

    public function test_unauthenticated_user_cannot_view_katalog(): void
    {
        $this->get(route('barang.index'))->assertRedirect(route('login'));
    }

    public function test_search_and_filter_katalog(): void
    {
        BarangPersediaan::create([
            'kode_barang' => '000002',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Kertas HVS A4',
            'satuan' => 'rim',
            'stok_minimum' => 10,
            'stok_aktif' => 3, // Kritis
            'harga_satuan_estimasi' => 55000,
        ]);

        // Search match
        $this->actingAs($this->admin)->get(route('barang.index', ['search' => 'HVS']))
            ->assertStatus(200)
            ->assertSee('Kertas HVS A4')
            ->assertDontSee('Ballpoin Balliner');

        // Filter status kritis
        $this->actingAs($this->admin)->get(route('barang.index', ['status' => 'kritis']))
            ->assertStatus(200)
            ->assertSee('Kertas HVS A4');
    }

    public function test_admin_can_create_and_store_new_barang(): void
    {
        $this->actingAs($this->admin)->get(route('barang.create'))
            ->assertStatus(200)
            ->assertSee('Formulir Tambah Barang Persediaan');

        $response = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => '000099',
            'kode_sakti' => '1010301099',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Spidol Whiteboard Snowman',
            'satuan' => 'buah',
            'stok_minimum' => 10,
            'stok_awal' => 20,
            'harga_satuan_estimasi' => 9500,
        ]);

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barang_persediaan', [
            'kode_barang' => '000099',
            'nama_barang' => 'Spidol Whiteboard Snowman',
            'stok_aktif' => 20,
        ]);
    }

    public function test_non_admin_cannot_create_or_store_barang(): void
    {
        $this->actingAs($this->pegawai)->get(route('barang.create'))->assertStatus(403);

        $this->actingAs($this->pegawai)->post(route('barang.store'), [
            'kode_barang' => '000100',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Barang Ilegal',
            'satuan' => 'buah',
            'stok_minimum' => 5,
        ])->assertStatus(403);
    }

    public function test_katalog_dropdowns_support_instant_automatic_filter_on_change(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('barang.index'));
        $response->assertStatus(200);

        // Verifikasi Alpine.js live filter container dan form bindings
        $response->assertSee('x-data="katalogLiveFilter()"', false);
        $response->assertSee('@submit.prevent="applyFilter()"', false);

        // Verifikasi dropdown kategori memiliki trigger live filter otomatis
        $response->assertSee('name="kategori_id"', false);
        $response->assertSee('@change="applyFilter()"', false);

        // Verifikasi dropdown status dan sort juga memiliki trigger live filter
        $response->assertSee('name="status"', false);
        $response->assertSee('name="sort"', false);

        // Verifikasi container table dan container reset untuk partial AJAX swap
        $response->assertSee('id="katalog-table-container"', false);
        $response->assertSee('id="katalog-reset-container"', false);

        // Verifikasi tombol submit Filter tetap ada di DOM sesuai kebutuhan user
        $response->assertSee('type="submit"', false);
        $response->assertSee('Filter', false);
    }

    public function test_katalog_live_filter_ajax_request_returns_updated_table_content(): void
    {
        BarangPersediaan::create([
            'kode_barang' => '000003',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Map Snelhechter Plastik',
            'satuan' => 'buah',
            'stok_minimum' => 5,
            'stok_aktif' => 25,
            'harga_satuan_estimasi' => 5000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('barang.index', ['search' => 'Map']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertSee('id="katalog-table-container"', false);
        $response->assertSee('Map Snelhechter Plastik');
        $response->assertDontSee('Ballpoin Balliner');
    }

    public function test_admin_can_update_barang_sharing_kode_barang_with_different_kode_sakti(): void
    {
        BarangPersediaan::create([
            'kode_barang' => '000048',
            'kode_sakti' => '1010304004',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Tinta Epson L1445 Yellow',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
        ]);

        $adaptor = BarangPersediaan::create([
            'kode_barang' => '000048',
            'kode_sakti' => '1010304999',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Adaptor Lenovo E440',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
        ]);

        $response = $this->actingAs($this->admin)->put(route('barang.update', $adaptor->id), [
            'kode_barang' => '000048',
            'kode_sakti' => '1010304999',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Adaptor Lenovo E440 Original',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
            'harga_satuan_estimasi' => 0,
            'lokasi_rak' => 'Lemari Persediaan Subbag Umum',
        ]);

        $response->assertRedirect(route('barang.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('barang_persediaan', [
            'id' => $adaptor->id,
            'nama_barang' => 'Adaptor Lenovo E440 Original',
        ]);
    }

    public function test_admin_can_store_barang_with_same_kode_barang_when_kode_sakti_differs(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => '000001',
            'kode_sakti' => '1010304999',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Kabel Power Printer',
            'satuan' => 'Buah',
            'stok_minimum' => 2,
            'stok_awal' => 5,
        ]);

        $response->assertRedirect(route('barang.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('barang_persediaan', [
            'kode_barang' => '000001',
            'kode_sakti' => '1010304999',
            'nama_barang' => 'Kabel Power Printer',
        ]);
    }

    public function test_store_and_update_reject_exact_duplicate_kode_sakti_and_kode_barang_pair(): void
    {
        $other = BarangPersediaan::create([
            'kode_barang' => '000048',
            'kode_sakti' => '1010304004',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Tinta Epson L1445 Yellow',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
        ]);

        // 1. Store duplikat persis (kode_sakti + kode_barang sama) -> ditolak dengan pesan Indonesia
        $storeRes = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => '000048',
            'kode_sakti' => '1010304004',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Duplikat Tinta',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
        ]);

        $storeRes->assertSessionHasErrors([
            'kode_barang' => 'Kombinasi Kode SAKTI dan Kode Barang sudah terdaftar pada barang lain.',
        ]);

        // 2. Update barang lain menabrak pasangan milik $other -> ditolak dengan pesan Indonesia
        $updateRes = $this->actingAs($this->admin)->put(route('barang.update', $this->barang->id), [
            'kode_barang' => '000048',
            'kode_sakti' => '1010304004',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Tabrak Kode Lain',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
        ]);

        $updateRes->assertSessionHasErrors([
            'kode_barang' => 'Kombinasi Kode SAKTI dan Kode Barang sudah terdaftar pada barang lain.',
        ]);
        $this->assertSame('000048', $other->fresh()->kode_barang);
    }

    public function test_null_kode_sakti_only_checks_uniqueness_among_null_kode_sakti_rows(): void
    {
        // Barang tanpa kode_sakti dengan kode_barang '000001' boleh dibuat meski (1010301001, 000001) sudah ada
        $res1 = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => '000001',
            'kode_sakti' => null,
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Barang Non-SAKTI 000001',
            'satuan' => 'Buah',
            'stok_minimum' => 1,
        ]);
        $res1->assertRedirect(route('barang.index'));
        $res1->assertSessionHasNoErrors();

        // Duplikat kedua yang juga tanpa kode_sakti dengan kode_barang sama -> ditolak
        $res2 = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => '000001',
            'kode_sakti' => '',
            'kategori_id' => $this->kategori->id,
            'nama_barang' => 'Duplikat Non-SAKTI 000001',
            'satuan' => 'Buah',
            'stok_minimum' => 1,
        ]);
        $res2->assertSessionHasErrors('kode_barang');
    }
}
