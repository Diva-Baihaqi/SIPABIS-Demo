<?php

namespace App\Http\Controllers;

use App\Models\BarangPersediaan;
use App\Models\Permintaan;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Services\StockOpnameService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StockOpnameController extends Controller
{
    public function __construct(
        protected StockOpnameService $service
    ) {}

    /**
     * Menampilkan daftar riwayat sesi Stock Opname persediaan BPS.
     */
    public function index(Request $request): View
    {
        $query = StokOpname::with(['user', 'pimpinan', 'details'])
            ->latest('tanggal_opname')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opnameList = $query->paginate(10)->withQueryString();

        $activeDraft = StokOpname::where('status', 'draft')->first();
        $totalFinal = StokOpname::where('status', 'final')->count();
        $totalDisahkan = StokOpname::where('status', 'disahkan')->count();

        return view('stok-opname.index', compact(
            'opnameList',
            'activeDraft',
            'totalFinal',
            'totalDisahkan'
        ));
    }

    /**
     * Menampilkan form pembukaan sesi Stock Opname baru.
     */
    public function create(): View
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Administrator Subbag Umum yang dapat membuka sesi Stock Opname.');
        }

        $now = Carbon::now();
        $defaultPeriode = $now->translatedFormat('F Y');
        $activeDraft = StokOpname::where('status', 'draft')->first();

        // Kesiapan Audit Gudang (Audit Readiness Metrics)
        $totalBarang = BarangPersediaan::count();
        $totalStokUnit = (int) BarangPersediaan::sum('stok_aktif');
        $totalNilaiBuku = (float) (BarangPersediaan::selectRaw('SUM(stok_aktif * harga_satuan_estimasi) as total')->value('total') ?? 0);
        $pendingPermintaanCount = Permintaan::whereIn('status', ['diajukan', 'disetujui'])->count();

        $tahunSekarang = $now->year;
        $bulanSekarang = $now->month;
        $namaBulan = $now->translatedFormat('F');

        // Rekomendasi Presets Sesuai Siklus Akuntansi Pemerintah / BPS
        $presets = [
            [
                'label' => "Semester I {$tahunSekarang}",
                'sub' => 'Audit Tengah Tahun (Per 30 Juni)',
                'val' => "Semester I {$tahunSekarang}",
            ],
            [
                'label' => "Semester II {$tahunSekarang}",
                'sub' => 'Tutup Buku Neraca Akhir Tahun (Per 31 Des)',
                'val' => "Semester II {$tahunSekarang}",
            ],
            [
                'label' => 'Triwulan ' . (int)ceil($bulanSekarang / 3) . " {$tahunSekarang}",
                'sub' => 'Audit Triwulanan Berkala',
                'val' => 'Triwulan ' . (int)ceil($bulanSekarang / 3) . " {$tahunSekarang}",
            ],
            [
                'label' => "{$namaBulan} {$tahunSekarang}",
                'sub' => 'Audit Rutin Bulanan',
                'val' => "{$namaBulan} {$tahunSekarang}",
            ],
        ];

        return view('stok-opname.create', compact(
            'defaultPeriode',
            'activeDraft',
            'totalBarang',
            'totalStokUnit',
            'totalNilaiBuku',
            'pendingPermintaanCount',
            'tahunSekarang',
            'presets'
        ));
    }

    /**
     * Membuka sesi baru Stock Opname dan membuat snapshot seluruh barang persediaan.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Administrator Subbag Umum yang dapat membuka sesi Stock Opname.');
        }

        $validated = $request->validate([
            'periode' => 'required|string|max:100',
            'tanggal_opname' => 'required|date',
        ], [
            'periode.required' => 'Nama periode opname wajib diisi (contoh: Semester I 2026 atau November 2026).',
            'tanggal_opname.required' => 'Tanggal pelaksanaan opname fisik wajib diisi.',
        ]);

        $opname = $this->service->bukaSesi(
            $user,
            $validated['periode'],
            Carbon::parse($validated['tanggal_opname'])
        );

        return redirect()->route('stok-opname.show', $opname->id)
            ->with('success', "Sesi Stock Opname nomor {$opname->nomor_opname} berhasil dibuka. Seluruh saldo barang persediaan telah disnapshot ke lembar kerja fisik.");
    }

    /**
     * Menampilkan lembar kerja perhitungan fisik Stock Opname.
     */
    public function show(Request $request, int $id): View
    {
        $opname = StokOpname::with(['user', 'pimpinan'])->findOrFail($id);

        $detailsQuery = StokOpnameDetail::where('stok_opname_id', $opname->id)
            ->with(['barang.kategori']);

        if ($request->filled('filter_selisih')) {
            if ($request->filter_selisih === 'selisih') {
                $detailsQuery->where('selisih', '!=', 0);
            } elseif ($request->filter_selisih === 'cocok') {
                $detailsQuery->where('selisih', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $detailsQuery->whereHas('barang', function ($b) use ($search) {
                $b->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $details = $detailsQuery->orderBy('barang_id', 'asc')->get();

        // Rekapitulasi metrik keseluruhan sesi (tanpa terpengaruh filter tampilan)
        $allDetails = StokOpnameDetail::where('stok_opname_id', $opname->id)->get();
        $totalItem = $allDetails->count();
        $totalCocok = $allDetails->where('selisih', 0)->count();
        $totalSelisih = $allDetails->where('selisih', '!=', 0)->count();
        $totalSelisihUnit = $allDetails->sum('selisih');

        return view('stok-opname.show', compact(
            'opname',
            'details',
            'totalItem',
            'totalCocok',
            'totalSelisih',
            'totalSelisihUnit'
        ));
    }

    /**
     * Menyimpan hasil hitung fisik barang (bisa batch maupun satuan).
     */
    public function updateFisik(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Administrator Subbag Umum yang dapat menginput hasil hitung fisik.');
        }

        $opname = StokOpname::findOrFail($id);

        if ($request->has('items') && is_array($request->items)) {
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.barang_id' => 'required|exists:barang_persediaan,id',
                'items.*.stok_fisik' => 'required|integer|min:0',
                'items.*.keterangan_selisih' => 'nullable|string|max:255',
            ]);

            foreach ($validated['items'] as $item) {
                $this->service->catatFisik(
                    $opname,
                    (int) $item['barang_id'],
                    (int) $item['stok_fisik'],
                    $item['keterangan_selisih'] ?? null
                );
            }

            return redirect()->back()->with('success', 'Hasil perhitungan fisik berhasil diperbarui.');
        }

        // Single item update
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang_persediaan,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan_selisih' => 'nullable|string|max:255',
        ]);

        $this->service->catatFisik(
            $opname,
            (int) $validated['barang_id'],
            (int) $validated['stok_fisik'],
            $validated['keterangan_selisih'] ?? null
        );

        return redirect()->back()->with('success', 'Catatan fisik barang berhasil disimpan.');
    }

    /**
     * Memfinalisasi sesi opname dan melakukan penyesuaian otomatis pada saldo sistem.
     */
    public function finalisasi(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Administrator Subbag Umum yang dapat memfinalisasi sesi Stock Opname.');
        }

        $opname = StokOpname::findOrFail($id);

        $validated = $request->validate([
            'catatan_kesimpulan' => 'nullable|string|max:500',
        ]);

        $this->service->finalisasi(
            $opname,
            $user,
            $validated['catatan_kesimpulan'] ?? null
        );

        return redirect()->route('stok-opname.show', $opname->id)
            ->with('success', "Sesi {$opname->nomor_opname} berhasil difinalisasi. Jurnal penyesuaian koreksi stok telah dibukukan otomatis ke buku kartu persediaan.");
    }

    /**
     * Pengesahan Berita Acara Stock Opname (BASO) oleh Pimpinan (Kepala BPS).
     */
    public function sahkan(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isPimpinan()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Kepala BPS / Pimpinan yang berwenang mengesahkan Berita Acara Stock Opname.');
        }

        $opname = StokOpname::findOrFail($id);

        $this->service->sahkanPimpinan($opname, $user);

        return redirect()->route('stok-opname.show', $opname->id)
            ->with('success', "Berita Acara Stock Opname {$opname->nomor_opname} telah resmi disahkan oleh Kepala BPS Kabupaten Cirebon.");
    }

    /**
     * Konversi bilangan integer menjadi kata terbilang Bahasa Indonesia.
     */
    public static function terbilang(int $angka): string
    {
        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return self::terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return self::terbilang((int)($angka / 10)) . ' puluh' . ($angka % 10 ? ' ' . self::terbilang($angka % 10) : '');
        } elseif ($angka < 200) {
            return 'seratus' . ($angka - 100 ? ' ' . self::terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            return self::terbilang((int)($angka / 100)) . ' ratus' . ($angka % 100 ? ' ' . self::terbilang($angka % 100) : '');
        } elseif ($angka < 2000) {
            return 'seribu' . ($angka - 1000 ? ' ' . self::terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            return self::terbilang((int)($angka / 1000)) . ' ribu' . ($angka % 1000 ? ' ' . self::terbilang($angka % 1000) : '');
        } elseif ($angka < 1000000000) {
            return self::terbilang((int)($angka / 1000000)) . ' juta' . ($angka % 1000000 ? ' ' . self::terbilang($angka % 1000000) : '');
        }
        return (string)$angka;
    }

    /**
     * Menampilkan layout cetak resmi Berita Acara Stock Opname (BASO).
     * Mendukung format komparasi fisik ('fisik') dan format neraca finansial SAKTI ('neraca').
     */
    public function cetak(Request $request, int $id): View
    {
        $opname = StokOpname::with(['user', 'pimpinan', 'details.barang.kategori'])->findOrFail($id);

        $format = $request->query('format', 'neraca'); // default format resmi neraca SAKTI
        $filter = $request->query('filter', 'ada_stok'); // 'ada_stok' (default versi baru ringkas) atau 'semua'

        $allDetails = $opname->details;
        $totalItemSemua = $allDetails->count();
        $totalSelisih = $allDetails->where('selisih', '!=', 0)->count();

        // Tanggal Terbilang Resmi Bahasa Indonesia
        $date = Carbon::parse($opname->tanggal_opname);
        $terbilangHari = $date->translatedFormat('l');
        $terbilangTgl = self::terbilang((int)$date->format('d'));
        $terbilangBulan = $date->translatedFormat('F');
        $terbilangTahun = self::terbilang((int)$date->format('Y'));

        // Komputasi Neraca Finansial (Sesuai Berita Acara Stock Opname.xlsx Tabel 1)
        $totalNilaiPersediaan = 0;
        $totalNilaiRusak = 0;
        $totalNilaiUsang = 0;

        foreach ($allDetails as $item) {
            $hargaBeli = (float) ($item->barang->harga_satuan_estimasi ?? 0);
            $qtyFisik = (int) $item->stok_fisik;
            $totalHarga = $hargaBeli * $qtyFisik;

            $rusakQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'rusak') ? abs($item->selisih) : 0;
            $rusakRp = $rusakQty * $hargaBeli;

            $usangQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'usang') ? abs($item->selisih) : 0;
            $usangRp = $usangQty * $hargaBeli;

            $saldoNeracaRp = $totalHarga - $rusakRp - $usangRp;

            $item->harga_beli_satuan = $hargaBeli;
            $item->total_persediaan_rp = $totalHarga;
            $item->rusak_qty = $rusakQty;
            $item->rusak_rp = $rusakRp;
            $item->usang_qty = $usangQty;
            $item->usang_rp = $usangRp;
            $item->saldo_neraca_rp = $saldoNeracaRp;

            $totalNilaiPersediaan += $totalHarga;
            $totalNilaiRusak += $rusakRp;
            $totalNilaiUsang += $usangRp;
        }

        $saldoNeracaTotal = $totalNilaiPersediaan - $totalNilaiRusak - $totalNilaiUsang;

        // Filter data jika 'ada_stok' (hanya barang bersaldo / ada fisik/nominal)
        if ($filter === 'ada_stok') {
            $displayDetails = $allDetails->filter(function ($item) {
                return (int) $item->stok_fisik > 0
                    || (float) $item->total_persediaan_rp > 0
                    || (int) $item->rusak_qty > 0
                    || (int) $item->usang_qty > 0
                    || (int) $item->selisih != 0;
            })->values();
        } else {
            $displayDetails = $allDetails->values();
        }

        $opname->setRelation('details', $displayDetails);
        $totalItem = $displayDetails->count();

        return view('stok-opname.cetak', compact(
            'opname',
            'totalItem',
            'totalItemSemua',
            'totalSelisih',
            'format',
            'filter',
            'terbilangHari',
            'terbilangTgl',
            'terbilangBulan',
            'terbilangTahun',
            'totalNilaiPersediaan',
            'totalNilaiRusak',
            'totalNilaiUsang',
            'saldoNeracaTotal'
        ));
    }

    /**
     * Unduh ekspor Berita Acara Stock Opname (BASO) Tabel 1 ke file Excel (.xlsx).
     */
    public function exportExcel(Request $request, int $id, \App\Services\SpreadsheetExportService $exportService): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $opname = StokOpname::with(['user', 'pimpinan', 'details.barang.kategori'])->findOrFail($id);
        $filter = $request->query('filter', 'ada_stok');
        $onlyWithStock = $filter === 'ada_stok';

        return $exportService->exportStockOpnameBaso($opname, $onlyWithStock);
    }

    /**
     * Menghapus sesi Stock Opname.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Administrator Subbag Umum yang dapat menghapus sesi Stock Opname.');
        }

        $opname = StokOpname::findOrFail($id);
        $nomorOpname = $opname->nomor_opname;

        $this->service->hapusSesi($opname, $user);

        return redirect()->route('stok-opname.index')
            ->with('success', "Sesi Stock Opname {$nomorOpname} berhasil dihapus.");
    }
}
