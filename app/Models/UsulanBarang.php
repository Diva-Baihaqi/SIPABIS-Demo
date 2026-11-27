<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanBarang extends Model
{
    use HasFactory;

    protected $table = 'usulan_barang';

    protected $fillable = [
        'user_id',
        'ruangan_id',
        'nama_barang_usulan',
        'perkiraan_satuan',
        'estimasi_kebutuhan',
        'alasan_kebutuhan',
        'foto',
        'status',
        'catatan_tindak_lanjut',
    ];

    protected $appends = [
        'foto_url',
    ];

    /**
     * URL foto lampiran usulan barang jika tersedia di disk.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!empty($this->foto) && file_exists(public_path($this->foto))) {
            return asset($this->foto);
        }

        return null;
    }

    protected function casts(): array
    {
        return [
            'estimasi_kebutuhan' => 'integer',
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
}
