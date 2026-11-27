<?php

namespace App\Services;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Layanan pemrosesan, kompresi otomatis WebP (target 50-100 KB),
 * manajemen subfolder kategori, dan pembersihan berkas fisik foto barang/usulan.
 */
class ImageOptimizationService
{
    private const MAX_DIMENSION = 600;
    private const FALLBACK_DIMENSION = 420;
    private const TARGET_MAX_BYTES = 102400; // 100 KB
    private const QUALITY_STEPS = [80, 70, 58, 45, 35];
    private const ALLOWED_DELETE_PREFIXES = [
        'images/barang/',
        'images/usulan/',
    ];

    /**
     * Simpan & kompres foto barang persediaan ke subfolder kategori terkait.
     * Otomatis menghapus foto lama jika ada penggantian.
     */
    public function simpanFotoBarang(UploadedFile $file, BarangPersediaan $barang, ?string $oldPhotoPath = null): string
    {
        $kategori = $barang->relationLoaded('kategori')
            ? $barang->getRelation('kategori')
            : KategoriBarang::find($barang->kategori_id);

        $slug = $kategori ? Str::slug($kategori->nama_kategori) : 'umum';
        if (empty($slug)) {
            $slug = 'umum';
        }

        $relativeDir = "images/barang/{$slug}";
        $kodeSlug = Str::slug($barang->kode_barang ?: 'barang', '-');
        $filename = "{$kodeSlug}_" . time() . '_' . Str::lower(Str::random(4)) . '.webp';

        return $this->storeOptimizedImage($file, $relativeDir, $filename, $oldPhotoPath);
    }

    /**
     * Simpan & kompres foto lampiran usulan pengadaan barang baru.
     */
    public function simpanFotoUsulan(UploadedFile $file, ?string $oldPhotoPath = null): string
    {
        $relativeDir = 'images/usulan';
        $filename = 'usulan_' . time() . '_' . Str::lower(Str::random(6)) . '.webp';

        return $this->storeOptimizedImage($file, $relativeDir, $filename, $oldPhotoPath);
    }

    /**
     * Hapus berkas fisik foto secara permanen dengan perlindungan agar tidak menghapus aset bawaan.
     */
    public function hapusFoto(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (str_contains($normalized, '..')) {
            return false;
        }

        $isAllowed = false;
        foreach (self::ALLOWED_DELETE_PREFIXES as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return false;
        }

        $fullPath = public_path($normalized);
        if (File::exists($fullPath) && File::isFile($fullPath)) {
            return File::delete($fullPath);
        }

        return false;
    }

    /**
     * Pastikan subfolder kategori tersedia di public/images/barang/{slug}/.
     */
    public function buatFolderKategori(string $namaKategori): string
    {
        $slug = Str::slug($namaKategori) ?: 'umum';
        $relativeDir = "images/barang/{$slug}";
        File::ensureDirectoryExists(public_path($relativeDir));

        return $relativeDir;
    }

    /**
     * Hapus subfolder kategori jika tidak memiliki file foto di dalamnya.
     */
    public function hapusFolderKategoriJikaKosong(string $namaKategori): bool
    {
        $slug = Str::slug($namaKategori) ?: 'umum';
        $fullDir = public_path("images/barang/{$slug}");

        if (File::isDirectory($fullDir) && count(File::allFiles($fullDir)) === 0) {
            return File::deleteDirectory($fullDir);
        }

        return false;
    }

    /**
     * Pindahkan foto barang jika kategori barang diubah pada menu edit barang.
     */
    public function pindahkanFotoKategori(BarangPersediaan $barang, KategoriBarang $kategoriBaru): ?string
    {
        if (empty($barang->foto)) {
            return null;
        }

        $oldRelative = ltrim(str_replace('\\', '/', $barang->foto), '/');
        $oldFullPath = public_path($oldRelative);

        if (!File::exists($oldFullPath) || !File::isFile($oldFullPath)) {
            return $barang->foto;
        }

        $slugBaru = Str::slug($kategoriBaru->nama_kategori) ?: 'umum';
        $targetRelativeDir = "images/barang/{$slugBaru}";

        if (str_starts_with($oldRelative, "{$targetRelativeDir}/")) {
            return $oldRelative;
        }

        File::ensureDirectoryExists(public_path($targetRelativeDir));
        $newRelativePath = "{$targetRelativeDir}/" . basename($oldRelative);
        File::move($oldFullPath, public_path($newRelativePath));

        return $newRelativePath;
    }

