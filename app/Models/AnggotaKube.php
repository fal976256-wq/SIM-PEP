<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKube extends Model
{
    protected $table = 'anggota_kube';

    protected $fillable = [
        'usulan_id',
        'nik',
        'no_kk',
        'nama',
        'no_hp',
        'desil',
        'is_desil_valid',
    ];

    protected function casts(): array
    {
        return [
            'desil' => 'integer',
            'is_desil_valid' => 'boolean',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(UsulanPokir::class, 'usulan_id');
    }
}
