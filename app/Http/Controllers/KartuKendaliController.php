<?php

namespace App\Http\Controllers;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Services\KartuKendaliService;
use App\Services\SpreadsheetExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KartuKendaliController extends Controller
{
    public function __construct(
        protected KartuKendaliService $service
    ) {}

    /**
     * Halaman indeks hub Buku Kendali & Kartu Stok Persediaan.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        // Pegawai otomatis diarahkan ke buku kendali ruangannya sendiri
        if ($user->isPegawai() && $user->ruangan_id) {
            return redirect()->route('kartu-kendali.ruangan', $user->ruangan_id);
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
        ];

        $rekapRuangan = $this->service->getRekapSemuaRuangan($filter);

        $kategoriId = $request->input('kategori_id');

        $barangs = BarangPersediaan::with('kategori')
            ->when($request->filled('search_barang'), function ($q) use ($request) {
                $search = $request->search_barang;
                $q->where(function ($query) use ($search) {
                    $query->where('nama_barang', 'like', "%{$search}%")
                          ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('kategori_id'), function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            })
            ->orderBy('nama_barang', 'asc')
            ->paginate(12)
            ->withQueryString();

        $totalUnitSemuaRuangan = $rekapRuangan->sum('total_unit');
        $kategoriList = KategoriBarang::orderBy('nama_kategori')->get();

        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return view('kartu-kendali.index', compact(
            'rekapRuangan',
            'barangs',
            'totalUnitSemuaRuangan',
            'filter',
            'kategoriList',
            'kategoriId',
            'bulanList'
        ));
    }

    /**
     * Menampilkan Buku Kendali konsumsi persediaan per unit kerja / ruangan.
     */
    public function ruangan(Request $request, int $id): View
    {
        $user = Auth::user();

        // Pegawai hanya boleh melihat buku kendali unit kerjanya sendiri
        if ($user->isPegawai() && $user->ruangan_id != $id) {
            abort(Response::HTTP_FORBIDDEN, 'Anda hanya memiliki izin melihat buku kendali unit kerja Anda sendiri.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
            'barang_id' => $request->input('barang_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $data = $this->service->getBukuKendaliRuangan($id, array_filter($filter));
        $daftarBarang = BarangPersediaan::orderBy('nama_barang', 'asc')->get();
        $semuaRuangan = $user->isPegawai() ? collect() : Ruangan::orderBy('id', 'asc')->get();

        return view('kartu-kendali.ruangan', array_merge($data, [
            'daftarBarang' => $daftarBarang,
            'semuaRuangan' => $semuaRuangan,
        ]));
    }

    /**
     * Menampilkan Kartu Stok / Rincian Buku Persediaan per barang format UC_PER53.
     */
    public function barang(Request $request, int $id): View
    {
        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $data = $this->service->getKartuStokBarang($id, array_filter($filter));
        $semuaBarang = BarangPersediaan::orderBy('nama_barang', 'asc')->get(['id', 'nama_barang', 'kode_barang']);

        return view('kartu-kendali.barang', array_merge($data, [
            'semuaBarang' => $semuaBarang,
        ]));
    }

    /**
     * Menampilkan layout cetak resmi Buku Kendali per unit kerja BPS.
     */
    public function cetakRuangan(Request $request, int $id): View
    {
        $user = Auth::user();

        if ($user->isPegawai() && $user->ruangan_id != $id) {
            abort(Response::HTTP_FORBIDDEN, 'Anda hanya memiliki izin mencetak buku kendali unit kerja Anda sendiri.');
        }

        $data = $this->service->getBukuKendaliRuangan($id, $request->all());

        return view('kartu-kendali.cetak-ruangan', $data);
    }

    /**
     * Menampilkan layout cetak resmi Kartu Persediaan UC_PER53 BPS.
     */
    public function cetakBarang(Request $request, int $id): View
    {
        $data = $this->service->getKartuStokBarang($id, $request->all());

        return view('kartu-kendali.cetak-barang', $data);
    }

    /**
     * Unduh ekspor Buku Kendali Ruangan ke file Excel (.xlsx).
     */
    public function exportRuangan(Request $request, int $id, \App\Services\SpreadsheetExportService $exportService): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();
        if ($user->isPegawai() && $user->ruangan_id != $id) {
            abort(Response::HTTP_FORBIDDEN, 'Anda hanya berhak mengunduh buku kendali unit kerja Anda sendiri.');
        }

        $ruangan = Ruangan::findOrFail($id);
        $data = $this->service->getBukuKendaliRuangan($id, $request->all());

        return $exportService->exportBukuKendaliRuangan($ruangan, $data);
    }

    /**
     * Unduh ekspor Kartu Stok Perpetual FIFO UC_PER53 ke file Excel (.xlsx).
     */
    public function exportBarang(Request $request, int $id, \App\Services\SpreadsheetExportService $exportService): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $barang = BarangPersediaan::findOrFail($id);
        $data = $this->service->getKartuStokBarang($id, $request->all());

        return $exportService->exportKartuStokBarang($barang, $data);
    }

    /**
     * Menampilkan layout cetak resmi Buku Kendali seluruh unit kerja BPS (PDF siap simpan).
     */
    public function cetakSemuaRuangan(Request $request): View
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang dapat mencetak buku kendali seluruh unit kerja.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
        ];

        $data = $this->service->getDataBukuKendaliSemuaRuangan(array_filter($filter));

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return view('kartu-kendali.cetak-semua-ruangan', array_merge($data, [
            'bulanList' => $bulanList,
        ]));
    }

    /**
     * Unduh ekspor Multi-Sheet Excel (.xlsx) Buku Kendali seluruh unit kerja.
     */
    public function exportSemuaRuanganExcel(Request $request, SpreadsheetExportService $exportService): StreamedResponse
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang berhak mengunduh buku kendali seluruh unit kerja.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
        ];

        $data = $this->service->getDataBukuKendaliSemuaRuangan(array_filter($filter));

        return $exportService->exportBukuKendaliSemuaRuangan($data['ruangans'], $data['dataPerRuangan'], $data['rekapSemuaRuangan'], $filter);
    }

    /**
     * Unduh dokumen Microsoft Word (.doc) Buku Kendali seluruh unit kerja.
     */
    public function exportSemuaRuanganWord(Request $request): Response
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang berhak mengunduh buku kendali seluruh unit kerja.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
        ];

        $data = $this->service->getDataBukuKendaliSemuaRuangan(array_filter($filter));

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $content = view('kartu-kendali.word-semua-ruangan', array_merge($data, [
            'bulanList' => $bulanList,
        ]))->render();

        $filename = 'Buku_Kendali_BPS_Semua_Divisi_' . ($filter['tahun'] ?? date('Y')) . '_' . date('Ymd_His') . '.doc';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-word; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Unduh dokumen Microsoft Word (.doc) Buku Kendali untuk ruangan tertentu.
     */
    public function exportRuanganWord(Request $request, int $id): Response
    {
        $user = Auth::user();
        if ($user->isPegawai() && $user->ruangan_id != $id) {
            abort(Response::HTTP_FORBIDDEN, 'Anda hanya berhak mengunduh buku kendali unit kerja Anda sendiri.');
        }

        $ruangan = Ruangan::findOrFail($id);
        $data = $this->service->getBukuKendaliRuangan($id, $request->all());

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $content = view('kartu-kendali.word-ruangan', array_merge($data, [
            'ruangan' => $ruangan,
            'bulanList' => $bulanList,
        ]))->render();

        $filename = 'Buku_Kendali_' . str_replace(' ', '_', $ruangan->nama_ruangan) . '_' . date('Ymd_His') . '.doc';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-word; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Menampilkan layout pratinjau cetak resmi Rekapitulasi Posisi Persediaan ATK (PDF Landscape).
     */
    public function cetakRekapBarang(Request $request): View
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang dapat mencetak rekapitulasi persediaan barang.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
            'kategori_id' => $request->input('kategori_id'),
            'cakupan' => $request->input('cakupan', 'aktif'),
            'search' => $request->input('search_barang'),
        ];

        $data = $this->service->getRekapPosisiPersediaanSemuaBarang(array_filter($filter));

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return view('kartu-kendali.cetak-rekap-barang', array_merge($data, [
            'bulanList' => $bulanList,
        ]));
    }

    /**
     * Unduh ekspor Excel (.xlsx) Rekapitulasi Posisi Persediaan ATK.
     */
    public function exportRekapBarangExcel(Request $request, SpreadsheetExportService $exportService): StreamedResponse
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang berhak mengunduh rekapitulasi persediaan barang.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
            'kategori_id' => $request->input('kategori_id'),
            'cakupan' => $request->input('cakupan', 'aktif'),
            'search' => $request->input('search_barang'),
        ];

        $data = $this->service->getRekapPosisiPersediaanSemuaBarang(array_filter($filter));

        return $exportService->exportRekapPersediaanBarang($data);
    }

    /**
     * Unduh dokumen Microsoft Word (.doc) Rekapitulasi Posisi Persediaan ATK.
     */
    public function exportRekapBarangWord(Request $request): Response
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(Response::HTTP_FORBIDDEN, 'Hanya Admin dan Pimpinan yang berhak mengunduh rekapitulasi persediaan barang.');
        }

        $filter = [
            'tahun' => $request->input('tahun', Carbon::now()->year),
            'bulan' => $request->input('bulan'),
            'kategori_id' => $request->input('kategori_id'),
            'cakupan' => $request->input('cakupan', 'aktif'),
            'search' => $request->input('search_barang'),
        ];

        $data = $this->service->getRekapPosisiPersediaanSemuaBarang(array_filter($filter));

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $content = view('kartu-kendali.word-rekap-barang', array_merge($data, [
            'bulanList' => $bulanList,
        ]))->render();

        $filename = 'Rekap_Persediaan_ATK_BPS_' . ($filter['tahun'] ?? date('Y')) . '_' . date('Ymd_His') . '.doc';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-word; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
