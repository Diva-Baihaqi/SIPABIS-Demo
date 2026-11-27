<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $table = 'kategori_barang';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
    ];

    public function barang(): HasMany
    {
        return $this->hasMany(BarangPersediaan::class, 'kategori_id');
    }

    /**
     * Nama subfolder aman untuk penyimpanan foto barang per kategori.
     */
    public function getFolderSlugAttribute(): string
    {
        return Str::slug($this->nama_kategori);
    }
}
