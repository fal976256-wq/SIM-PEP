<?php

namespace Tests\Feature;

use App\Enums\JenisBantuan;
use App\Enums\UserRole;
use App\Enums\UsulanStatus;
use App\Models\MasterStandarPagu;
use App\Models\User;
use App\Models\UsulanPokir;
use App\Services\UsulanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsulanTest extends TestCase
{
    use RefreshDatabase;

    private UsulanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UsulanService();
    }

    public function test_pagu_validation(): void
    {
        MasterStandarPagu::create([
            'tahun_anggaran' => 2027,
            'bidang_usaha' => 'Pertanian',
            'pagu_maksimal' => 50000000,
        ]);

        // Under pagu
        $result = $this->service->validatePagu('Pertanian', 40000000, 2027);
        $this->assertTrue($result['is_valid']);

        // Over pagu
        $result = $this->service->validatePagu('Pertanian', 60000000, 2027);
        $this->assertFalse($result['is_valid']);
    }

    public function test_rekening_validation(): void
    {
        // Match
        $result = $this->service->validateRekening('Budi Santoso', 'Budi Santoso');
        $this->assertTrue($result['is_valid']);

        // Case insensitive
        $result = $this->service->validateRekening('budi santoso', 'Budi Santoso');
        $this->assertTrue($result['is_valid']);

        // Mismatch
        $result = $this->service->validateRekening('Rina Wati', 'Budi Santoso');
        $this->assertFalse($result['is_valid']);
    }

    public function test_submit_usulan(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);
        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Maju Jaya',
            'nama_ketua_individu' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        $this->service->submitUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::REVIEW_DINAS, $usulan->fresh()->status);
    }

    public function test_approve_usulan(): void
    {
        $user = User::factory()->create(['role' => UserRole::VERIFIKATOR_DINAS, 'is_active' => true, 'email_verified_at' => now()]);
        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::UEP,
            'nama_ketua_individu' => 'Rina Wati',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::REVIEW_DINAS,
        ]);

        $this->service->approveUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::CLEARED_RKA, $usulan->fresh()->status);
    }

    public function test_return_usulan(): void
    {
        $user = User::factory()->create(['role' => UserRole::VERIFIKATOR_DINAS, 'is_active' => true, 'email_verified_at' => now()]);
        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Test',
            'nama_ketua_individu' => 'Test User',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::REVIEW_DINAS,
        ]);

        $this->service->returnUsulan($usulan->fresh(), 'Dokumen tidak lengkap');
        $this->assertEquals(UsulanStatus::REVISI_UTUSAN, $usulan->fresh()->status);
        $this->assertEquals('Dokumen tidak lengkap', $usulan->fresh()->catatan_verifikator);
    }

    public function test_final_approve(): void
    {
        $user = User::factory()->create(['role' => UserRole::VERIFIKATOR_DINAS, 'is_active' => true, 'email_verified_at' => now()]);
        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::UEP,
            'nama_ketua_individu' => 'Test User',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::CLEARED_RKA,
        ]);

        $this->service->finalApprove($usulan->fresh());
        $this->assertEquals(UsulanStatus::FINAL_APPROVED, $usulan->fresh()->status);
    }

    public function test_dashboard_stats(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'Test KUBE',
            'nama_ketua_individu' => 'Test',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        $stats = $this->service->getStats((int) date('Y'), $user->id);
        $this->assertEquals(1, $stats['total']);
        $this->assertEquals(1, $stats['draft']);
        $this->assertEquals(1, $stats['kube']);
    }
}
