<?php

namespace App\Services;

use App\Models\BarangPersediaan;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PermintaanApprovalService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * Membuat pengajuan permintaan baru oleh pegawai.
     *
     * @param User $pemohon
     * @param array $items Array of ['barang_id' => int, 'jumlah_diminta' => int, 'catatan_item' => ?string]
     * @param string|null $catatanPemohon
     * @return Permintaan
     * @throws InvalidArgumentException
     */
    public function buatPermintaan(User $pemohon, array $items, ?string $catatanPemohon = null): Permintaan
    {
        if (empty($items)) {
            throw new InvalidArgumentException("Daftar barang permintaan tidak boleh kosong.");
        }

        if (!$pemohon->ruangan_id) {
            throw new InvalidArgumentException("Pegawai pemohon belum terdaftar pada unit ruangan kerja.");
        }

        return DB::transaction(function () use ($pemohon, $items, $catatanPemohon) {
            $nomorPermintaan = $this->generateNomorPermintaan();

            $permintaan = Permintaan::create([
                'nomor_permintaan' => $nomorPermintaan,
                'user_id' => $pemohon->id,
                'ruangan_id' => $pemohon->ruangan_id,
                'tanggal_pengajuan' => Carbon::now()->toDateString(),
                'status' => 'diajukan',
                'catatan_pemohon' => $catatanPemohon,
            ]);

            foreach ($items as $item) {
                if (!isset($item['barang_id']) || !isset($item['jumlah_diminta']) || (int)$item['jumlah_diminta'] <= 0) {
                    throw new InvalidArgumentException("Setiap item permintaan harus memiliki barang_id dan jumlah_diminta > 0.");
                }

                PermintaanDetail::create([
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah_diminta' => (int)$item['jumlah_diminta'],
                    'jumlah_disetujui' => 0,
                    'catatan_item' => $item['catatan_item'] ?? null,
                ]);
            }

            return $permintaan->load(['details.barang', 'ruangan', 'user']);
        });
    }

    /**
     * Memverifikasi permintaan oleh Admin (Persetujuan kuota sebagian atau penuh).
     *
     * @param Permintaan $permintaan
     * @param User $admin
     * @param array $persetujuanItems Keyed by detail_id => int jumlah_disetujui
     * @param string|null $catatanAdmin
     * @return Permintaan
     * @throws RuntimeException
     */
    public function verifikasi(Permintaan $permintaan, User $admin, array $persetujuanItems, ?string $catatanAdmin = null): Permintaan
    {
        if ($permintaan->status !== 'diajukan') {
            throw new RuntimeException("Hanya permintaan berstatus 'diajukan' yang dapat diverifikasi.");
        }

        return DB::transaction(function () use ($permintaan, $admin, $persetujuanItems, $catatanAdmin) {
            $totalDisetujui = 0;

            foreach ($permintaan->details as $detail) {
                $qtyDisetujui = isset($persetujuanItems[$detail->id]) ? (int)$persetujuanItems[$detail->id] : 0;
                
                // Kuota disetujui tidak boleh melebihi jumlah diminta atau negatif
                if ($qtyDisetujui < 0) {
                    $qtyDisetujui = 0;
                } elseif ($qtyDisetujui > $detail->jumlah_diminta) {
                    $qtyDisetujui = $detail->jumlah_diminta;
                }

                $detail->jumlah_disetujui = $qtyDisetujui;
                $detail->save();

                $totalDisetujui += $qtyDisetujui;
            }

            // Jika total disetujui 0, ubah status menjadi ditolak
            $statusBaru = $totalDisetujui > 0 ? 'disetujui' : 'ditolak';

            $permintaan->update([
                'status' => $statusBaru,
                'verifikator_id' => $admin->id,
                'tanggal_verifikasi' => Carbon::now(),
                'catatan_admin' => $catatanAdmin,
            ]);

            return $permintaan->fresh(['details.barang', 'verifikator']);
        });
    }

    /**
     * Melakukan serah terima fisik barang persediaan kepada pemohon.
     * Mengurangi stok aktif secara resmi via StockMovementService.
     *
     * @param Permintaan $permintaan
     * @param User $admin
     * @param string|null $fileBukti
     * @return Permintaan
     * @throws RuntimeException
     */
    public function serahTerima(Permintaan $permintaan, User $admin, ?string $fileBukti = null): Permintaan
    {
        if ($permintaan->status !== 'disetujui') {
            throw new RuntimeException("Serah terima hanya dapat dilakukan untuk permintaan berstatus 'disetujui'.");
        }

        return DB::transaction(function () use ($permintaan, $admin, $fileBukti) {
            foreach ($permintaan->details as $detail) {
                if ($detail->jumlah_disetujui > 0) {
                    $this->stockMovementService->kurangiStok(
                        $detail->barang,
                        $detail->jumlah_disetujui,
                        $admin,
                        [
                            'permintaan_id' => $permintaan->id,
                            'nomor_dokumen' => $permintaan->nomor_permintaan,
                            'sumber_atau_tujuan' => $permintaan->ruangan->nama_ruangan ?? 'Ruangan Pemohon',
                            'keterangan' => 'Serah terima ATK permintaan ' . $permintaan->nomor_permintaan,
                        ]
                    );
                }
            }

            $permintaan->update([
                'status' => 'selesai',
                'tanggal_serah_terima' => Carbon::now(),
                'file_bukti_serah_terima' => $fileBukti,
            ]);

            return $permintaan->fresh(['details.barang', 'transaksi']);
        });
    }

    /**
     * Menolak pengajuan permintaan ATK.
     *
     * @param Permintaan $permintaan
     * @param User $admin
     * @param string $alasan
     * @return Permintaan
     * @throws RuntimeException
     */
    public function tolak(Permintaan $permintaan, User $admin, string $alasan): Permintaan
    {
        if (!in_array($permintaan->status, ['diajukan', 'disetujui'])) {
            throw new RuntimeException("Permintaan dengan status '{$permintaan->status}' tidak dapat ditolak.");
        }

        $permintaan->update([
            'status' => 'ditolak',
            'verifikator_id' => $admin->id,
            'tanggal_verifikasi' => Carbon::now(),
            'catatan_admin' => $alasan,
        ]);

        return $permintaan->fresh();
    }

    /**
     * Membatalkan pengajuan oleh pemohon (hanya jika masih 'diajukan').
     *
     * @param Permintaan $permintaan
     * @param User $pemohon
     * @param string|null $alasan
     * @return Permintaan
     * @throws RuntimeException
     */
    public function batalkan(Permintaan $permintaan, User $pemohon, ?string $alasan = null): Permintaan
    {
        if ($permintaan->user_id !== $pemohon->id) {
            throw new RuntimeException("Anda tidak berhak membatalkan permintaan milik pegawai lain.");
        }

        if ($permintaan->status !== 'diajukan') {
            throw new RuntimeException("Hanya permintaan yang berstatus 'diajukan' yang dapat dibatalkan.");
        }

        $catatan = $permintaan->catatan_pemohon;
        if ($alasan) {
            $catatan .= " [Dibatalkan oleh pemohon: {$alasan}]";
        }

        $permintaan->update([
            'status' => 'dibatalkan',
            'catatan_pemohon' => $catatan,
        ]);

        return $permintaan->fresh();
    }

    /**
     * Menghasilkan nomor permintaan resmi BPS: PSD-[nomor]/32091/PL.711/[tahun].
     * Sesuai format riil dokumen BPS 'form permintaan persediaan.pdf' & Buku Persediaan SAKTI (UC_PER53).
     *
     * @return string
     */
    protected function generateNomorPermintaan(): string
    {
        $year = Carbon::now()->format('Y');

        // Cari nomor terakhir dengan format resmi PSD BPS pada tahun bersangkutan
        $latest = Permintaan::where('nomor_permintaan', 'like', "PSD-%/32091/PL.711/{$year}")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/PSD-(\d+)\/32091\/PL\.711/', $latest->nomor_permintaan, $matches)) {
            $sequence = (int)$matches[1] + 1;
        } else {
            // Jika belum ada nomor format PSD pada tahun ini, hitung total permohonan tahun ini
            $countYear = Permintaan::whereYear('tanggal_pengajuan', $year)->count();
            $sequence = max(1, $countYear + 1);
        }

        return "PSD-{$sequence}/32091/PL.711/{$year}";
    }
}
