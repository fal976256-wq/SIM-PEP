<?php

namespace App\Enums;

enum UserRole: string
{
    case UTUSAN_DEWAN = 'UTUSAN_DEWAN';
    case VERIFIKATOR_DINAS = 'VERIFIKATOR_DINAS';
    case ADMIN_PROV = 'ADMIN_PROV';

    public function label(): string
    {
        return match ($this) {
            self::UTUSAN_DEWAN => 'Utusan Dewan',
            self::VERIFIKATOR_DINAS => 'Verifikator Dinas',
            self::ADMIN_PROV => 'Admin Provinsi',
        };
    }
}
