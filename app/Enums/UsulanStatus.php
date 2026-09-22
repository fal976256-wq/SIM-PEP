<?php

namespace App\Enums;

enum UsulanStatus: string
{
    case DRAFT = 'DRAFT';
    case REVIEW_DINAS = 'REVIEW_DINAS';
    case REVISI_UTUSAN = 'REVISI_UTUSAN';
    case CLEARED_RKA = 'CLEARED_RKA';
    case FINAL_APPROVED = 'FINAL_APPROVED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::REVIEW_DINAS => 'Review Dinas',
            self::REVISI_UTUSAN => 'Revisi Utusan',
            self::CLEARED_RKA => 'Cleared RKA',
            self::FINAL_APPROVED => 'Final Disetujui',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::REVIEW_DINAS => 'blue',
            self::REVISI_UTUSAN => 'yellow',
            self::CLEARED_RKA => 'orange',
            self::FINAL_APPROVED => 'green',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-gray-100 text-gray-800',
            self::REVIEW_DINAS => 'bg-blue-100 text-blue-800',
            self::REVISI_UTUSAN => 'bg-yellow-100 text-yellow-800',
            self::CLEARED_RKA => 'bg-orange-100 text-orange-800',
            self::FINAL_APPROVED => 'bg-green-100 text-green-800',
        };
    }
}
