<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'nomor_permintaan',
        'user_id',
        'ruangan_id',
        'tanggal_pengajuan',
        'status',
        'verifikator_id',
        'tanggal_verifikasi',
        'tanggal_serah_terima',
        'catatan_pemohon',
        'catatan_admin',
        'file_bukti_serah_terima',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_verifikasi' => 'datetime',
            'tanggal_serah_terima' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PermintaanDetail::class, 'permintaan_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiPersediaan::class, 'permintaan_id');
    }
}
