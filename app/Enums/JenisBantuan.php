<?php

namespace App\Enums;

enum JenisBantuan: string
{
    case KUBE = 'KUBE';
    case UEP = 'UEP';

    public function label(): string
    {
        return match ($this) {
            self::KUBE => 'KUBE (Kelompok Usaha Bersama)',
            self::UEP => 'UEP (Usaha Ekonomi Produktif)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::KUBE => 'Bantuan berupa barang/modal kerja untuk kelompok',
            self::UEP => 'Bantuan Tunai (Cash Transfer) untuk individu/UMKM',
        };
    }
}
