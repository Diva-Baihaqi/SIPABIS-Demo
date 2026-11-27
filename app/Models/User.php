<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'role',
        'ruangan_id',
        'jabatan',
        'golongan',
        'status_pegawai',
        'jenis_kelamin',
        'no_hp',
        'is_active',
    ];

    /**
     * Memeriksa apakah pegawai berjenis kelamin perempuan.
     * Menggunakan kolom jenis_kelamin, dengan fallback digit ke-15 NIP 18 digit.
     */
    public function isPerempuan(): bool
    {
        if ($this->jenis_kelamin === 'P') {
            return true;
        }
        if ($this->jenis_kelamin === 'L') {
            return false;
        }
        return strlen($this->nip) === 18 && substr($this->nip, 14, 1) === '2';
    }

    /**
     * Mendapatkan URL foto profil resmi berdasarkan gender (man.png / girl.png).
     */
    public function getFotoUrlAttribute(): string
    {
        return asset($this->isPerempuan() ? 'images/girl.png' : 'images/man.png');
    }

    /**
     * Label teks jenis kelamin resmi.
     */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->isPerempuan() ? 'Perempuan' : 'Laki-laki';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function permintaan(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'user_id');
    }

    public function transaksiPersediaan(): HasMany
    {
        return $this->hasMany(TransaksiPersediaan::class, 'user_id');
    }

    public function stokOpname(): HasMany
    {
        return $this->hasMany(StokOpname::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }
}
