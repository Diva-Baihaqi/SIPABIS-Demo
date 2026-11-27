<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'penanggung_jawab',
        'nip_penanggung_jawab',
        'keterangan',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'ruangan_id');
    }

    public function permintaan(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'ruangan_id');
    }

    public function usulanBarang(): HasMany
    {
        return $this->hasMany(UsulanBarang::class, 'ruangan_id');
    }
}
