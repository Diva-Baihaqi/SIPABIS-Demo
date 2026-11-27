<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    protected $fillable = [
        'nomor_opname',
        'tanggal_opname',
        'periode',
        'status',
        'user_id',
        'pimpinan_id',
        'catatan_kesimpulan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_opname' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StokOpnameDetail::class, 'stok_opname_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiPersediaan::class, 'stok_opname_id');
    }
}
