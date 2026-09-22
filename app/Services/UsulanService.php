<?php

namespace App\Services;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use App\Models\AnggotaKube;
use App\Models\DokumenArsip;
use App\Models\LogAuditTrail;
use App\Models\MasterStandarPagu;
use App\Models\Notifikasi;
use App\Models\UsulanPokir;
use Illuminate\Support\Str;

class UsulanService
{
    /**
     * Validate RAB against pagu for KUBE
     */
    public function validatePagu(string $bidangUsaha, float $totalRab, int $tahun): array
    {
        $pagu = MasterStandarPagu::where('bidang_usaha', $bidangUsaha)
            ->where('tahun_anggaran', $tahun)
            ->first();

        if (!$pagu) {
            return [
                'is_valid' => false,
                'message' => 'Pagu belum diatur untuk bidang usaha: ' . $bidangUsaha,
                'pagu_maksimal' => 0,
            ];
        }

        // Sum existing approved/cleared usulans for same bidang & year
        $existingSum = UsulanPokir::where('bidang_usaha', $bidangUsaha)
            ->where('tahun_anggaran', $tahun)
            ->whereIn('status', [UsulanStatus::CLEARED_RKA, UsulanStatus::FINAL_APPROVED])
            ->sum('total_rab');

        $totalWithExisting = $existingSum + $totalRab;
        $isValid = $totalWithExisting <= $pagu->pagu_maksimal;

        $message = $isValid
            ? 'RAB Rp ' . number_format($totalRab, 0, ',', '.') . ' dalam batas pagu'
            : 'RAB Rp ' . number_format($totalRab, 0, ',', '.') . ' melebihi pagu maksimal Rp ' . number_format($pagu->pagu_maksimal, 0, ',', '.');

        if ($existingSum > 0) {
            $message .= ' (sudah terpakai: Rp ' . number_format($existingSum, 0, ',', '.') . ')';
        }

        return [
            'is_valid' => $isValid,
            'pagu_maksimal' => $pagu->pagu_maksimal,
            'existing_sum' => $existingSum,
            'message' => $message,
        ];
    }

    /**
     * Validate rekening name matches KTP name
     */
    public function validateRekening(string $namaPemilik, string $namaKTP): array
    {
        $isValid = strtolower(trim($namaPemilik)) === strtolower(trim($namaKTP));

        return [
            'is_valid' => $isValid,
            'message' => $isValid
                ? 'Nama pemilik rekening sesuai dengan KTP'
                : 'Nama pemilik rekening TIDAK sesuai dengan KTP!',
        ];
    }

    /**
     * Submit usulan (change status from DRAFT/REVISI to REVIEW_DINAS)
     */
    public function submitUsulan(UsulanPokir $usulan): void
    {
        $oldStatus = $usulan->status;
        $usulan->update([
            'status' => UsulanStatus::REVIEW_DINAS,
            'catatan_verifikator' => null,
        ]);

        LogAuditTrail::log(
            action: 'SUBMIT_USULAN',
            entityType: UsulanPokir::class,
            entityId: $usulan->id,
            oldData: ['status' => $oldStatus->value],
            newData: ['status' => UsulanStatus::REVIEW_DINAS->value],
        );

        // Notifikasi ke verifikator
        $this->notifyVerifikator(
            usulan: $usulan,
            judul: 'Usulan Baru',
            pesan: "Usulan \"{$usulan->nama_kelompok_usaha}\" (#{$usulan->id}) menunggu verifikasi.",
            tipe: 'info'
        );
    }

    /**
     * Approve usulan
     */
    public function approveUsulan(UsulanPokir $usulan, ?string $catatan = null): void
    {
        $oldStatus = $usulan->status;
        $usulan->update([
            'status' => UsulanStatus::CLEARED_RKA,
            'catatan_verifikator' => $catatan,
        ]);

        LogAuditTrail::log(
            action: 'APPROVE_USULAN',
            entityType: UsulanPokir::class,
            entityId: $usulan->id,
            oldData: ['status' => $oldStatus->value],
            newData: ['status' => UsulanStatus::CLEARED_RKA->value, 'catatan' => $catatan],
        );

        $this->notifyUser(
            usulan: $usulan,
            judul: 'Usulan Disetujui (Clear RKA)',
            pesan: "Usulan \"{$usulan->nama_kelompok_usaha}\" (#{$usulan->id}) telah di-Clear RKA.",
            tipe: 'success'
        );
    }

    /**
     * Return usulan for revision
     */
    public function returnUsulan(UsulanPokir $usulan, string $catatan): void
    {
        $oldStatus = $usulan->status;
        $usulan->update([
            'status' => UsulanStatus::REVISI_UTUSAN,
            'catatan_verifikator' => $catatan,
        ]);

        LogAuditTrail::log(
            action: 'RETURN_USULAN',
            entityType: UsulanPokir::class,
            entityId: $usulan->id,
            oldData: ['status' => $oldStatus->value],
            newData: ['status' => UsulanStatus::REVISI_UTUSAN->value, 'catatan' => $catatan],
        );

        $this->notifyUser(
            usulan: $usulan,
            judul: 'Usulan Perlu Revisi',
            pesan: "Usulan \"{$usulan->nama_kelompok_usaha}\" (#{$usulan->id}) dikembalikan: {$catatan}",
            tipe: 'warning'
        );
    }

