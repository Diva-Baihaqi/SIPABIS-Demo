<?php

namespace Tests\Unit;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Services\ImageOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImageOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImageOptimizationService $service;
    protected array $createdPaths = [];
    protected array $createdDirs = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImageOptimizationService();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdPaths as $path) {
            $full = public_path($path);
            if (File::exists($full)) {
                File::delete($full);
            }
        }
        foreach ($this->createdDirs as $dir) {
            $full = public_path($dir);
            if (File::isDirectory($full) && count(File::allFiles($full)) === 0) {
                File::deleteDirectory($full);
            }
        }
        parent::tearDown();
    }

    public function test_simpan_foto_barang_compresses_to_webp_under_100kb_in_category_subfolder(): void
    {
        $kategori = KategoriBarang::create([
            'kode_kategori' => 'ATK-TEST',
            'nama_kategori' => 'Alat Tulis Uji Coba',
        ]);

        $barang = BarangPersediaan::create([
            'kode_barang' => 'TEST-001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Pulpen Uji',
            'satuan' => 'Buah',
            'stok_minimum' => 5,
            'stok_aktif' => 10,
        ]);

        // Buat gambar acak berukuran besar (1200x1200 px) dengan banyak warna/noise untuk menguji kompresi nyata
        $tmpFile = tempnam(sys_get_temp_dir(), 'testimg') . '.jpg';
        $img = imagecreatetruecolor(1200, 1200);
        for ($y = 0; $y < 1200; $y += 10) {
            for ($x = 0; $x < 1200; $x += 10) {
                $col = imagecolorallocate($img, random_int(0, 255), random_int(0, 255), random_int(0, 255));
                imagefilledrectangle($img, $x, $y, $x + 9, $y + 9, $col);
            }
        }
        imagejpeg($img, $tmpFile, 95);
        unset($img);

        $uploadedFile = new UploadedFile($tmpFile, 'photo_hp.jpg', 'image/jpeg', null, true);

        $relativePath = $this->service->simpanFotoBarang($uploadedFile, $barang);
        $this->createdPaths[] = $relativePath;
        $this->createdDirs[] = 'images/barang/alat-tulis-uji-coba';

        $this->assertStringStartsWith('images/barang/alat-tulis-uji-coba/', $relativePath);
        $this->assertStringEndsWith('.webp', $relativePath);
        $this->assertFileExists(public_path($relativePath));
        $this->assertLessThanOrEqual(102400, filesize(public_path($relativePath)));

        // Pastikan dimensi maks 600px
        [$width, $height] = getimagesize(public_path($relativePath));
        $this->assertLessThanOrEqual(600, $width);
        $this->assertLessThanOrEqual(600, $height);

        @unlink($tmpFile);
    }

    public function test_replacing_and_deleting_foto_removes_old_file_permanently(): void
    {
        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-HAPUS',
            'nama_kategori' => 'Kategori Hapus Uji',
        ]);

        $barang = BarangPersediaan::create([
            'kode_barang' => 'DEL-001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kertas Uji',
            'satuan' => 'Rim',
            'stok_minimum' => 1,
            'stok_aktif' => 5,
        ]);

        $file1 = UploadedFile::fake()->image('foto1.jpg', 800, 800);
        $path1 = $this->service->simpanFotoBarang($file1, $barang);
        $this->createdPaths[] = $path1;
        $this->createdDirs[] = 'images/barang/kategori-hapus-uji';
        $this->assertFileExists(public_path($path1));

        // Ganti dengan foto baru -> foto lama wajib terhapus fisik
        $file2 = UploadedFile::fake()->image('foto2.png', 700, 700);
        $path2 = $this->service->simpanFotoBarang($file2, $barang, $path1);
        $this->createdPaths[] = $path2;

        $this->assertFileDoesNotExist(public_path($path1));
        $this->assertFileExists(public_path($path2));

        // Hapus foto -> path2 terhapus fisik
        $deleted = $this->service->hapusFoto($path2);
        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist(public_path($path2));

        // Perlindungan aset sistem: tidak boleh menghapus logo atau placeholder
        $this->assertFalse($this->service->hapusFoto('images/logo.png'));
        $this->assertFileExists(public_path('images/logo.png'));
    }
}