    /**
     * Pindahkan isi folder dan perbarui path database saat admin mengubah nama kategori.
     */
    public function pindahkanFolderKategori(KategoriBarang $kategori, string $namaLama): void
    {
        $oldSlug = Str::slug($namaLama) ?: 'umum';
        $newSlug = Str::slug($kategori->nama_kategori) ?: 'umum';

        if ($oldSlug === $newSlug) {
            $this->buatFolderKategori($kategori->nama_kategori);
            return;
        }

        $oldRelativeDir = "images/barang/{$oldSlug}";
        $newRelativeDir = "images/barang/{$newSlug}";
        $oldFullDir = public_path($oldRelativeDir);
        $newFullDir = public_path($newRelativeDir);

        File::ensureDirectoryExists($newFullDir);

        if (File::isDirectory($oldFullDir)) {
            foreach (File::files($oldFullDir) as $file) {
                $targetFile = $newFullDir . DIRECTORY_SEPARATOR . $file->getFilename();
                if (!File::exists($targetFile)) {
                    File::move($file->getPathname(), $targetFile);
                }
            }

            if (count(File::allFiles($oldFullDir)) === 0) {
                File::deleteDirectory($oldFullDir);
            }
        }

        // Perbarui path foto pada seluruh barang dalam kategori ini
        $kategori->barang()
            ->whereNotNull('foto')
            ->where('foto', 'like', "{$oldRelativeDir}/%")
            ->get()
            ->each(function (BarangPersediaan $item) use ($oldRelativeDir, $newRelativeDir) {
                $item->update([
                    'foto' => Str::replaceFirst("{$oldRelativeDir}/", "{$newRelativeDir}/", $item->foto),
                ]);
            });
    }

    /**
     * Alur inti: siapkan folder, proses kompresi WebP, simpan, lalu bersihkan file lama.
     */
    private function storeOptimizedImage(UploadedFile $file, string $relativeDir, string $filename, ?string $oldPhotoPath): string
    {
        $fullDir = public_path($relativeDir);
        File::ensureDirectoryExists($fullDir);

        $fullTargetPath = "{$fullDir}/{$filename}";
        $gdSource = $this->createGdImageWithOrientation($file->getRealPath());

        try {
            $resized = $this->resizeProportionally($gdSource, self::MAX_DIMENSION);
            $this->encodeWebpWithinSizeLimit($resized, $fullTargetPath);
        } finally {
            unset($gdSource, $resized);
        }

        $relativePath = "{$relativeDir}/{$filename}";

        if ($oldPhotoPath && $oldPhotoPath !== $relativePath) {
            $this->hapusFoto($oldPhotoPath);
        }

        return $relativePath;
    }

    /**
     * Baca berkas gambar dari disk ke instance GdImage dan koreksi rotasi EXIF kamera HP.
     */
    private function createGdImageWithOrientation(string $filePath): \GdImage
    {
        $binary = @file_get_contents($filePath);
        if ($binary === false) {
            throw new RuntimeException('Berkas gambar tidak dapat dibaca.');
        }

        $image = @imagecreatefromstring($binary);
        if ($image === false) {
            throw new RuntimeException('Format gambar tidak didukung atau berkas rusak.');
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            $orientation = $exif['Orientation'] ?? null;

            $rotated = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => null,
            };

            if ($rotated instanceof \GdImage) {
                $image = $rotated;
            }
        }

        return $image;
    }

    /**
     * Perkecil dimensi gambar secara proporsional tanpa melebihi batas piksel maksimum.
     */
    private function resizeProportionally(\GdImage $source, int $maxDim): \GdImage
    {
        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        if ($srcWidth <= $maxDim && $srcHeight <= $maxDim) {
            return $source;
        }

        $ratio = min($maxDim / $srcWidth, $maxDim / $srcHeight);
        $newWidth = max(1, (int) round($srcWidth * $ratio));
        $newHeight = max(1, (int) round($srcHeight * $ratio));

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        return $dest;
    }

    /**
     * Kompres gambar menjadi WebP dan pastikan ukurannya berada di bawah batas 100 KB.
     */
    private function encodeWebpWithinSizeLimit(\GdImage $image, string $targetFullPath): void
    {
        if (!function_exists('imagewebp')) {
            throw new RuntimeException('Ekstensi PHP GD WebP tidak aktif pada server.');
        }

        foreach (self::QUALITY_STEPS as $quality) {
            imagewebp($image, $targetFullPath, $quality);
            clearstatcache(true, $targetFullPath);

            if (file_exists($targetFullPath) && filesize($targetFullPath) <= self::TARGET_MAX_BYTES) {
                return;
            }
        }

        // Jika masih > 100 KB karena detail foto sangat padat, perkecil sedikit ke 420px
        $smaller = $this->resizeProportionally($image, self::FALLBACK_DIMENSION);
        foreach ([65, 50, 35] as $quality) {
            imagewebp($smaller, $targetFullPath, $quality);
            clearstatcache(true, $targetFullPath);

            if (file_exists($targetFullPath) && filesize($targetFullPath) <= self::TARGET_MAX_BYTES) {
                return;
            }
        }
    }
}
