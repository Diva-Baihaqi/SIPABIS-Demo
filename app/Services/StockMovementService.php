<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\BarangPersediaan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockMovementService
{
    /**
     * Menambahkan stok barang persediaan (Pengadaan / Penerimaan Vendor).
     *
     * @param BarangPersediaan $barang
     * @param int $jumlah
     * @param User $user
     * @param array $meta
     * @return TransaksiPersediaan
     * @throws InvalidArgumentException
     */
    public function tambahStok(BarangPersediaan $barang, int $jumlah, User $user, array $meta = []): TransaksiPersediaan
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah penambahan stok harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($barang, $jumlah, $user, $meta) {
            /** @var BarangPersediaan $lockedBarang */
            $lockedBarang = BarangPersediaan::where('id', $barang->id)->lockForUpdate()->firstOrFail();

            $saldoSebelum = $lockedBarang->stok_aktif;
            $saldoSetelah = $saldoSebelum + $jumlah;

            $lockedBarang->stok_aktif = $saldoSetelah;
            $lockedBarang->save();

            return TransaksiPersediaan::create([
                'barang_id' => $lockedBarang->id,
                'user_id' => $user->id,
                'jenis_transaksi' => 'masuk',
                'jumlah' => $jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_setelah' => $saldoSetelah,
                'tanggal_transaksi' => $meta['tanggal_transaksi'] ?? Carbon::now()->toDateString(),
                'nomor_dokumen' => $meta['nomor_dokumen'] ?? null,
                'sumber_atau_tujuan' => $meta['sumber_atau_tujuan'] ?? 'Vendor / Pengadaan',
                'status_spj' => $meta['status_spj'] ?? 'selesai',
                'keterangan' => $meta['keterangan'] ?? 'Penerimaan barang persediaan',
                'permintaan_id' => $meta['permintaan_id'] ?? null,
                'stok_opname_id' => $meta['stok_opname_id'] ?? null,
            ]);
        });
    }

    /**
     * Mengurangi stok barang persediaan (Serah Terima / Pengeluaran ATK).
     *
     * @param BarangPersediaan $barang
     * @param int $jumlah
     * @param User $user
     * @param array $meta
     * @return TransaksiPersediaan
     * @throws InsufficientStockException|InvalidArgumentException
     */
    public function kurangiStok(BarangPersediaan $barang, int $jumlah, User $user, array $meta = []): TransaksiPersediaan
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah pengeluaran stok harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($barang, $jumlah, $user, $meta) {
            /** @var BarangPersediaan $lockedBarang */
            $lockedBarang = BarangPersediaan::where('id', $barang->id)->lockForUpdate()->firstOrFail();

            $saldoSebelum = $lockedBarang->stok_aktif;

            if ($saldoSebelum < $jumlah) {
                throw new InsufficientStockException(
                    "Stok barang '{$lockedBarang->nama_barang}' tidak mencukupi (Tersedia: {$saldoSebelum}, Diminta: {$jumlah}).",
                    $saldoSebelum,
                    $jumlah
                );
            }

            $saldoSetelah = $saldoSebelum - $jumlah;
            $lockedBarang->stok_aktif = $saldoSetelah;
            $lockedBarang->save();

            return TransaksiPersediaan::create([
                'barang_id' => $lockedBarang->id,
                'user_id' => $user->id,
                'jenis_transaksi' => 'keluar',
                'jumlah' => -$jumlah, // Negatif sesuai skema ledger
                'saldo_sebelum' => $saldoSebelum,
                'saldo_setelah' => $saldoSetelah,
                'tanggal_transaksi' => $meta['tanggal_transaksi'] ?? Carbon::now()->toDateString(),
                'nomor_dokumen' => $meta['nomor_dokumen'] ?? null,
                'sumber_atau_tujuan' => $meta['sumber_atau_tujuan'] ?? 'Permintaan Divisi',
                'status_spj' => 'selesai',
                'keterangan' => $meta['keterangan'] ?? 'Pengeluaran barang persediaan',
                'permintaan_id' => $meta['permintaan_id'] ?? null,
                'stok_opname_id' => $meta['stok_opname_id'] ?? null,
            ]);
        });
    }

    /**
     * Melakukan koreksi stok fisik pasca Stock Opname.
     *
     * @param BarangPersediaan $barang
     * @param int $stokFisik
     * @param User $user
     * @param array $meta
     * @return TransaksiPersediaan|null
     * @throws InvalidArgumentException
     */
    public function koreksiStok(BarangPersediaan $barang, int $stokFisik, User $user, array $meta = []): ?TransaksiPersediaan
    {
        if ($stokFisik < 0) {
            throw new InvalidArgumentException("Stok fisik opname tidak boleh bernilai negatif.");
        }

        return DB::transaction(function () use ($barang, $stokFisik, $user, $meta) {
            /** @var BarangPersediaan $lockedBarang */
            $lockedBarang = BarangPersediaan::where('id', $barang->id)->lockForUpdate()->firstOrFail();

            $saldoSebelum = $lockedBarang->stok_aktif;
            $selisih = $stokFisik - $saldoSebelum;

            // Jika tidak ada selisih, tidak perlu mencatat transaksi koreksi
            if ($selisih === 0) {
                return null;
            }

            $saldoSetelah = $stokFisik;
            $lockedBarang->stok_aktif = $saldoSetelah;
            $lockedBarang->save();

            return TransaksiPersediaan::create([
                'barang_id' => $lockedBarang->id,
                'user_id' => $user->id,
                'jenis_transaksi' => 'penyesuaian',
                'jumlah' => $selisih,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_setelah' => $saldoSetelah,
                'tanggal_transaksi' => $meta['tanggal_transaksi'] ?? Carbon::now()->toDateString(),
                'nomor_dokumen' => $meta['nomor_dokumen'] ?? null,
                'sumber_atau_tujuan' => $meta['sumber_atau_tujuan'] ?? 'Penyesuaian Fisik (Opname)',
                'status_spj' => 'selesai',
                'keterangan' => $meta['keterangan'] ?? 'Penyesuaian stok opname',
                'permintaan_id' => $meta['permintaan_id'] ?? null,
                'stok_opname_id' => $meta['stok_opname_id'] ?? null,
            ]);
        });
    }

    /**
     * Memperbarui kuantitas dan data transaksi barang masuk serta menyinkronkan saldo stok.
     *
     * @param TransaksiPersediaan $transaksi
     * @param int $newJumlah
     * @param array $meta
     * @return TransaksiPersediaan
     * @throws InsufficientStockException|InvalidArgumentException
     */
    public function updateTransaksiMasuk(TransaksiPersediaan $transaksi, int $newJumlah, array $meta = []): TransaksiPersediaan
    {
        if ($transaksi->jenis_transaksi !== 'masuk') {
            throw new InvalidArgumentException("Hanya transaksi bertipe 'masuk' yang dapat diperbarui melalui metode ini.");
        }

        if ($newJumlah <= 0) {
            throw new InvalidArgumentException("Kuantitas barang masuk harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($transaksi, $newJumlah, $meta) {
            /** @var BarangPersediaan $lockedBarang */
            $lockedBarang = BarangPersediaan::where('id', $transaksi->barang_id)->lockForUpdate()->firstOrFail();

            $diff = $newJumlah - $transaksi->jumlah;

            // Jika kuantitas dikurangi, pastikan stok fisik tidak menjadi minus
            if ($diff < 0 && ($lockedBarang->stok_aktif + $diff) < 0) {
                throw new InsufficientStockException(
                    "Stok barang '{$lockedBarang->nama_barang}' saat ini ({$lockedBarang->stok_aktif}) tidak mencukupi untuk pengurangan ini karena barang telah digunakan/didistribusikan.",
                    $lockedBarang->stok_aktif,
                    abs($diff)
                );
            }

            // Sinkronkan stok aktif master barang
            $lockedBarang->stok_aktif += $diff;
            $lockedBarang->save();

            // Perbarui record transaksi
            $transaksi->jumlah = $newJumlah;
            $transaksi->saldo_setelah = $transaksi->saldo_sebelum + $newJumlah;

            if (isset($meta['nomor_dokumen'])) {
                $transaksi->nomor_dokumen = $meta['nomor_dokumen'];
            }
            if (isset($meta['tanggal_transaksi'])) {
                $transaksi->tanggal_transaksi = $meta['tanggal_transaksi'];
            }
            if (isset($meta['sumber_atau_tujuan'])) {
                $transaksi->sumber_atau_tujuan = $meta['sumber_atau_tujuan'];
            }
            if (isset($meta['status_spj'])) {
                $transaksi->status_spj = $meta['status_spj'];
            }
            if (array_key_exists('keterangan', $meta)) {
                $transaksi->keterangan = $meta['keterangan'];
            }

            $transaksi->save();

            return $transaksi;
        });
    }

    /**
     * Membatalkan / menghapus transaksi barang masuk dan mengembalikan saldo stok ke saldo awal.
     *
     * @param TransaksiPersediaan $transaksi
     * @return void
     * @throws InsufficientStockException|InvalidArgumentException
     */
    public function hapusTransaksiMasuk(TransaksiPersediaan $transaksi): void
    {
        if ($transaksi->jenis_transaksi !== 'masuk') {
            throw new InvalidArgumentException("Hanya transaksi bertipe 'masuk' yang dapat dibatalkan melalui metode ini.");
        }

        DB::transaction(function () use ($transaksi) {
            /** @var BarangPersediaan $lockedBarang */
            $lockedBarang = BarangPersediaan::where('id', $transaksi->barang_id)->lockForUpdate()->firstOrFail();

            // Pastikan stok mencukupi untuk ditarik kembali
            if ($lockedBarang->stok_aktif < $transaksi->jumlah) {
                throw new InsufficientStockException(
                    "Transaksi barang masuk tidak dapat dibatalkan karena stok fisik saat ini ({$lockedBarang->stok_aktif}) lebih kecil dari kuantitas penerimaan ({$transaksi->jumlah}) akibat telah didistribusikan ke pegawai.",
                    $lockedBarang->stok_aktif,
                    $transaksi->jumlah
                );
            }

            // Kembalikan saldo stok aktif master barang
            $lockedBarang->stok_aktif -= $transaksi->jumlah;
            $lockedBarang->save();

            $transaksi->delete();
        });
    }
}
