<?php

namespace App\Models;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UsulanPokir extends Model
{
    protected $table = 'usulan_pokir';

    protected $fillable = [
        'user_id',
        'anggota_dewan_id',
        'tahun_anggaran',
        'tahun_asal_proposal',
        'jenis_bantuan',
        'nama_kelompok_usaha',
        'sektor_usaha',
        'sektor_usaha_lainnya',
        'jumlah_anggota',
        'nama_ketua_individu',
        'nik',
        'no_kk_ketua',
        'no_hp',
        'desil',
        'is_desil_valid',
        // Sekretaris
        'nik_sekretaris',
        'no_kk_sekretaris',
        'nama_sekretaris',
        'no_hp_sekretaris',
        'desil_sekretaris',
        'is_desil_sekretaris_valid',
        // Bendahara
        'nik_bendahara',
        'no_kk_bendahara',
        'nama_bendahara',
        'no_hp_bendahara',
        'desil_bendahara',
        'is_desil_bendahara_valid',
        // Lokasi
        'alamat_lengkap',
        'kabupaten',
        'kecamatan',
        'desa_kelurahan',
        'alamat_detail',
        'latitude',
        'longitude',
        'link_google_maps',
        // Bidang & Pagu
        'bidang_usaha',
        'total_rab',
        'no_nib',
        // Status
        'status',
        'catatan_verifikator',
    ];

    protected function casts(): array
    {
        return [
            'jenis_bantuan' => JenisBantuan::class,
            'status' => UsulanStatus::class,
            'tahun_anggaran' => 'integer',
            'tahun_asal_proposal' => 'integer',
            'desil' => 'integer',
            'is_desil_valid' => 'boolean',
            'desil_sekretaris' => 'integer',
            'is_desil_sekretaris_valid' => 'boolean',
            'desil_bendahara' => 'integer',
            'is_desil_bendahara_valid' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'total_rab' => 'decimal:2',
        ];
    }

    // Relationships
    public function anggotaDewan(): BelongsTo
    {
        return $this->belongsTo(MasterAnggotaDewan::class, 'anggota_dewan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rekening(): HasOne
    {
        return $this->hasOne(DataRekeningUep::class, 'usulan_id');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenArsip::class, 'usulan_id');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaKube::class, 'usulan_id');
    }

    // Helpers
    public function isKube(): bool
    {
        return $this->jenis_bantuan === JenisBantuan::KUBE;
    }

    public function isUep(): bool
    {
        return $this->jenis_bantuan === JenisBantuan::UEP;
    }

    public function isDraft(): bool
    {
        return $this->status === UsulanStatus::DRAFT;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [
            UsulanStatus::DRAFT,
            UsulanStatus::REVISI_UTUSAN,
        ]);
    }

    public function canBeVerified(): bool
    {
        return $this->status === UsulanStatus::REVIEW_DINAS;
    }

    // Scopes
    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_anggaran', $tahun);
    }

    public function scopeTahunPengajuan($query, int $tahun)
    {
        return $query->whereYear('created_at', $tahun);
    }

    public function scopeJenis($query, JenisBantuan $jenis)
    {
        return $query->where('jenis_bantuan', $jenis);
    }

    public function scopeStatus($query, UsulanStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForKanban($query)
    {
        return $query->whereIn('status', [
            UsulanStatus::REVIEW_DINAS,
            UsulanStatus::REVISI_UTUSAN,
            UsulanStatus::CLEARED_RKA,
        ]);
    }
}
