<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterStandarPagu extends Model
{
    protected $table = 'master_standar_pagu';

    protected $fillable = [
        'tahun_anggaran',
        'bidang_usaha',
        'pagu_maksimal',
    ];

    protected function casts(): array
    {
        return [
            'pagu_maksimal' => 'decimal:2',
            'tahun_anggaran' => 'integer',
        ];
    }

    public function usulan(): HasMany
    {
        return $this->hasMany(UsulanPokir::class, 'bidang_usaha', 'bidang_usaha')
            ->where('tahun_anggaran', $this->tahun_anggaran);
    }

    // Scope: filter by tahun
    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_anggaran', $tahun);
    }
}
