<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'usulan_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(UsulanPokir::class, 'usulan_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public static function unreadCount(int $userId): int
    {
        return static::forUser($userId)->unread()->count();
    }

    public static function markAllRead(int $userId): void
    {
        static::forUser($userId)->unread()->update(['is_read' => true]);
    }
}