    /**
     * Final approve
     */
    public function finalApprove(UsulanPokir $usulan): void
    {
        $oldStatus = $usulan->status;
        $usulan->update(['status' => UsulanStatus::FINAL_APPROVED]);

        LogAuditTrail::log(
            action: 'FINAL_APPROVE',
            entityType: UsulanPokir::class,
            entityId: $usulan->id,
            oldData: ['status' => $oldStatus->value],
            newData: ['status' => UsulanStatus::FINAL_APPROVED->value],
        );

        $this->notifyUser(
            usulan: $usulan,
            judul: 'Usulan Final Disetujui',
            pesan: "Usulan \"{$usulan->nama_kelompok_usaha}\" (#{$usulan->id}) telah FINAL APPROVED.",
            tipe: 'success'
        );
    }

    // === Notification helpers ===

    private function notifyVerifikator(UsulanPokir $usulan, string $judul, string $pesan, string $tipe = 'info'): void
    {
        $recipients = \App\Models\User::whereIn('role', ['VERIFIKATOR_DINAS', 'ADMIN_PROV'])
            ->where('is_active', true)
            ->pluck('id');
        foreach ($recipients as $userId) {
            Notifikasi::create([
                'user_id' => $userId,
                'usulan_id' => $usulan->id,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
            ]);
        }
    }

    private function notifyUser(UsulanPokir $usulan, string $judul, string $pesan, string $tipe = 'info'): void
    {
        Notifikasi::create([
            'user_id' => $usulan->user_id,
            'usulan_id' => $usulan->id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
        ]);
    }

    /**
     * Get dashboard stats
     */
    public function getStats(int $tahun, ?int $userId = null): array
    {
        $query = UsulanPokir::whereYear('created_at', $tahun);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => (clone $query)->count(),
            'draft' => (clone $query)->status(UsulanStatus::DRAFT)->count(),
            'review' => (clone $query)->status(UsulanStatus::REVIEW_DINAS)->count(),
            'revisi' => (clone $query)->status(UsulanStatus::REVISI_UTUSAN)->count(),
            'cleared' => (clone $query)->status(UsulanStatus::CLEARED_RKA)->count(),
            'approved' => (clone $query)->status(UsulanStatus::FINAL_APPROVED)->count(),
            'kube' => (clone $query)->jenis(JenisBantuan::KUBE)->count(),
            'uep' => (clone $query)->jenis(JenisBantuan::UEP)->count(),
        ];
    }

    /**
     * Validate file size (max 5MB)
     */
    public function validateFileSize($file, int $maxBytes = 5 * 1024 * 1024): bool
    {
        return $file && $file->getSize() <= $maxBytes;
    }

    /**
     * Validate Indonesian phone number format (08xxxxxxxxxx)
     */
    public function validatePhone(string $phone): bool
    {
        return (bool) preg_match('/^0[0-9]{9,12}$/', $phone);
    }

    /**
     * Validate file MIME type and extension
     */
    public function validateFileUpload($file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($mimeType, $allowedMimes) && in_array($extension, $allowedExts);
    }

    /**
     * Upload dokumen usulan to local storage + GDrive
     */
    public function uploadDokumen(int $usulanId, string $jenisDokumen, $file): void
    {
        if (!$this->validateFileUpload($file)) {
            abort(422, 'Tipe file tidak diizinkan. Hanya PDF, JPG, dan PNG yang diperbolehkan.');
        }

        // 1. Local upload
        $safeName = Str::slug($jenisDokumen);
        $filename = $safeName . '_' . $usulanId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/usulan/' . $usulanId, $filename, 'public');

        // 2. GDrive upload (if configured)
        $gdriveFileId = null;
        $gdriveLink = null;

        $gdriveService = new GdriveService();
        if ($gdriveService->isConfigured()) {
            $usulan = \App\Models\UsulanPokir::find($usulanId);
            $gdriveResult = $gdriveService->upload($file, [
                'tahunAnggaran' => $usulan->tahun_anggaran ?? config('app.tahun_anggaran'),
                'jenisBantuan'  => $usulan->jenis_bantuan?->value ?? 'KUBE',
                'usulanId'      => $usulanId,
                'jenisDokumen'  => $jenisDokumen,
            ]);

            if ($gdriveResult['success']) {
                $gdriveFileId = $gdriveResult['file_id'];
                $gdriveLink = $gdriveResult['link'];
            } else {
                \Illuminate\Support\Facades\Log::warning('GDrive upload failed for usulan #' . $usulanId . ': ' . ($gdriveResult['error'] ?? 'unknown'));
            }
        }

        DokumenArsip::create([
            'usulan_id'      => $usulanId,
            'jenis_dokumen'  => $jenisDokumen,
            'nama_file'      => $file->getClientOriginalName(),
            'file_path'      => $path,
            'file_size'      => $file->getSize(),
            'mime_type'      => $file->getMimeType(),
            'gdrive_file_id' => $gdriveFileId,
            'gdrive_link'    => $gdriveLink,
        ]);
    }

    /**
     * Delete dokumen by jenis_dokumen (for edit/replace) — local + GDrive
     */
    public function deleteDokumenByJenis(int $usulanId, string $jenisDokumen): void
    {
        $docs = DokumenArsip::where('usulan_id', $usulanId)
            ->where('jenis_dokumen', $jenisDokumen)
            ->get();

        $gdriveService = new GdriveService();

        foreach ($docs as $doc) {
            // Delete from GDrive
            if ($doc->gdrive_file_id && $gdriveService->isConfigured()) {
                $gdriveService->delete($doc->gdrive_file_id);
            }

            // Delete local file
            $fullPath = storage_path('app/public/' . $doc->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $doc->delete();
        }
    }

    /**
     * Delete all anggota for a usulan (for edit/replace)
     */
    public function deleteAnggotaByUsulan(int $usulanId): void
    {
        AnggotaKube::where('usulan_id', $usulanId)->delete();
    }
}
