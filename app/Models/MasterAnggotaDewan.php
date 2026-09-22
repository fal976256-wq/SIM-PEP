<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAnggotaDewan extends Model
{
    protected $table = 'master_anggota_dewan';

    protected $fillable = [
        'dapil',
        'kabupaten',
        'nama',
        'partai',
        'periode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'anggota_dewan_id');
    }

    public function usulan(): HasMany
    {
        return $this->hasMany(UsulanPokir::class, 'anggota_dewan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDapil($query, string $dapil)
    {
        return $query->where('dapil', $dapil);
    }
}
