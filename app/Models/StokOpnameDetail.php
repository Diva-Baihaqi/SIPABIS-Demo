<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'stok_opname_detail';

    protected $fillable = [
        'stok_opname_id',
        'barang_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan_selisih',
    ];

    protected function casts(): array
    {
        return [
            'stok_sistem' => 'integer',
            'stok_fisik' => 'integer',
            'selisih' => 'integer',
        ];
    }

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(StokOpname::class, 'stok_opname_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangPersediaan::class, 'barang_id');
    }
}
