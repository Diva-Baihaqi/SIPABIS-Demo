<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class KategoriBarangTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected User $pimpinan;
    protected Ruangan $ruangan;

    protected function tearDown(): void
    {
        foreach ([
            'images/barang/peralatan-rumah-tangga-kantor',
            'images/barang/perlengkapan-lapangan-sensus',
            'images/barang/perlengkapan-it-jaringan',
            'images/barang/kebersihan-sanitasi',
        ] as $testDir) {
            $full = public_path($testDir);
            if (File::isDirectory($full) && count(File::allFiles($full)) === 0) {
                File::deleteDirectory($full);
            }
        }
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
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
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai = User::create([
            'nip' => 'user',
            'name' => 'Pegawai Tim Kerja',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pimpinan = User::create([
            'nip' => 'pimpinan',
            'name' => 'Kepala BPS',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan->id,
        ]);
    }

    public function test_admin_can_view_kategori_index_with_item_counts(): void
    {
        $kat1 = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Alat tulis kantor harian',
        ]);

        $kat2 = KategoriBarang::create([
            'kode_kategori' => 'KOSONG',
            'nama_kategori' => 'Kategori Belum Berisi',
            'deskripsi' => 'Belum ada barang di sini',
        ]);

        BarangPersediaan::create([
            'kode_barang' => '000001',
            'kategori_id' => $kat1->id,
            'nama_barang' => 'Pulpen Hitam Standard',
            'satuan' => 'buah',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kategori.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Kategori Persediaan');
        $response->assertSee('Alat Tulis Kantor');
        $response->assertSee('1 Barang');
        $response->assertSee('Kategori Belum Berisi');
        $response->assertSee('0 Barang (Kosong)');
    }

    public function test_admin_can_create_new_kategori(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Peralatan Rumah Tangga Kantor',
            'kode_kategori' => 'RT_KANTOR',
            'deskripsi' => 'Sapu, kain pel, dispenser, dan kebutuhan rumah tangga kantor',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori_barang', [
            'nama_kategori' => 'Peralatan Rumah Tangga Kantor',
            'kode_kategori' => 'RT_KANTOR',
        ]);
    }

    public function test_kategori_creation_generates_code_if_left_empty(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Perlengkapan Lapangan Sensus',
            'kode_kategori' => '',
            'deskripsi' => 'Rompi dan tas sensus lapangan',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));

        $kategori = KategoriBarang::where('nama_kategori', 'Perlengkapan Lapangan Sensus')->first();
        $this->assertNotNull($kategori);
        $this->assertNotEmpty($kategori->kode_kategori);
        $this->assertSame(strtoupper($kategori->kode_kategori), $kategori->kode_kategori);
    }

    public function test_kategori_validation_prevents_duplicate_names(): void
    {
        KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Alat Tulis Kantor',
            'kode_kategori' => 'ATK2',
        ]);

        $response->assertSessionHasErrors(['nama_kategori']);
    }

    public function test_admin_can_update_kategori(): void
    {
        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KOMP',
            'nama_kategori' => 'Komputer & IT',
            'deskripsi' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kategori.update', $kategori->id), [
            'nama_kategori' => 'Perlengkapan IT & Jaringan',
            'kode_kategori' => 'IT_JARINGAN',
            'deskripsi' => 'Flashdisk, kabel LAN, dan perangkat IT',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori_barang', [
            'id' => $kategori->id,
            'nama_kategori' => 'Perlengkapan IT & Jaringan',
            'kode_kategori' => 'IT_JARINGAN',
        ]);
    }

    public function test_admin_cannot_delete_kategori_with_existing_items(): void
    {
        $kategori = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        BarangPersediaan::create([
            'kode_barang' => '000001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Buku Ekspedisi',
            'satuan' => 'buku',
            'stok_minimum' => 2,
            'stok_aktif' => 5,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kategori_barang', [
            'id' => $kategori->id,
        ]);
    }

    public function test_admin_can_delete_empty_kategori(): void
    {
        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KOSONG',
            'nama_kategori' => 'Kategori Mau Dihapus',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori->id));

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kategori_barang', [
            'id' => $kategori->id,
        ]);
    }

    public function test_non_admin_cannot_access_or_manage_kategori(): void
    {
        // 1. Pegawai
        $this->actingAs($this->pegawai)->get(route('admin.kategori.index'))->assertStatus(403);
        $this->actingAs($this->pegawai)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Tes Ilegal',
        ])->assertStatus(403);

        // 2. Pimpinan
        $this->actingAs($this->pimpinan)->get(route('admin.kategori.index'))->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_kategori(): void
    {
        $this->get(route('admin.kategori.index'))->assertRedirect(route('login'));
    }

    public function test_newly_created_kategori_appears_in_katalog_and_allows_moving_items(): void
    {
        // 1. Kategori lama
        $katLama = KategoriBarang::create([
            'kode_kategori' => 'UMUM',
            'nama_kategori' => 'Barang Umum',
        ]);

        // 2. Buat barang di kategori lama
        $barang = BarangPersediaan::create([
            'kode_barang' => '000099',
            'kategori_id' => $katLama->id,
            'nama_barang' => 'Sapu Ijuk Ruangan',
            'satuan' => 'buah',
            'stok_minimum' => 2,
            'stok_aktif' => 5,
        ]);

        // 3. Admin membuat kategori baru
        $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Kebersihan & Sanitasi',
            'kode_kategori' => 'BERSIH',
        ]);

        $katBaru = KategoriBarang::where('kode_kategori', 'BERSIH')->first();
        $this->assertNotNull($katBaru);

        // 4. Periksa dropdown di Katalog Persediaan
        $responseKatalog = $this->actingAs($this->admin)->get(route('barang.index'));
        $responseKatalog->assertSee('Kebersihan & Sanitasi');

        // 5. Periksa dropdown di Form Tambah Barang
        $responseCreate = $this->actingAs($this->admin)->get(route('barang.create'));
        $responseCreate->assertSee('Kebersihan & Sanitasi');

        // 6. Pindahkan barang lama ke kategori baru via update barang
        $responseUpdate = $this->actingAs($this->admin)->put(route('barang.update', $barang->id), [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'satuan' => $barang->satuan,
            'stok_minimum' => $barang->stok_minimum,
            'kategori_id' => $katBaru->id, // Pindah ke kategori baru!
        ]);

        $responseUpdate->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barang_persediaan', [
            'id' => $barang->id,
            'kategori_id' => $katBaru->id,
        ]);
    }
}
