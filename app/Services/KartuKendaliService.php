<?php

namespace App\Services;

use App\Models\BarangPersediaan;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KartuKendaliService
{
    /**
     * Mengambil data Buku Kendali distribusi ATK untuk ruangan tertentu.
     *
     * @param int $ruanganId
     * @param array $filter ['tahun' => int, 'bulan' => int, 'start_date' => string, 'end_date' => string, 'barang_id' => int]
     * @return array
     */
    public function getBukuKendaliRuangan(int $ruanganId, array $filter = []): array
    {
        $ruangan = Ruangan::findOrFail($ruanganId);

        $query = TransaksiPersediaan::where('jenis_transaksi', 'keluar')
            ->where(function ($q) use ($ruanganId, $ruangan) {
                $q->whereHas('permintaan', function ($p) use ($ruanganId) {
                    $p->where('ruangan_id', $ruanganId);
                })->orWhere('sumber_atau_tujuan', $ruangan->nama_ruangan);
            })
            ->with(['barang.kategori', 'user', 'permintaan']);

        if (!empty($filter['tahun'])) {
            $query->whereYear('tanggal_transaksi', $filter['tahun']);
        }

        if (!empty($filter['bulan'])) {
            $query->whereMonth('tanggal_transaksi', $filter['bulan']);
        }

        if (!empty($filter['start_date'])) {
            $query->whereDate('tanggal_transaksi', '>=', $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $query->whereDate('tanggal_transaksi', '<=', $filter['end_date']);
        }

        if (!empty($filter['barang_id'])) {
            $query->where('barang_id', $filter['barang_id']);
        }

        $transaksiList = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalUnitKeluar = $transaksiList->sum(fn($t) => abs($t->jumlah));
        $totalJenisBarang = $transaksiList->pluck('barang_id')->unique()->count();

        $rekapBarang = $transaksiList->groupBy('barang_id')->map(function ($items) {
            $first = $items->first();
            return (object) [
                'barang_id' => $first->barang_id,
                'nama_barang' => $first->barang->nama_barang,
                'kode_barang' => $first->barang->kode_barang,
                'kategori' => $first->barang->kategori?->nama_kategori ?? 'Umum',
                'satuan' => $first->barang->satuan,
                'total_jumlah' => $items->sum(fn($i) => abs($i->jumlah)),
                'frekuensi' => $items->count(),
            ];
        })->sortByDesc('total_jumlah')->values();

        return [
            'ruangan' => $ruangan,
            'transaksiList' => $transaksiList,
            'rekapBarang' => $rekapBarang,
            'totalUnitKeluar' => $totalUnitKeluar,
            'totalJenisBarang' => $totalJenisBarang,
            'filter' => $filter,
        ];
    }

    /**
     * Mengambil data Kartu Stok / Rincian Buku Persediaan per barang.
     * Sesuai format resmi BPS UC_PER53.
     *
     * @param int $barangId
     * @param array $filter ['start_date' => string, 'end_date' => string, 'tahun' => int]
     * @return array
     */
    public function getKartuStokBarang(int $barangId, array $filter = []): array
    {
        $barang = BarangPersediaan::with('kategori')->findOrFail($barangId);

        $startDate = $filter['start_date'] ?? null;
        $endDate = $filter['end_date'] ?? null;

        if (!$startDate && !empty($filter['tahun'])) {
            $startDate = "{$filter['tahun']}-01-01";
            $endDate = "{$filter['tahun']}-12-31";
        }

        // 1. Ambil mutasi dalam periode
        $query = TransaksiPersediaan::where('barang_id', $barangId)
            ->with(['user', 'permintaan.ruangan'])
            ->orderBy('tanggal_transaksi', 'asc')
            ->orderBy('id', 'asc');

        if ($startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $endDate);
        }

        $mutasiRaw = $query->get();

        // 2. Hitung saldo awal sebelum tanggal awal filter
        $saldoAwal = 0;
        if ($startDate) {
            $lastBefore = TransaksiPersediaan::where('barang_id', $barangId)
                ->whereDate('tanggal_transaksi', '<', $startDate)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastBefore) {
                $saldoAwal = (int) $lastBefore->saldo_setelah;
            } elseif ($mutasiRaw->isNotEmpty()) {
                // Hitung mundur saldo awal sebelum transaksi pertama di periode ini
                $firstMutation = $mutasiRaw->first();
                $saldoAwal = max(0, (int) $firstMutation->saldo_setelah - (int) $firstMutation->jumlah);
            } else {
                // Jika belum ada transaksi apapun, saldo awal adalah stok fisik yang ada
                $saldoAwal = (int) $barang->stok_aktif;
            }
        } elseif ($mutasiRaw->isEmpty()) {
            $saldoAwal = (int) $barang->stok_aktif;
        }

        $hargaSatuan = (float) ($barang->harga_satuan_estimasi ?? 0);

        $mutasiList = $mutasiRaw->map(function ($t) use ($hargaSatuan) {
            $masuk = $t->jumlah > 0 ? $t->jumlah : 0;
            $keluar = $t->jumlah < 0 ? abs($t->jumlah) : 0;

            $uraian = $t->sumber_atau_tujuan;
            if ($t->keterangan && $t->keterangan !== $t->sumber_atau_tujuan) {
                $uraian .= ' - ' . $t->keterangan;
            }

            $masukRp = $masuk * $hargaSatuan;
            $keluarRp = $keluar * $hargaSatuan;
            $saldoRp = ((int) $t->saldo_setelah) * $hargaSatuan;

            return (object) [
                'id' => $t->id,
                'tanggal' => $t->tanggal_transaksi,
                'nomor_dokumen' => $t->nomor_dokumen ?? '-',
                'uraian' => $uraian,
                'jenis' => $t->jenis_transaksi,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'saldo' => $t->saldo_setelah,
                'harga_satuan' => $hargaSatuan,
                'masuk_rp' => $masukRp,
                'keluar_rp' => $keluarRp,
                'saldo_rp' => $saldoRp,
            ];
        });

        $totalMasuk = $mutasiRaw->where('jumlah', '>', 0)->sum('jumlah');
        $totalKeluar = abs($mutasiRaw->where('jumlah', '<', 0)->sum('jumlah'));

        $saldoAkhir = $mutasiRaw->isNotEmpty() 
            ? $mutasiRaw->last()->saldo_setelah 
            : ($startDate ? $saldoAwal : $barang->stok_aktif);

        $saldoAwalRp = $saldoAwal * $hargaSatuan;
        $totalMasukRp = $totalMasuk * $hargaSatuan;
        $totalKeluarRp = $totalKeluar * $hargaSatuan;
        $saldoAkhirRp = $saldoAkhir * $hargaSatuan;

        return [
            'barang' => $barang,
            'saldoAwal' => $saldoAwal,
            'saldoAwalRp' => $saldoAwalRp,
            'mutasiList' => $mutasiList,
            'totalMasuk' => $totalMasuk,
            'totalMasukRp' => $totalMasukRp,
            'totalKeluar' => $totalKeluar,
            'totalKeluarRp' => $totalKeluarRp,
            'saldoAkhir' => $saldoAkhir,
            'saldoAkhirRp' => $saldoAkhirRp,
            'hargaSatuan' => $hargaSatuan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filter' => $filter,
        ];
    }

    /**
     * Mengambil rekapitulasi penarikan ATK di seluruh 7 unit kerja BPS.
     *
     * @param array $filter
     * @return Collection
     */
    public function getRekapSemuaRuangan(array $filter = []): Collection
    {
        $ruangans = Ruangan::orderBy('id', 'asc')->get();

        return $ruangans->map(function ($r) use ($filter) {
            $query = TransaksiPersediaan::where('jenis_transaksi', 'keluar')
                ->where(function ($q) use ($r) {
                    $q->whereHas('permintaan', function ($p) use ($r) {
                        $p->where('ruangan_id', $r->id);
                    })->orWhere('sumber_atau_tujuan', $r->nama_ruangan);
                });

            if (!empty($filter['tahun'])) {
                $query->whereYear('tanggal_transaksi', $filter['tahun']);
            }

            if (!empty($filter['bulan'])) {
                $query->whereMonth('tanggal_transaksi', $filter['bulan']);
            }

            $transaksis = $query->get();

            $r->total_unit = $transaksis->sum(fn($t) => abs($t->jumlah));
            $r->total_transaksi = $transaksis->count();
            $r->total_jenis_barang = $transaksis->pluck('barang_id')->unique()->count();
            $r->terakhir_transaksi = $transaksis->max('tanggal_transaksi');

            return $r;
        });
    }

    /**
     * Mengambil data komprehensif Buku Kendali seluruh ruangan/unit kerja BPS.
     * Digunakan untuk cetak PDF kolektif, ekspor Excel multi-sheet, dan ekspor Word.
     *
     * @param array $filter
     * @return array
     */
    public function getDataBukuKendaliSemuaRuangan(array $filter = []): array
    {
        $ruangans = Ruangan::orderBy('id', 'asc')->get();
        $rekapSemuaRuangan = $this->getRekapSemuaRuangan($filter);

        $dataPerRuangan = [];
        foreach ($ruangans as $r) {
            $dataPerRuangan[$r->id] = $this->getBukuKendaliRuangan($r->id, $filter);
        }

        $totalUnitSemua = $rekapSemuaRuangan->sum('total_unit');
        $totalTransaksiSemua = $rekapSemuaRuangan->sum('total_transaksi');

        return [
            'ruangans' => $ruangans,
            'rekapSemuaRuangan' => $rekapSemuaRuangan,
            'dataPerRuangan' => $dataPerRuangan,
            'totalUnitSemua' => $totalUnitSemua,
            'totalTransaksiSemua' => $totalTransaksiSemua,
            'filter' => $filter,
        ];
    }

    /**
     * Mengambil data rekapitulasi posisi fisik dan nilai persediaan seluruh barang.
     * Digunakan untuk Laporan Posisi Persediaan / Neraca ATK BPS.
     *
     * @param array $filter
     * @return array
     */
    public function getRekapPosisiPersediaanSemuaBarang(array $filter = []): array
    {
        $tahun = (int) ($filter['tahun'] ?? Carbon::now()->year);
        $bulan = $filter['bulan'] ?? null;
        $kategoriId = $filter['kategori_id'] ?? null;
        $cakupan = $filter['cakupan'] ?? 'aktif';

        if (!empty($bulan)) {
            $startDate = Carbon::createFromDate($tahun, (int) $bulan, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate($tahun, (int) $bulan, 1)->endOfMonth()->toDateString();
        } else {
            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-12-31";
        }

        $query = BarangPersediaan::with('kategori')
            ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
            ->when(!empty($filter['search']), function ($q) use ($filter) {
                $search = $filter['search'];
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_barang', 'asc');

        if ($cakupan === 'aktif') {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->where('stok_aktif', '>', 0)
                  ->orWhereHas('transaksiPersediaan', function ($t) use ($startDate, $endDate) {
                      $t->whereDate('tanggal_transaksi', '>=', $startDate)
                        ->whereDate('tanggal_transaksi', '<=', $endDate);
                  });
            });
        }

        $barangs = $query->get();

        $barangItems = [];
        $grandTotalSaldoAwalFisik = 0;
        $grandTotalSaldoAwalRp = 0;
        $grandTotalMasukFisik = 0;
        $grandTotalMasukRp = 0;
        $grandTotalKeluarFisik = 0;
        $grandTotalKeluarRp = 0;
        $grandTotalSaldoAkhirFisik = 0;
        $grandTotalSaldoAkhirRp = 0;

        foreach ($barangs as $b) {
            $itemData = $this->getKartuStokBarang($b->id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tahun' => $tahun,
            ]);

            $saldoAwal = (int) $itemData['saldoAwal'];
            $totalMasuk = (int) $itemData['totalMasuk'];
            $totalKeluar = (int) $itemData['totalKeluar'];
            $saldoAkhir = (int) $itemData['saldoAkhir'];
            $hargaSatuan = (float) $itemData['hargaSatuan'];

            $saldoAwalRp = $saldoAwal * $hargaSatuan;
            $totalMasukRp = $totalMasuk * $hargaSatuan;
            $totalKeluarRp = $totalKeluar * $hargaSatuan;
            $saldoAkhirRp = $saldoAkhir * $hargaSatuan;

            if ($cakupan === 'aktif' && $saldoAwal === 0 && $totalMasuk === 0 && $totalKeluar === 0 && $saldoAkhir === 0) {
                continue;
            }

            $barangItems[] = (object) [
                'barang' => $b,
                'kode_barang' => $b->kode_barang,
                'nama_barang' => $b->nama_barang,
                'kategori' => $b->kategori?->nama_kategori ?? 'Umum',
                'satuan' => $b->satuan,
                'harga_satuan' => $hargaSatuan,
                'saldo_awal' => $saldoAwal,
                'saldo_awal_rp' => $saldoAwalRp,
                'total_masuk' => $totalMasuk,
                'total_masuk_rp' => $totalMasukRp,
                'total_keluar' => $totalKeluar,
                'total_keluar_rp' => $totalKeluarRp,
                'saldo_akhir' => $saldoAkhir,
                'saldo_akhir_rp' => $saldoAkhirRp,
            ];

            $grandTotalSaldoAwalFisik += $saldoAwal;
            $grandTotalSaldoAwalRp += $saldoAwalRp;
            $grandTotalMasukFisik += $totalMasuk;
            $grandTotalMasukRp += $totalMasukRp;
            $grandTotalKeluarFisik += $totalKeluar;
            $grandTotalKeluarRp += $totalKeluarRp;
            $grandTotalSaldoAkhirFisik += $saldoAkhir;
            $grandTotalSaldoAkhirRp += $saldoAkhirRp;
        }

        return [
            'barangItems' => collect($barangItems),
            'totalJenisBarang' => count($barangItems),
            'grandTotalSaldoAwalFisik' => $grandTotalSaldoAwalFisik,
            'grandTotalSaldoAwalRp' => $grandTotalSaldoAwalRp,
            'grandTotalMasukFisik' => $grandTotalMasukFisik,
            'grandTotalMasukRp' => $grandTotalMasukRp,
            'grandTotalKeluarFisik' => $grandTotalKeluarFisik,
            'grandTotalKeluarRp' => $grandTotalKeluarRp,
            'grandTotalSaldoAkhirFisik' => $grandTotalSaldoAkhirFisik,
            'grandTotalSaldoAkhirRp' => $grandTotalSaldoAkhirRp,
            'filter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }
}
