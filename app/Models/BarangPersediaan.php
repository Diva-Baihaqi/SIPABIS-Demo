<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BarangPersediaan extends Model
{
    use HasFactory;

    protected $table = 'barang_persediaan';

    protected $fillable = [
        'kode_barang',
        'kode_sakti',
        'kategori_id',
        'nama_barang',
        'satuan',
        'stok_minimum',
        'stok_aktif',
        'harga_satuan_estimasi',
        'lokasi_rak',
        'keterangan',
        'foto',
    ];

    protected $appends = [
        'foto_url',
        'has_real_foto',
    ];

    protected function casts(): array
    {
        return [
            'stok_minimum' => 'integer',
            'stok_aktif' => 'integer',
            'harga_satuan_estimasi' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function transaksiPersediaan(): HasMany
    {
        return $this->hasMany(TransaksiPersediaan::class, 'barang_id');
    }

    public function permintaanDetail(): HasMany
    {
        return $this->hasMany(PermintaanDetail::class, 'barang_id');
    }

    public function stokOpnameDetail(): HasMany
    {
        return $this->hasMany(StokOpnameDetail::class, 'barang_id');
    }

    public function isStokKritis(): bool
    {
        return $this->stok_aktif <= $this->stok_minimum;
    }

    /**
     * Format resmi Kode Barang BMN SAKTI (contoh: 1.01.03.01.001.000001).
     */
    public function getKodeBmnLengkapAttribute(): string
    {
        if (empty($this->kode_sakti)) {
            return $this->kode_barang ?? '-';
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $this->kode_sakti);
        if (strlen($digits) === 10) {
            $formattedSakti = sprintf(
                '%s.%s.%s.%s.%s',
                substr($digits, 0, 1),
                substr($digits, 1, 2),
                substr($digits, 3, 2),
                substr($digits, 5, 2),
                substr($digits, 7, 3)
            );
        } else {
            $formattedSakti = (string) $this->kode_sakti;
        }

        return $formattedSakti . '.' . str_pad((string) $this->kode_barang, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah barang memiliki foto fisik yang terunggah di server.
     */
    public function getHasRealFotoAttribute(): bool
    {
        return !empty($this->foto) && file_exists(public_path($this->foto));
    }

    /**
     * URL foto produk barang persediaan, otomatis fallback ke placeholder kategori jika belum ada foto.
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->has_real_foto) {
            return asset($this->foto);
        }

        $kategori = $this->relationLoaded('kategori') ? $this->getRelation('kategori') : $this->kategori;
        $slug = $kategori ? Str::slug($kategori->nama_kategori) : 'umum';
        $placeholderPath = "images/placeholders/{$slug}.svg";

        if (file_exists(public_path($placeholderPath))) {
            return asset($placeholderPath);
        }

        if (file_exists(public_path('images/placeholders/default.svg'))) {
            return asset('images/placeholders/default.svg');
        }

        return asset('images/kategori/barang.png');
    }
}
