<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_detail';

    protected $fillable = [
        'permintaan_id',
        'barang_id',
        'jumlah_diminta',
        'jumlah_disetujui',
        'catatan_item',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_diminta' => 'integer',
            'jumlah_disetujui' => 'integer',
        ];
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangPersediaan::class, 'barang_id');
    }
}
