<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPersediaan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_persediaan';

    protected $fillable = [
        'barang_id',
        'user_id',
        'jenis_transaksi',
        'jumlah',
        'saldo_sebelum',
        'saldo_setelah',
        'tanggal_transaksi',
        'nomor_dokumen',
        'sumber_atau_tujuan',
        'status_spj',
        'keterangan',
        'permintaan_id',
        'stok_opname_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'jumlah' => 'integer',
            'saldo_sebelum' => 'integer',
            'saldo_setelah' => 'integer',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangPersediaan::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(StokOpname::class, 'stok_opname_id');
    }
}
