<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterWilayah extends Model
{
    protected $table = 'master_wilayah';

    protected $fillable = [
        'kabupaten',
        'kecamatan',
    ];

    public function scopeKabupaten($query, string $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }
}
