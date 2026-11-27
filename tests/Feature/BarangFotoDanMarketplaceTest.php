<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\UsulanBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BarangFotoDanMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected User $pimpinan;
    protected Ruangan $ruangan;
    protected KategoriBarang $katAtk;
    protected KategoriBarang $katKertas;
    protected array $cleanupDirs = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Kasubbag Umum',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Admin BMN',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai = User::create([
            'nip' => 'pegawai01',
            'name' => 'Pegawai BPS',
            'email' => 'pegawai@bps.go.id',
            'password' => bcrypt('password'),
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

        $this->katAtk = KategoriBarang::create([
            'kode_kategori' => 'ATK-TEST',
            'nama_kategori' => 'Uji ATK',
        ]);

        $this->katKertas = KategoriBarang::create([
            'kode_kategori' => 'KERTAS-TEST',
            'nama_kategori' => 'Uji Kertas',
        ]);

        $this->cleanupDirs = [
            'images/barang/uji-atk',
            'images/barang/uji-kertas',
            'images/usulan',
        ];
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanupDirs as $dir) {
            $full = public_path($dir);
            if (File::isDirectory($full)) {
                if ($dir === 'images/usulan') {
                    foreach (File::files($full) as $f) {
                        File::delete($f->getPathname());
                    }
                    File::deleteDirectory($full);
                } else {
                    File::deleteDirectory($full);
                }
            }
        }
        parent::tearDown();
    }

    private function makeNoisyJpegUploadedFile(string $name = 'kamera.jpg'): UploadedFile
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'bpsfoto') . '.jpg';
        $img = imagecreatetruecolor(1000, 1000);
        for ($y = 0; $y < 1000; $y += 10) {
            for ($x = 0; $x < 1000; $x += 10) {
                $col = imagecolorallocate($img, random_int(0, 255), random_int(0, 255), random_int(0, 255));
                imagefilledrectangle($img, $x, $y, $x + 9, $y + 9, $col);
            }
        }
        imagejpeg($img, $tmpPath, 95);
        unset($img);

        return new UploadedFile($tmpPath, $name, 'image/jpeg', null, true);
    }

    public function test_admin_can_create_barang_with_compressed_photo_in_category_subfolder(): void
    {
        $file = $this->makeNoisyJpegUploadedFile('spidol_kamera.jpg');

        $response = $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'SPIDOL-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Spidol Whiteboard Snowman Hitam',
            'satuan' => 'buah',
            'stok_minimum' => 5,
            'stok_awal' => 20,
            'foto' => $file,
        ]);

        $response->assertRedirect(route('barang.index'));
        $barang = BarangPersediaan::where('kode_barang', 'SPIDOL-01')->firstOrFail();

        $this->assertNotNull($barang->foto);
        $this->assertStringStartsWith('images/barang/uji-atk/', $barang->foto);
        $this->assertStringEndsWith('.webp', $barang->foto);
        $this->assertFileExists(public_path($barang->foto));
        $this->assertLessThanOrEqual(102400, filesize(public_path($barang->foto)));
        $this->assertTrue($barang->has_real_foto);
    }

    public function test_admin_can_replace_photo_and_old_file_is_deleted_permanently(): void
    {
        $file1 = UploadedFile::fake()->image('lama.jpg', 700, 700);
        $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'MAP-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Map Snelhecter Biru',
            'satuan' => 'buah',
            'stok_minimum' => 2,
            'foto' => $file1,
        ]);

        $barang = BarangPersediaan::where('kode_barang', 'MAP-01')->firstOrFail();
        $oldFoto = $barang->foto;
        $this->assertFileExists(public_path($oldFoto));

        // Ganti foto via update
        $file2 = UploadedFile::fake()->image('baru.png', 800, 800);
        $response = $this->actingAs($this->admin)->put(route('barang.update', $barang->id), [
            'kode_barang' => 'MAP-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Map Snelhecter Biru',
            'satuan' => 'buah',
            'stok_minimum' => 2,
            'foto' => $file2,
        ]);

        $response->assertRedirect(route('barang.index'));
        $barang->refresh();

        $this->assertNotSame($oldFoto, $barang->foto);
        $this->assertFileDoesNotExist(public_path($oldFoto));
        $this->assertFileExists(public_path($barang->foto));
    }

    public function test_admin_can_remove_photo_permanently_without_deleting_barang(): void
    {
        $file = UploadedFile::fake()->image('penghapus.jpg', 500, 500);
        $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'HPS-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Penghapus Karet Steadler',
            'satuan' => 'buah',
            'stok_minimum' => 3,
            'foto' => $file,
        ]);

        $barang = BarangPersediaan::where('kode_barang', 'HPS-01')->firstOrFail();
        $fotoPath = $barang->foto;
        $this->assertFileExists(public_path($fotoPath));

        $response = $this->actingAs($this->admin)->put(route('barang.update', $barang->id), [
            'kode_barang' => 'HPS-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Penghapus Karet Steadler',
            'satuan' => 'buah',
            'stok_minimum' => 3,
            'hapus_foto' => '1',
        ]);

        $response->assertRedirect(route('barang.index'));
        $barang->refresh();

        $this->assertNull($barang->foto);
        $this->assertFalse($barang->has_real_foto);
        $this->assertFileDoesNotExist(public_path($fotoPath));
        $this->assertStringContainsString('images/placeholders/', $barang->foto_url);
    }

    public function test_moving_barang_category_moves_physical_photo_subfolder(): void
    {
        $file = UploadedFile::fake()->image('kertas.jpg', 500, 500);
        $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'KRT-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Kertas HVS A4',
            'satuan' => 'rim',
            'stok_minimum' => 5,
            'foto' => $file,
        ]);

        $barang = BarangPersediaan::where('kode_barang', 'KRT-01')->firstOrFail();
        $oldPath = $barang->foto;
        $this->assertStringStartsWith('images/barang/uji-atk/', $oldPath);

        // Pindahkan kategori ke Uji Kertas tanpa mengganti foto
        $this->actingAs($this->admin)->put(route('barang.update', $barang->id), [
            'kode_barang' => 'KRT-01',
            'kategori_id' => $this->katKertas->id,
            'nama_barang' => 'Kertas HVS A4',
            'satuan' => 'rim',
            'stok_minimum' => 5,
        ]);

        $barang->refresh();
        $this->assertStringStartsWith('images/barang/uji-kertas/', $barang->foto);
        $this->assertFileDoesNotExist(public_path($oldPath));
        $this->assertFileExists(public_path($barang->foto));
    }

    public function test_deleting_barang_removes_physical_photo_from_disk(): void
    {
        $file = UploadedFile::fake()->image('gunting.jpg', 400, 400);
        $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'GNT-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Gunting Joyko Sedang',
            'satuan' => 'buah',
            'stok_minimum' => 1,
            'foto' => $file,
        ]);

        $barang = BarangPersediaan::where('kode_barang', 'GNT-01')->firstOrFail();
        $fotoPath = $barang->foto;
        $this->assertFileExists(public_path($fotoPath));

        $this->actingAs($this->admin)->delete(route('barang.destroy', $barang->id))
            ->assertRedirect(route('barang.index'));

        $this->assertDatabaseMissing('barang_persediaan', ['id' => $barang->id]);
        $this->assertFileDoesNotExist(public_path($fotoPath));
    }

    public function test_pegawai_and_pimpinan_can_submit_usulan_with_compressed_photo_and_cancel_deletes_it(): void
    {
        $file = $this->makeNoisyJpegUploadedFile('usulan_kamera.jpg');

        $this->actingAs($this->pimpinan)->post(route('usulan-barang.store'), [
            'nama_barang_usulan' => 'Printer Thermal Label',
            'perkiraan_satuan' => 'unit',
            'estimasi_kebutuhan' => 2,
            'alasan_kebutuhan' => 'Untuk label rak gudang BPS',
            'foto' => $file,
        ])->assertRedirect(route('usulan-barang.index'));

        $usulan = UsulanBarang::where('nama_barang_usulan', 'Printer Thermal Label')->firstOrFail();
        $this->assertNotNull($usulan->foto);
        $this->assertStringStartsWith('images/usulan/', $usulan->foto);
        $this->assertStringEndsWith('.webp', $usulan->foto);
        $this->assertFileExists(public_path($usulan->foto));
        $this->assertLessThanOrEqual(102400, filesize(public_path($usulan->foto)));

        $savedPath = $usulan->foto;

        // Pegawai mengajukan lalu membatalkan -> foto ikut terhapus
        $file2 = UploadedFile::fake()->image('usulan_pegawai.png', 500, 500);
        $this->actingAs($this->pegawai)->post(route('usulan-barang.store'), [
            'nama_barang_usulan' => 'Lakban Bening Lebar',
            'perkiraan_satuan' => 'roll',
            'estimasi_kebutuhan' => 10,
            'alasan_kebutuhan' => 'Packing dokumen sensus',
            'foto' => $file2,
        ])->assertRedirect(route('usulan-barang.index'));

        $usulanPegawai = UsulanBarang::where('nama_barang_usulan', 'Lakban Bening Lebar')->firstOrFail();
        $pegawaiFotoPath = $usulanPegawai->foto;
        $this->assertFileExists(public_path($pegawaiFotoPath));

        $this->actingAs($this->pegawai)->delete(route('usulan-barang.destroy', $usulanPegawai->id))
            ->assertRedirect(route('usulan-barang.index'));

        $this->assertFileDoesNotExist(public_path($pegawaiFotoPath));
        $this->assertFileExists(public_path($savedPath));
    }

    public function test_homepage_and_katalog_render_marketplace_photo_cards(): void
    {
        $file = UploadedFile::fake()->image('stabilo.jpg', 500, 500);
        $this->actingAs($this->admin)->post(route('barang.store'), [
            'kode_barang' => 'STB-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Stabilo Boss Kuning',
            'satuan' => 'buah',
            'stok_minimum' => 5,
            'stok_awal' => 15,
            'foto' => $file,
        ]);

        BarangPersediaan::create([
            'kode_barang' => 'NOFOTO-01',
            'kategori_id' => $this->katKertas->id,
            'nama_barang' => 'Kertas Buram Tanpa Foto',
            'satuan' => 'rim',
            'stok_minimum' => 2,
            'stok_aktif' => 10,
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('selectedProduct', false);
        $homeResponse->assertSee('has_real_foto', false);
        $homeResponse->assertSee('Stabilo Boss Kuning', false);
        $homeResponse->assertSee('uji-atk', false);

        $barangContoh = $homeResponse->viewData('barangContoh');
        $stabilo = $barangContoh->firstWhere('kode_barang', 'STB-01');
        $this->assertNotNull($stabilo);
        $this->assertTrue($stabilo->has_real_foto);
        $this->assertStringContainsString('images/barang/uji-atk/', $stabilo->foto_url);

        $nofoto = $barangContoh->firstWhere('kode_barang', 'NOFOTO-01');
        $this->assertNotNull($nofoto);
        $this->assertFalse($nofoto->has_real_foto);
        $this->assertStringContainsString('images/placeholders/', $nofoto->foto_url);

        $katalogResponse = $this->actingAs($this->admin)->get(route('barang.index'));
        $katalogResponse->assertStatus(200);
        $katalogResponse->assertSee('data-foto-preview', false);
        $katalogResponse->assertSee('Stabilo Boss Kuning');
    }

    public function test_all_foto_uploader_forms_support_drag_drop_and_clipboard_paste(): void
    {
        $barang = BarangPersediaan::create([
            'kode_barang' => 'DND-01',
            'kategori_id' => $this->katAtk->id,
            'nama_barang' => 'Adaptor Uji Drag Drop',
            'satuan' => 'buah',
            'stok_minimum' => 2,
            'stok_aktif' => 10,
        ]);

        $pages = [
            $this->actingAs($this->admin)->get(route('barang.edit', $barang->id)),
            $this->actingAs($this->admin)->get(route('barang.create')),
            $this->actingAs($this->pegawai)->get(route('usulan-barang.create')),
        ];

        foreach ($pages as $response) {
            $response->assertStatus(200);
            $response->assertSee('handleDrop($event)', false);
            $response->assertSee('handleDragEnter($event)', false);
            $response->assertSee('@paste.window="handleWindowPaste($event)"', false);
            $response->assertSee('pasteFromClipboard()', false);
            $response->assertSee('Tempel', false);
            $response->assertSee('Ctrl+V', false);
            $response->assertSee('Lepaskan gambar di sini', false);
            $response->assertSee('uploadError', false);
        }
    }
}
