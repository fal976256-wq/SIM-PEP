<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_bantuan',
        'current_step',
        'form_data',
        'file_paths',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'file_paths' => 'array',
            'current_step' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
