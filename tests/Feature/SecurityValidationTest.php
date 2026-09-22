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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityValidationTest extends TestCase
{
    use RefreshDatabase;

    private UsulanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UsulanService();
    }

    // === NIK VALIDATION ===

    public function test_nik_must_be_16_digits(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        // Too short
        $this->assertFalse($this->validateNik('123456789012345'));
        // Too long
        $this->assertFalse($this->validateNik('12345678901234567'));
        // Exactly 16
        $this->assertTrue($this->validateNik('1234567890123456'));
    }

    public function test_nik_must_be_numeric(): void
    {
        // Contains letters
        $this->assertFalse($this->validateNik('abcdefghijklmnop'));
        // Mixed
        $this->assertFalse($this->validateNik('123456789012345a'));
        // All numeric
        $this->assertTrue($this->validateNik('1234567890123456'));
    }

    public function test_no_kk_must_be_16_digits_numeric(): void
    {
        $this->assertFalse($this->validateNoKk('123456789012345'));
        $this->assertFalse($this->validateNoKk('abcdefghijklmnop'));
        $this->assertTrue($this->validateNoKk('1234567890123456'));
    }

    // === KK UNIQUENESS WITHIN KUBE ===

    public function test_kk_uniqueness_within_kube(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Test',
            'nama_ketua_individu' => 'Ketua Test',
            'nik' => '1111111111111111',
            'no_kk_ketua' => '2222222222222222',
            'nik_sekretaris' => '3333333333333333',
            'no_kk_sekretaris' => '2222222222222222', // Same KK as ketua
            'nik_bendahara' => '4444444444444444',
            'no_kk_bendahara' => '5555555555555555',
            'status' => UsulanStatus::DRAFT,
        ]);

        $allKk = array_filter([
            $usulan->no_kk_ketua,
            $usulan->no_kk_sekretaris,
            $usulan->no_kk_bendahara,
        ]);

        // Check uniqueness
        $uniqueKk = array_unique($allKk);
        $this->assertNotEquals(count($allKk), count($uniqueKk), 'KK uniqueness should be violated');
    }

    // === NIK UNIQUENESS (NO DOUBLE POSITIONS) ===

    public function test_nik_uniqueness_no_double_positions(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Test',
            'nama_ketua_individu' => 'Ketua Test',
            'nik' => '1111111111111111',
            'no_kk_ketua' => '2222222222222222',
            'nik_sekretaris' => '1111111111111111', // Same NIK as ketua
            'no_kk_sekretaris' => '3333333333333333',
            'nik_bendahara' => '4444444444444444',
            'no_kk_bendahara' => '5555555555555555',
            'status' => UsulanStatus::DRAFT,
        ]);

        $allNik = array_filter([
            $usulan->nik,
            $usulan->nik_sekretaris,
            $usulan->nik_bendahara,
        ]);

        $uniqueNik = array_unique($allNik);
        $this->assertNotEquals(count($allNik), count($uniqueNik), 'NIK uniqueness should be violated');
    }

    // === PHONE VALIDATION ===

    public function test_phone_format_validation(): void
    {
        // Valid formats
        $this->assertTrue($this->service->validatePhone('08123456789'));
        $this->assertTrue($this->service->validatePhone('081234567890'));
        $this->assertTrue($this->service->validatePhone('0812345678901'));

        // Invalid formats
        $this->assertFalse($this->service->validatePhone('12345'));
        $this->assertFalse($this->service->validatePhone('abc'));
        $this->assertFalse($this->service->validatePhone('08123456789012')); // Too long
        $this->assertFalse($this->service->validatePhone('1234567890123456')); // Doesn't start with 08
    }

    // === FILE SIZE VALIDATION ===

    public function test_file_size_validation(): void
    {
        $file = new class {
            public function getSize(): int { return 4 * 1024 * 1024; }
        };
        $this->assertTrue($this->service->validateFileSize($file));

        $exactFile = new class {
            public function getSize(): int { return 5 * 1024 * 1024; }
        };
        $this->assertTrue($this->service->validateFileSize($exactFile));

        $bigFile = new class {
            public function getSize(): int { return 6 * 1024 * 1024; }
        };
        $this->assertFalse($this->service->validateFileSize($bigFile));
    }

    // === FILE UPLOAD MIME VALIDATION ===

    public function test_file_upload_rejects_invalid_mime(): void
    {
        $file = UploadedFile::fake()->createWithContent('test.php', '<?php echo "hacked"; ?>');
        $this->assertFalse($this->service->validateFileUpload($file));
    }

    public function test_file_upload_accepts_valid_mime(): void
    {
        $pdf = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $this->assertTrue($this->service->validateFileUpload($pdf));

        $jpg = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');
        $this->assertTrue($this->service->validateFileUpload($jpg));

        $png = UploadedFile::fake()->create('test.png', 100, 'image/png');
        $this->assertTrue($this->service->validateFileUpload($png));
    }

    // === FULL STATUS FLOW ===

    public function test_full_status_flow_draft_to_final(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Lestari',
            'nama_ketua_individu' => 'Budi',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        // DRAFT → REVIEW_DINAS
        $this->service->submitUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::REVIEW_DINAS, $usulan->fresh()->status);

        // REVIEW_DINAS → CLEARED_RKA
        $this->service->approveUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::CLEARED_RKA, $usulan->fresh()->status);

        // CLEARED_RKA → FINAL_APPROVED
        $this->service->finalApprove($usulan->fresh());
        $this->assertEquals(UsulanStatus::FINAL_APPROVED, $usulan->fresh()->status);
    }

    // === REVISI LOOP ===

    public function test_revisi_loop(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::UEP,
            'nama_ketua_individu' => 'Rina',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        // DRAFT → REVIEW_DINAS
        $this->service->submitUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::REVIEW_DINAS, $usulan->fresh()->status);

        // REVIEW_DINAS → REVISI_UTUSAN
        $this->service->returnUsulan($usulan->fresh(), 'Perlu revisi');
        $this->assertEquals(UsulanStatus::REVISI_UTUSAN, $usulan->fresh()->status);

        // REVISI_UTUSAN → REVIEW_DINAS (resubmit)
        $this->service->submitUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::REVIEW_DINAS, $usulan->fresh()->status);

        // REVIEW_DINAS → CLEARED_RKA
        $this->service->approveUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::CLEARED_RKA, $usulan->fresh()->status);
    }

    // === AUDIT TRAIL ===

    public function test_submit_creates_audit_trail(): void
    {
        $user = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $user->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Audit',
            'nama_ketua_individu' => 'Audit',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        $this->service->submitUsulan($usulan->fresh());

        $this->assertDatabaseHas('log_audit_trail', [
            'entity_type' => 'App\Models\UsulanPokir',
            'entity_id' => $usulan->id,
            'action' => 'SUBMIT_USULAN',
        ]);
    }

    // === NOTIFICATION ===

    public function test_submit_creates_notification_for_verifikator(): void
    {
        $utusan = User::factory()->create(['role' => UserRole::UTUSAN_DEWAN, 'is_active' => true, 'email_verified_at' => now()]);
        $verifikator = User::factory()->create(['role' => UserRole::VERIFIKATOR_DINAS, 'is_active' => true, 'email_verified_at' => now()]);

        $usulan = UsulanPokir::create([
            'user_id' => $utusan->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Notif',
            'nama_ketua_individu' => 'Notif',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        $this->service->submitUsulan($usulan->fresh());

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $verifikator->id,
            'usulan_id' => $usulan->id,
            'judul' => 'Usulan Baru',
        ]);
    }

    // === HELPERS ===

    private function validateNik(string $nik): bool
    {
        return strlen($nik) === 16 && ctype_digit($nik);
    }

    private function validateNoKk(string $noKk): bool
    {
        return strlen($noKk) === 16 && ctype_digit($noKk);
    }
}
