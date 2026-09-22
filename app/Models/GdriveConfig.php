<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdriveConfig extends Model
{
    protected $table = 'gdrive_config';

    protected $fillable = [
        'folder_id',
        'folder_name',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_anggaran', $tahun);
    }
}
