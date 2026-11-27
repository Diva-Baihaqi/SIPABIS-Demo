<?php

namespace App\Http\Controllers;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\Ruangan;
use App\Models\StokOpname;
use App\Models\TransaksiPersediaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk Administrator Subbag Umum (Pengelola Barang).
     */
    public function admin(): View
    {
        $totalBarang = BarangPersediaan::count();
        $stokMenipis = BarangPersediaan::where('stok_aktif', '>', 0)
            ->whereColumn('stok_aktif', '<=', 'stok_minimum')
            ->count();
        $permintaanMenunggu = Permintaan::where('status', 'diajukan')->count();
        $opnameAktif = StokOpname::where('status', 'draft')->count();

        $permintaanTerbaru = Permintaan::with(['user', 'ruangan'])
            ->latest()
            ->take(5)
            ->get();

        $barangMenipisList = BarangPersediaan::with('kategori')
            ->where('stok_aktif', '>', 0)
            ->whereColumn('stok_aktif', '<=', 'stok_minimum')
            ->orderBy('stok_aktif', 'asc')
            ->take(5)
            ->get();

        // 1. Tren Sirkulasi 6 Bulan Terakhir (Barang Masuk vs Barang Keluar)
        $bulanLabels = [];
        $masukData = [];
        $keluarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulanLabels[] = $date->translatedFormat('M Y');
            $year = $date->year;
            $month = $date->month;

            $masuk = (int) TransaksiPersediaan::where('jenis_transaksi', 'masuk')
                ->whereYear('tanggal_transaksi', $year)
                ->whereMonth('tanggal_transaksi', $month)
                ->sum('jumlah');

            $keluar = (int) TransaksiPersediaan::where('jenis_transaksi', 'keluar')
                ->whereYear('tanggal_transaksi', $year)
                ->whereMonth('tanggal_transaksi', $month)
                ->sum(DB::raw('ABS(jumlah)'));

            $masukData[] = $masuk;
            $keluarData[] = $keluar;
        }

        // 2. Komposisi Persediaan per Kategori
        $kategoriDistribution = KategoriBarang::withCount('barang')
            ->withSum('barang', 'stok_aktif')
            ->get()
            ->map(function ($kat) {
                return [
                    'nama' => $kat->nama_kategori,
                    'total_barang' => $kat->barang_count,
                    'total_stok' => (int) ($kat->barang_sum_stok_aktif ?? 0),
                ];
            });

        // 3. Penyerapan Persediaan per Ruangan/Fungsi BPS (7 Fungsi)
        $ruanganDistribution = Ruangan::all()->map(function ($r) {
            $totalKeluar = (int) PermintaanDetail::join('permintaan', 'permintaan_detail.permintaan_id', '=', 'permintaan.id')
                ->where('permintaan.ruangan_id', $r->id)
                ->where('permintaan.status', 'selesai')
                ->sum('permintaan_detail.jumlah_disetujui');

            return [
                'nama' => $r->nama_ruangan,
                'kode' => $r->kode_ruangan,
                'total_diserahkan' => $totalKeluar,
            ];
        });

        // Metrik Mutasi Bulan Ini
        $masukBulanIni = (int) TransaksiPersediaan::where('jenis_transaksi', 'masuk')
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->sum('jumlah');

        $keluarBulanIni = (int) TransaksiPersediaan::where('jenis_transaksi', 'keluar')
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->sum(DB::raw('ABS(jumlah)'));

        return view('dashboard.admin', compact(
            'totalBarang',
            'stokMenipis',
            'permintaanMenunggu',
            'opnameAktif',
            'permintaanTerbaru',
            'barangMenipisList',
            'bulanLabels',
            'masukData',
            'keluarData',
            'kategoriDistribution',
            'ruanganDistribution',
            'masukBulanIni',
            'keluarBulanIni'
        ));
    }

    /**
     * Dashboard untuk Pegawai / Tim Kerja.
     */
    public function pegawai(): View
    {
        $user = Auth::user();
        $userId = $user->id;
        $ruanganId = $user->ruangan_id;

        $totalPermintaan = Permintaan::where('user_id', $userId)->count();
        $permintaanMenunggu = Permintaan::where('user_id', $userId)->where('status', 'diajukan')->count();
        $permintaanDisetujui = Permintaan::where('user_id', $userId)->where('status', 'disetujui')->count();
        $permintaanSelesai = Permintaan::where('user_id', $userId)->where('status', 'selesai')->count();
        $permintaanDitolak = Permintaan::where('user_id', $userId)->where('status', 'ditolak')->count();

        $permintaanSaya = Permintaan::where('user_id', $userId)
            ->with(['details.barang'])
            ->latest()
            ->take(6)
            ->get();

        // 1. Distribusi Status Tiket Permintaan Pegawai
        $statusDistribution = [
            'Diajukan' => $permintaanMenunggu,
            'Disetujui' => $permintaanDisetujui,
            'Selesai' => $permintaanSelesai,
            'Ditolak' => $permintaanDitolak,
        ];

        // 2. Tren Penyerapan ATK Unit Kerja Pegawai (6 Bulan Terakhir)
        $bulanLabels = [];
        $ruanganTrenData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulanLabels[] = $date->translatedFormat('M Y');
            $year = $date->year;
            $month = $date->month;

            $volume = 0;
            if ($ruanganId) {
                $volume = (int) PermintaanDetail::join('permintaan', 'permintaan_detail.permintaan_id', '=', 'permintaan.id')
                    ->where('permintaan.ruangan_id', $ruanganId)
                    ->where('permintaan.status', 'selesai')
                    ->whereYear('permintaan.tanggal_serah_terima', $year)
                    ->whereMonth('permintaan.tanggal_serah_terima', $month)
                    ->sum('permintaan_detail.jumlah_disetujui');
            }
            $ruanganTrenData[] = $volume;
        }

        // 3. Top 5 Barang yang Paling Sering Diminta oleh Ruangan Pegawai
        $topBarangRuangan = collect();
        if ($ruanganId) {
            $topBarangRuangan = PermintaanDetail::join('permintaan', 'permintaan_detail.permintaan_id', '=', 'permintaan.id')
                ->join('barang_persediaan', 'permintaan_detail.barang_id', '=', 'barang_persediaan.id')
                ->where('permintaan.ruangan_id', $ruanganId)
                ->whereIn('permintaan.status', ['disetujui', 'selesai'])
                ->selectRaw('barang_persediaan.nama_barang, sum(permintaan_detail.jumlah_disetujui) as total_qty')
                ->groupBy('barang_persediaan.id', 'barang_persediaan.nama_barang')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get();
        }

        return view('dashboard.pegawai', compact(
            'totalPermintaan',
            'permintaanMenunggu',
            'permintaanDisetujui',
            'permintaanSelesai',
            'permintaanDitolak',
            'permintaanSaya',
            'statusDistribution',
            'bulanLabels',
            'ruanganTrenData',
            'topBarangRuangan'
        ));
    }

    /**
     * Dashboard untuk Kepala BPS / Pimpinan.
     */
    public function pimpinan(): View
    {
        $totalBarang = BarangPersediaan::count();
        $totalPermintaan = Permintaan::count();
        $permintaanSelesai = Permintaan::where('status', 'selesai')->count();
        $opnameMenungguPengesahan = StokOpname::where('status', 'final')->count();

        $opnameTerbaru = StokOpname::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 1. Akurasi Opname Fisik vs Sistem (Hasil Opname Terakhir)
        $latestOpname = StokOpname::with('details')->latest()->first();
        $akurasiOpname = 100.0;
        $totalItemOpname = 0;
        $totalCocokOpname = 0;
        $totalSelisihOpname = 0;

        if ($latestOpname && $latestOpname->details->isNotEmpty()) {
            $totalItemOpname = $latestOpname->details->count();
            $totalCocokOpname = $latestOpname->details->where('selisih', 0)->count();
            $totalSelisihOpname = $latestOpname->details->where('selisih', '!=', 0)->count();
            $akurasiOpname = round(($totalCocokOpname / $totalItemOpname) * 100, 1);
        }

        // 2. Komparasi Penyerapan Persediaan Antar 7 Fungsi BPS
        $ruanganSerapan = Ruangan::all()->map(function ($r) {
            $total = (int) PermintaanDetail::join('permintaan', 'permintaan_detail.permintaan_id', '=', 'permintaan.id')
                ->where('permintaan.ruangan_id', $r->id)
                ->where('permintaan.status', 'selesai')
                ->sum('permintaan_detail.jumlah_disetujui');

            return [
                'nama' => $r->nama_ruangan,
                'kode' => $r->kode_ruangan,
                'total' => $total,
            ];
        });

        // 3. Tren 6 Bulan Mutasi Logistik Kantor
        $bulanLabels = [];
        $masukData = [];
        $keluarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulanLabels[] = $date->translatedFormat('M Y');
            $year = $date->year;
            $month = $date->month;

            $masuk = (int) TransaksiPersediaan::where('jenis_transaksi', 'masuk')
                ->whereYear('tanggal_transaksi', $year)
                ->whereMonth('tanggal_transaksi', $month)
                ->sum('jumlah');

            $keluar = (int) TransaksiPersediaan::where('jenis_transaksi', 'keluar')
                ->whereYear('tanggal_transaksi', $year)
                ->whereMonth('tanggal_transaksi', $month)
                ->sum(DB::raw('ABS(jumlah)'));

            $masukData[] = $masuk;
            $keluarData[] = $keluar;
        }

        return view('dashboard.pimpinan', compact(
            'totalBarang',
            'totalPermintaan',
            'permintaanSelesai',
            'opnameMenungguPengesahan',
            'opnameTerbaru',
            'latestOpname',
            'akurasiOpname',
            'totalItemOpname',
            'totalCocokOpname',
            'totalSelisihOpname',
            'ruanganSerapan',
            'bulanLabels',
            'masukData',
            'keluarData'
        ));
    }
}
