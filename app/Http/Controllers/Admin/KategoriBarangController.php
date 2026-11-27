<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use App\Services\ImageOptimizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategoriBarangController extends Controller
{
    /**
     * Tampilkan daftar seluruh kategori persediaan barang BPS.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = KategoriBarang::withCount('barang');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('kode_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $kategoriList = $query->orderBy('nama_kategori', 'asc')->get();

        // Kartu Statistik Metrik Kategori
        $totalKategori = KategoriBarang::count();
        $kategoriTerisi = KategoriBarang::has('barang')->count();
        $kategoriKosong = KategoriBarang::doesntHave('barang')->count();

        return view('admin.kategori.index', compact(
            'kategoriList',
            'totalKategori',
            'kategoriTerisi',
            'kategoriKosong',
            'search'
        ));
    }

    /**
     * Simpan kategori persediaan baru.
     */
    public function store(Request $request, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'kode_kategori' => 'nullable|string|max:50|unique:kategori_barang,kode_kategori',
            'nama_kategori' => 'required|string|max:150|unique:kategori_barang,nama_kategori',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama_kategori.required' => 'Nama kategori barang wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah terdaftar sebelumnya.',
            'kode_kategori.unique' => 'Kode kategori ini sudah digunakan oleh kategori lain.',
        ]);

        // Jika kode_kategori dikosongkan, buat otomatis dari nama kategori
        $kodeKategori = $validated['kode_kategori'] ?? null;
        if (empty($kodeKategori)) {
            $baseCode = Str::upper(Str::slug(Str::limit($validated['nama_kategori'], 15, ''), ''));
            $kodeKategori = $baseCode;
            $counter = 1;
            while (KategoriBarang::where('kode_kategori', $kodeKategori)->exists()) {
                $kodeKategori = $baseCode . '_' . $counter;
                $counter++;
            }
        } else {
            $kodeKategori = Str::upper(str_replace(' ', '_', trim($kodeKategori)));
        }

        $kategori = KategoriBarang::create([
            'kode_kategori' => $kodeKategori,
            'nama_kategori' => trim($validated['nama_kategori']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        // Otomatis buatkan subfolder kategori di public/images/barang/{slug}/
        $imageService->buatFolderKategori($kategori->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', "Kategori '{$kategori->nama_kategori}' ({$kategori->kode_kategori}) berhasil ditambahkan.");
    }

    /**
     * Perbarui data kategori persediaan.
     */
    public function update(Request $request, KategoriBarang $kategori, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'kode_kategori' => "required|string|max:50|unique:kategori_barang,kode_kategori,{$kategori->id}",
            'nama_kategori' => "required|string|max:150|unique:kategori_barang,nama_kategori,{$kategori->id}",
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama_kategori.required' => 'Nama kategori barang wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah digunakan oleh kategori lain.',
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori ini sudah digunakan oleh kategori lain.',
        ]);

        $namaLama = $kategori->nama_kategori;
        $kodeKategori = Str::upper(str_replace(' ', '_', trim($validated['kode_kategori'])));

        $kategori->update([
            'kode_kategori' => $kodeKategori,
            'nama_kategori' => trim($validated['nama_kategori']),
            'deskripsi' => !empty($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
        ]);

        $imageService->pindahkanFolderKategori($kategori, $namaLama);

        return redirect()->route('admin.kategori.index')->with('success', "Kategori '{$kategori->nama_kategori}' berhasil diperbarui.");
    }

    /**
     * Hapus kategori persediaan (Hanya jika belum memiliki barang).
     */
    public function destroy(KategoriBarang $kategori, ImageOptimizationService $imageService): RedirectResponse
    {
        if ($kategori->barang()->exists()) {
            $count = $kategori->barang()->count();
            return back()->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$count} barang persediaan. Silakan pindahkan barang ke kategori lain terlebih dahulu.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();
        $imageService->hapusFolderKategoriJikaKosong($nama);

        return redirect()->route('admin.kategori.index')->with('success', "Kategori '{$nama}' berhasil dihapus dari sistem.");
    }
}
