<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenArsip extends Model
{
    protected $table = 'dokumen_arsip';

    protected $fillable = [
        'usulan_id',
        'jenis_dokumen',
        'nama_file',
        'file_path',
        'file_size',
        'mime_type',
        'gdrive_file_id',
        'gdrive_link',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(UsulanPokir::class, 'usulan_id');
    }

    public function fileUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
