<?php

namespace App\Services;

use App\Models\BarangPersediaan;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\User;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class StockOpnameService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * Membuka sesi baru Stock Opname dan membuat snapshot freeze stok sistem.
     *
     * @param User $user
     * @param string $periode Contoh: 'November 2026'
     * @param DateTimeInterface|null $tanggal
     * @return StokOpname
     */
    public function bukaSesi(User $user, string $periode, ?DateTimeInterface $tanggal = null): StokOpname
    {
        return DB::transaction(function () use ($user, $periode, $tanggal) {
            $nomorOpname = $this->generateNomorOpname();

            $opname = StokOpname::create([
                'nomor_opname' => $nomorOpname,
                'tanggal_opname' => $tanggal ? Carbon::parse($tanggal)->toDateString() : Carbon::now()->toDateString(),
                'periode' => $periode,
                'status' => 'draft',
                'user_id' => $user->id,
            ]);

            // Snapshot seluruh barang persediaan aktif
            $semuaBarang = BarangPersediaan::all();
            foreach ($semuaBarang as $barang) {
                StokOpnameDetail::create([
                    'stok_opname_id' => $opname->id,
                    'barang_id' => $barang->id,
                    'stok_sistem' => $barang->stok_aktif,
                    'stok_fisik' => $barang->stok_aktif, // Default sama dengan sistem
                    'selisih' => 0,
                    'keterangan_selisih' => null,
                ]);
            }

            return $opname->load(['details.barang', 'user']);
        });
    }

    /**
     * Mencatat hasil perhitungan fisik untuk suatu barang pada sesi draft opname.
     *
     * @param StokOpname $opname
     * @param int $barangId
     * @param int $stokFisik
     * @param string|null $keterangan
     * @return StokOpnameDetail
     * @throws RuntimeException|InvalidArgumentException
     */
    public function catatFisik(StokOpname $opname, int $barangId, int $stokFisik, ?string $keterangan = null): StokOpnameDetail
    {
        if ($opname->status !== 'draft') {
            throw new RuntimeException("Pencatatan fisik hanya dapat dilakukan saat sesi opname berstatus 'draft'.");
        }

        if ($stokFisik < 0) {
            throw new InvalidArgumentException("Nilai stok fisik tidak boleh negatif.");
        }

        $detail = StokOpnameDetail::where('stok_opname_id', $opname->id)
            ->where('barang_id', $barangId)
            ->firstOrFail();

        $detail->stok_fisik = $stokFisik;
        $detail->selisih = $stokFisik - $detail->stok_sistem;
        $detail->keterangan_selisih = $keterangan;
        $detail->save();

        return $detail->load('barang');
    }

    /**
     * Memfinalisasi sesi opname dan melakukan penyesuaian stok otomatis pada barang yang selisih.
     *
     * @param StokOpname $opname
     * @param User $admin
     * @param string|null $catatan
     * @return StokOpname
     * @throws RuntimeException
     */
    public function finalisasi(StokOpname $opname, User $admin, ?string $catatan = null): StokOpname
    {
        if ($opname->status !== 'draft') {
            throw new RuntimeException("Hanya sesi opname berstatus 'draft' yang dapat difinalisasi.");
        }

        return DB::transaction(function () use ($opname, $admin, $catatan) {
            $opname->load('details.barang');

            foreach ($opname->details as $detail) {
                if ($detail->selisih !== 0) {
                    $this->stockMovementService->koreksiStok(
                        $detail->barang,
                        $detail->stok_fisik,
                        $admin,
                        [
                            'stok_opname_id' => $opname->id,
                            'nomor_dokumen' => $opname->nomor_opname,
                            'sumber_atau_tujuan' => 'Penyesuaian Fisik (Opname)',
                            'keterangan' => 'Penyesuaian Opname ' . $opname->periode . ($detail->keterangan_selisih ? ': ' . $detail->keterangan_selisih : ''),
                        ]
                    );
                }
            }

            $opname->update([
                'status' => 'final',
                'catatan_kesimpulan' => $catatan,
            ]);

            return $opname->fresh(['details.barang', 'transaksi']);
        });
    }

    /**
     * Pengesahan Berita Acara Stock Opname oleh Kepala BPS / Pimpinan.
     *
     * @param StokOpname $opname
     * @param User $pimpinan
     * @return StokOpname
     * @throws RuntimeException
     */
    public function sahkanPimpinan(StokOpname $opname, User $pimpinan): StokOpname
    {
        if ($opname->status !== 'final') {
            throw new RuntimeException("Hanya Berita Acara Opname berstatus 'final' yang dapat disahkan.");
        }

        $opname->update([
            'status' => 'disahkan',
            'pimpinan_id' => $pimpinan->id,
        ]);

        return $opname->fresh(['pimpinan']);
    }

    /**
     * Menghapus sesi Stock Opname.
     * Jika sesi berstatus final atau disahkan, jurnal penyesuaian stok akan dibatalkan/direverse otomatis.
     *
     * @param StokOpname $opname
     * @param User $admin
     * @return void
     * @throws RuntimeException
     */
    public function hapusSesi(StokOpname $opname, User $admin): void
    {
        if (! $admin->isAdmin()) {
            throw new RuntimeException("Hanya Administrator yang memiliki akses untuk menghapus sesi Stock Opname.");
        }

        DB::transaction(function () use ($opname) {
            $opname->load(['transaksi.barang', 'details']);

            // Jika sesi sudah pernah difinalisasi dan memiliki transaksi penyesuaian stok,
            // lakukan pembalikan stok (reversal) pada master barang
            foreach ($opname->transaksi as $transaksi) {
                if ($transaksi->barang) {
                    $barang = BarangPersediaan::where('id', $transaksi->barang_id)->lockForUpdate()->first();
                    if ($barang) {
                        $barang->stok_aktif -= $transaksi->jumlah;
                        if ($barang->stok_aktif < 0) {
                            $barang->stok_aktif = 0;
                        }
                        $barang->save();
                    }
                }
                $transaksi->delete();
            }

            // Hapus rincian detail snapshot fisik
            $opname->details()->delete();

            // Hapus rekaman sesi opname
            $opname->delete();
        });
    }

    /**
     * Menghasilkan nomor Berita Acara Opname: BA-SO/BPS3209/YYYY/MM/XXXX.
     *
     * @return string
     */
    protected function generateNomorOpname(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $prefix = "BA-SO/BPS3209/{$year}/{$month}/";

        $latest = StokOpname::where('nomor_opname', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest) {
            $parts = explode('/', $latest->nomor_opname);
            $lastSeq = (int)end($parts);
            $sequence = $lastSeq + 1;
        }

        return $prefix . str_pad((string)$sequence, 4, '0', STR_PAD_LEFT);
    }
}
