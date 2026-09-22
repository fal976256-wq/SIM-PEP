<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDesilSen extends Model
{
    protected $table = 'master_desil_dtsen';

    protected $fillable = [
        'nik',
        'nama',
        'desil',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        'tahun_sync',
    ];

    protected function casts(): array
    {
        return [
            'desil' => 'integer',
            'tahun_sync' => 'integer',
        ];
    }

    // Static: check desil by NIK
    public static function getDesilByNik(string $nik, int $tahun): ?int
    {
        $record = static::where('nik', $nik)
            ->where('tahun_sync', $tahun)
            ->first();

        return $record?->desil;
    }

    public static function isValidForBantuan(string $nik, int $tahun): bool
    {
        $desil = static::getDesilByNik($nik, $tahun);
        return $desil !== null && $desil >= 1 && $desil <= 4;
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_sync', $tahun);
    }
}
