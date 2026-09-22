<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'anggota_dewan_id',
        'no_hp',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function anggotaDewan(): BelongsTo
    {
        return $this->belongsTo(MasterAnggotaDewan::class, 'anggota_dewan_id');
    }

    public function usulan(): HasMany
    {
        return $this->hasMany(UsulanPokir::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(LogAuditTrail::class);
    }

    // Helpers
    public function isUtusanDewan(): bool
    {
        return $this->role === UserRole::UTUSAN_DEWAN;
    }

    public function isVerifikator(): bool
    {
        return $this->role === UserRole::VERIFIKATOR_DINAS;
    }

    public function isAdminProv(): bool
    {
        return $this->role === UserRole::ADMIN_PROV;
    }

    public function canVerify(): bool
    {
        return $this->isVerifikator() || $this->isAdminProv();
    }
}
