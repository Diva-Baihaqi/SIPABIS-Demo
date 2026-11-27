<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangPersediaan;
use App\Models\TransaksiPersediaan;
use App\Services\SpreadsheetExportService;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BarangMasukController extends Controller
{
    public static array $daftarBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public function __construct(
        protected StockMovementService $stockService
    ) {}

    /**
     * Helper query untuk filter pencarian, status SPJ, bulan, dan tahun.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = TransaksiPersediaan::where('jenis_transaksi', 'masuk')
            ->with(['barang.kategori', 'user'])
            ->latest('tanggal_transaksi')
            ->latest('id');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_transaksi', (int) $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', (int) $request->bulan);
        }

        if ($request->filled('status_spj')) {
            $query->where('status_spj', $request->status_spj);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_dokumen', 'like', "%{$search}%")
                  ->orWhere('sumber_atau_tujuan', 'like', "%{$search}%")
                  ->orWhereHas('barang', function ($b) use ($search) {
                      $b->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Helper membuat ringkasan teks filter untuk cetak dan ekspor.
     */
    protected function getFilterDescription(Request $request): string
    {
        $parts = [];

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $namaBulan = self::$daftarBulan[(int) $request->bulan] ?? ('Bulan ' . $request->bulan);
            $parts[] = "Periode: {$namaBulan} {$request->tahun}";
        } elseif ($request->filled('tahun')) {
            $parts[] = "Tahun Anggaran: {$request->tahun} (Seluruh Bulan)";
        } elseif ($request->filled('bulan')) {
            $namaBulan = self::$daftarBulan[(int) $request->bulan] ?? ('Bulan ' . $request->bulan);
            $parts[] = "Bulan: {$namaBulan} (Seluruh Tahun)";
        } else {
            $parts[] = "Seluruh Riwayat Penerimaan";
        }

        if ($request->filled('status_spj')) {
            $parts[] = $request->status_spj === 'selesai' ? 'Status: SPJ Lunas' : 'Status: SPJ Tertunda';
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return implode(' • ', $parts);
    }

    /**
     * Menampilkan daftar riwayat barang masuk / pengadaan persediaan.
     */
    public function index(Request $request): View
    {
        $query = $this->getFilteredQuery($request);

        $transaksiList = $query->paginate(15)->withQueryString();
        
        $totalMasukBulanIni = TransaksiPersediaan::where('jenis_transaksi', 'masuk')
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('jumlah');

        $totalSpjTertunda = TransaksiPersediaan::where('jenis_transaksi', 'masuk')
            ->where('status_spj', 'tertunda')
            ->count();

        // Ambil daftar tahun unik dari transaksi barang masuk
        $availableYears = TransaksiPersediaan::where('jenis_transaksi', 'masuk')
            ->selectRaw('YEAR(tanggal_transaksi) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values()
            ->toArray();

        $currentYear = (int) now()->year;
        if (!in_array($currentYear, $availableYears)) {
            array_unshift($availableYears, $currentYear);
        }

        $daftarBulan = self::$daftarBulan;

        return view('admin.barang-masuk.index', compact(
            'transaksiList', 
            'totalMasukBulanIni', 
            'totalSpjTertunda',
            'availableYears',
            'daftarBulan'
        ));
    }

    /**
     * Ekspor Buku Register Penerimaan Barang Persediaan ke Excel (.xlsx).
     */
    public function exportExcel(Request $request, SpreadsheetExportService $exportService): StreamedResponse
    {
        $transaksiList = $this->getFilteredQuery($request)->get();
        $filterDesc = $this->getFilterDescription($request);
        $tahunAnggaran = $request->filled('tahun') ? (int) $request->tahun : (int) date('Y');

        return $exportService->exportRegisterBarangMasuk($transaksiList, [
            'deskripsi' => $filterDesc,
            'tahun' => $tahunAnggaran,
        ]);
    }

    /**
     * Cetak Laporan Register Penerimaan Barang Persediaan (PDF / Print View).
     */
    public function cetak(Request $request): View
    {
        $transaksiList = $this->getFilteredQuery($request)->get();
        $filterDesc = $this->getFilterDescription($request);
        $totalUnit = $transaksiList->sum('jumlah');

        $periodeTitle = 'SELURUH PERIODE';
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $namaBulan = self::$daftarBulan[(int) $request->bulan] ?? '';
            $periodeTitle = strtoupper("{$namaBulan} {$request->tahun}");
        } elseif ($request->filled('tahun')) {
            $periodeTitle = "TAHUN ANGGARAN {$request->tahun}";
        } elseif ($request->filled('bulan')) {
            $namaBulan = self::$daftarBulan[(int) $request->bulan] ?? '';
            $periodeTitle = strtoupper("BULAN {$namaBulan}");
        }

        return view('admin.barang-masuk.cetak', compact(
            'transaksiList', 
            'filterDesc', 
            'totalUnit',
            'periodeTitle'
        ));
    }

    /**
     * Menampilkan form input penerimaan barang masuk.
     */
    public function create(): View
    {
        $barangs = BarangPersediaan::with('kategori')
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('admin.barang-masuk.create', compact('barangs'));
    }

    /**
     * Menyimpan transaksi barang masuk (multi-item) dan memperbarui saldo stok.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'nomor_dokumen' => 'nullable|string|max:100',
            'sumber_atau_tujuan' => 'nullable|string|max:150',
            'status_spj' => 'required|in:selesai,tertunda',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang_persediaan,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ], [
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'items.required' => 'Minimal satu barang harus ditambahkan.',
            'items.*.barang_id.required' => 'Pilih barang yang diterima.',
            'items.*.jumlah.min' => 'Jumlah barang masuk harus minimal 1.',
        ]);

        $user = Auth::user();

        foreach ($validated['items'] as $item) {
            $barang = BarangPersediaan::findOrFail($item['barang_id']);

            $this->stockService->tambahStok(
                $barang,
                (int) $item['jumlah'],
                $user,
                [
                    'tanggal_transaksi' => $validated['tanggal_transaksi'],
                    'nomor_dokumen' => $validated['nomor_dokumen'] ?? null,
                    'sumber_atau_tujuan' => $validated['sumber_atau_tujuan'] ?? 'Vendor / Pengadaan DIPA',
                    'status_spj' => $validated['status_spj'],
                    'keterangan' => $validated['keterangan'] ?? 'Penerimaan barang persediaan',
                ]
            );
        }

        $pesan = count($validated['items']) . ' barang berhasil dicatat ke gudang persediaan.';
        if ($validated['status_spj'] === 'tertunda') {
            $pesan .= ' Ditandai sebagai Barang Langganan (SPJ Tertunda).';
        }

        return redirect()->route('admin.barang-masuk.index')->with('success', $pesan);
    }

    /**
     * Memperbarui status SPJ transaksi barang langganan menjadi selesai.
     */
    public function updateSpj(int $id): RedirectResponse
    {
        $transaksi = TransaksiPersediaan::where('jenis_transaksi', 'masuk')->findOrFail($id);

        $transaksi->update([
            'status_spj' => 'selesai',
        ]);

        return redirect()->back()->with('success', "Status SPJ untuk dokumen {$transaksi->nomor_dokumen} berhasil diperbarui menjadi Selesai.");
    }

    /**
     * Memperbarui / mengoreksi data transaksi penerimaan barang dan menyesuaikan stok master.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $transaksi = TransaksiPersediaan::where('jenis_transaksi', 'masuk')->findOrFail($id);

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_transaksi' => 'required|date',
            'sumber_atau_tujuan' => 'nullable|string|max:150',
            'status_spj' => 'required|in:selesai,tertunda',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'jumlah.required' => 'Jumlah barang masuk wajib diisi.',
            'jumlah.min' => 'Jumlah barang masuk minimal 1.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'status_spj.required' => 'Status SPJ wajib dipilih.',
        ]);

        try {
            $this->stockService->updateTransaksiMasuk(
                $transaksi,
                (int) $validated['jumlah'],
                [
                    'nomor_dokumen' => $validated['nomor_dokumen'] ?? null,
                    'tanggal_transaksi' => $validated['tanggal_transaksi'],
                    'sumber_atau_tujuan' => $validated['sumber_atau_tujuan'] ?? null,
                    'status_spj' => $validated['status_spj'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]
            );

            return redirect()->back()->with('success', "Transaksi penerimaan dokumen '{$transaksi->nomor_dokumen}' berhasil diperbarui dan saldo stok disinkronkan.");
        } catch (\App\Exceptions\InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Membatalkan / menghapus transaksi barang masuk dan mengembalikan saldo stok.
     */
    public function destroy(int $id): RedirectResponse
    {
        $transaksi = TransaksiPersediaan::where('jenis_transaksi', 'masuk')->findOrFail($id);
        $noDok = $transaksi->nomor_dokumen ?? '—';
        $namaBarang = $transaksi->barang->nama_barang ?? 'Barang';
        $jumlah = $transaksi->jumlah;

        try {
            $this->stockService->hapusTransaksiMasuk($transaksi);

            return redirect()->back()->with('success', "Transaksi penerimaan '{$noDok}' ({$namaBarang} sebanyak {$jumlah} unit) berhasil dibatalkan dan saldo stok telah ditarik kembali.");
        } catch (\App\Exceptions\InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
