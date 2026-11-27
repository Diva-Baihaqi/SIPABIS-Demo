<?php

namespace App\Http\Controllers;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Services\ImageOptimizationService;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;
use RuntimeException;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar katalog persediaan barang pakai habis.
     * Dapat diakses oleh Admin, Pegawai, dan Pimpinan.
     */
    public function index(Request $request): View
    {
        $query = BarangPersediaan::with('kategori');

        // Pencarian Nama / Kode Barang / Kode SAKTI
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kode_sakti', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($kategoriId = $request->input('kategori_id')) {
            $query->where('kategori_id', $kategoriId);
        }

        // Filter Status Ketersediaan Stok
        $status = $request->input('status');
        if ($status === 'kritis') {
            $query->whereColumn('stok_aktif', '<=', 'stok_minimum')
                  ->where('stok_aktif', '>', 0);
        } elseif ($status === 'habis') {
            $query->where('stok_aktif', '<=', 0);
        } elseif ($status === 'aman') {
            $query->whereColumn('stok_aktif', '>', 'stok_minimum');
        }

        // Sorting
        $sort = $request->input('sort', 'nama_asc');
        match ($sort) {
            'nama_desc' => $query->orderBy('nama_barang', 'desc'),
            'stok_asc' => $query->orderBy('stok_aktif', 'asc'),
            'stok_desc' => $query->orderBy('stok_aktif', 'desc'),
            default => $query->orderBy('nama_barang', 'asc'),
        };

        $barangList = $query->paginate(15)->withQueryString();

        // Kartu Metrik Ringkasan untuk Header Katalog
        $totalKatalog = BarangPersediaan::count();
        $totalAman = BarangPersediaan::whereColumn('stok_aktif', '>', 'stok_minimum')->count();
        $totalKritis = BarangPersediaan::whereColumn('stok_aktif', '<=', 'stok_minimum')->where('stok_aktif', '>', 0)->count();
        $totalHabis = BarangPersediaan::where('stok_aktif', '<=', 0)->count();

        $kategoriList = KategoriBarang::orderBy('nama_kategori')->get();

        return view('barang.index', compact(
            'barangList',
            'kategoriList',
            'totalKatalog',
            'totalAman',
            'totalKritis',
            'totalHabis',
            'search',
            'kategoriId',
            'status',
            'sort'
        ));
    }

    /**
     * Form penambahan barang baru (Khusus Admin Subbag Umum).
     */
    public function create(): View
    {
        $kategoriList = KategoriBarang::orderBy('nama_kategori')->get();
        return view('barang.create', compact('kategoriList'));
    }

    /**
     * Simpan barang baru ke katalog persediaan.
     */
    public function store(Request $request, StockMovementService $stockService, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', $this->kodeBarangUniqueRule($request)],
            'kode_sakti' => 'nullable|string|max:50',
            'kategori_id' => 'required|exists:kategori_barang,id',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'stok_awal' => 'nullable|integer|min:0',
            'harga_satuan_estimasi' => 'nullable|numeric|min:0',
            'lokasi_rak' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'kode_barang.unique' => 'Kombinasi Kode SAKTI dan Kode Barang sudah terdaftar pada barang lain.',
            'foto.image' => 'Berkas yang diunggah harus berupa gambar.',
            'foto.mimes' => 'Format foto barang harus berupa JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran awal foto tidak boleh lebih dari 10 MB.',
        ]);

        $stokAwal = (int) ($validated['stok_awal'] ?? 0);
        $savedFotoPath = null;

        try {
            $barang = DB::transaction(function () use ($validated, $request, $stockService, $imageService, $stokAwal, &$savedFotoPath) {
                $barang = BarangPersediaan::create([
                    'kode_barang' => trim($validated['kode_barang']),
                    'kode_sakti' => filled($validated['kode_sakti'] ?? null) ? trim($validated['kode_sakti']) : null,
                    'kategori_id' => $validated['kategori_id'],
                    'nama_barang' => $validated['nama_barang'],
                    'satuan' => $validated['satuan'],
                    'stok_minimum' => $validated['stok_minimum'],
                    'stok_aktif' => 0, // Inisialisasi awal 0, jika ada stok_awal dicatat via StockMovementService
                    'harga_satuan_estimasi' => $validated['harga_satuan_estimasi'] ?? 0,
                    'lokasi_rak' => $validated['lokasi_rak'] ?? null,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                if ($request->hasFile('foto')) {
                    $savedFotoPath = $imageService->simpanFotoBarang($request->file('foto'), $barang);
                    $barang->update(['foto' => $savedFotoPath]);
                }

                if ($stokAwal > 0) {
                    $stockService->tambahStok($barang, $stokAwal, Auth::user(), [
                        'sumber_atau_tujuan' => 'Saldo Awal Katalog Baru',
                        'nomor_dokumen' => 'INIT/' . $barang->kode_barang,
                        'keterangan' => 'Pencatatan saldo awal saat barang didaftarkan',
                    ]);
                }

                return $barang;
            });
        } catch (RuntimeException $e) {
            if ($savedFotoPath) {
                $imageService->hapusFoto($savedFotoPath);
            }
            return back()->withInput()->withErrors(['foto' => $e->getMessage()]);
        }

        return redirect()->route('barang.index')->with('success', "Barang '{$barang->nama_barang}' berhasil ditambahkan ke katalog persediaan.");
    }

    /**
     * Form edit data barang (Khusus Admin Subbag Umum).
     */
    public function edit(BarangPersediaan $barang): View
    {
        $kategoriList = KategoriBarang::orderBy('nama_kategori')->get();
        return view('barang.edit', compact('barang', 'kategoriList'));
    }

    /**
     * Perbarui data master barang.
     */
    public function update(Request $request, BarangPersediaan $barang, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', $this->kodeBarangUniqueRule($request, $barang->id)],
            'kode_sakti' => 'nullable|string|max:50',
            'kategori_id' => 'required|exists:kategori_barang,id',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'harga_satuan_estimasi' => 'nullable|numeric|min:0',
            'lokasi_rak' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'hapus_foto' => 'nullable|boolean',
        ], [
            'kode_barang.unique' => 'Kombinasi Kode SAKTI dan Kode Barang sudah terdaftar pada barang lain.',
            'foto.image' => 'Berkas yang diunggah harus berupa gambar.',
            'foto.mimes' => 'Format foto barang harus berupa JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran awal foto tidak boleh lebih dari 10 MB.',
        ]);

        $oldPhoto = $barang->foto;
        $kategoriBerubah = (int) $barang->kategori_id !== (int) $validated['kategori_id'];
        $fotoPath = $oldPhoto;

        $barang->fill([
            'kode_barang' => trim($validated['kode_barang']),
            'kode_sakti' => filled($validated['kode_sakti'] ?? null) ? trim($validated['kode_sakti']) : null,
            'kategori_id' => $validated['kategori_id'],
            'nama_barang' => $validated['nama_barang'],
            'satuan' => $validated['satuan'],
            'stok_minimum' => $validated['stok_minimum'],
            'harga_satuan_estimasi' => $validated['harga_satuan_estimasi'] ?? 0,
            'lokasi_rak' => $validated['lokasi_rak'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        if ($request->hasFile('foto')) {
            try {
                // Muat ulang relasi kategori agar disimpan pada subfolder kategori tujuan yang terbaru
                $barang->setRelation('kategori', KategoriBarang::find($validated['kategori_id']));
                $fotoPath = $imageService->simpanFotoBarang($request->file('foto'), $barang, $oldPhoto);
            } catch (RuntimeException $e) {
                return back()->withInput()->withErrors(['foto' => $e->getMessage()]);
            }
        } elseif ($request->boolean('hapus_foto')) {
            $imageService->hapusFoto($oldPhoto);
            $fotoPath = null;
        } elseif ($kategoriBerubah && !empty($oldPhoto)) {
            $kategoriBaru = KategoriBarang::find($validated['kategori_id']);
            if ($kategoriBaru) {
                $fotoPath = $imageService->pindahkanFotoKategori($barang, $kategoriBaru);
            }
        }

        $barang->foto = $fotoPath;
        $barang->save();

        return redirect()->route('barang.index')->with('success', "Data barang '{$barang->nama_barang}' berhasil diperbarui.");
    }

    /**
     * Hapus barang dari katalog (Hanya jika belum memiliki riwayat mutasi).
     */
    public function destroy(BarangPersediaan $barang, ImageOptimizationService $imageService): RedirectResponse
    {
        if ($barang->transaksiPersediaan()->exists() || $barang->permintaanDetail()->exists()) {
            return back()->with('error', "Barang '{$barang->nama_barang}' tidak dapat dihapus karena sudah memiliki riwayat mutasi persediaan / permohonan.");
        }

        $nama = $barang->nama_barang;
        $fotoPath = $barang->foto;
        $barang->delete();

        $imageService->hapusFoto($fotoPath);

        return redirect()->route('barang.index')->with('success', "Barang '{$nama}' berhasil dihapus dari katalog.");
    }

    /**
     * Aturan unik gabungan (kode_sakti, kode_barang) sesuai indeks unik di tabel barang_persediaan.
     */
    private function kodeBarangUniqueRule(Request $request, ?int $ignoreId = null): Unique
    {
        $kodeSakti = trim((string) $request->input('kode_sakti', ''));

        $rule = Rule::unique('barang_persediaan', 'kode_barang')
            ->where(function ($query) use ($kodeSakti) {
                if ($kodeSakti === '') {
                    $query->whereNull('kode_sakti');
                } else {
                    $query->where('kode_sakti', $kodeSakti);
                }
            });

        if ($ignoreId !== null) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }
}
