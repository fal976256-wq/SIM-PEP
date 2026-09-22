<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataRekeningUep extends Model
{
    protected $table = 'data_rekening_uep';

    protected $fillable = [
        'usulan_id',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(UsulanPokir::class, 'usulan_id');
    }
}
