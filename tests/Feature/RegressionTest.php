<?php

namespace Tests\Feature;

use App\Enums\JenisBantuan;
use App\Enums\UserRole;
use App\Enums\UsulanStatus;
use App\Models\DataRekeningUep;
use App\Models\User;
use App\Models\UsulanPokir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegressionTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(UserRole $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * GdriveConfig Livewire component must be registered —
     * /admin/gdrive must render 200 for admin (regression: was 500).
     */
    public function test_admin_gdrive_page_renders(): void
    {
        $admin = $this->makeUser(UserRole::ADMIN_PROV);

        $this->actingAs($admin)
            ->get(route('admin.gdrive'))
            ->assertOk();
    }

    /**
     * Non-admin must be blocked from /admin/gdrive (403).
     */
    public function test_gdrive_page_blocks_non_admin(): void
    {
        $verifikator = $this->makeUser(UserRole::VERIFIKATOR_DINAS);

        $this->actingAs($verifikator)
            ->get(route('admin.gdrive'))
            ->assertForbidden();
    }

    /**
     * Edit mode UEP uses the 'rekening' relation (regression:
     * loadFromUsulan() referenced non-existent 'rekeningUep' relation).
     */
    public function test_uep_edit_page_renders_with_rekening(): void
    {
        $utusan = $this->makeUser(UserRole::UTUSAN_DEWAN);

        $usulan = UsulanPokir::create([
            'user_id' => $utusan->id,
            'tahun_anggaran' => 2027,
            'tahun_asal_proposal' => 2025,
            'jenis_bantuan' => JenisBantuan::UEP,
            'nama_kelompok_usaha' => 'Toko Berkah',
            'nama_ketua_individu' => 'Rina Wati',
            'nik' => '7301010101010011',
            'no_hp' => '081234567890',
            'desil' => 2,
            'kabupaten' => 'Majene',
            'kecamatan' => 'Banggae',
            'desa_kelurahan' => 'Baru',
            'alamat_detail' => 'Jl. Test No. 1',
            'latitude' => -3.1234567,
            'longitude' => 118.7654321,
            'bidang_usaha' => 'Perdagangan',
            'status' => UsulanStatus::REVISI_UTUSAN,
        ]);

        DataRekeningUep::create([
            'usulan_id' => $usulan->id,
            'nama_bank' => 'BSI',
            'nomor_rekening' => '1234567890',
            'nama_pemilik_rekening' => 'Rina Wati',
        ]);

        $this->actingAs($utusan)
            ->get(route('utusan.pengajuan.uep', ['edit' => $usulan->id]))
            ->assertOk()
            ->assertSee('Toko Berkah');
    }

    /**
     * KUBE edit mode renders (existing behavior must not regress).
     */
    public function test_kube_edit_page_renders(): void
    {
        $utusan = $this->makeUser(UserRole::UTUSAN_DEWAN);

        $usulan = UsulanPokir::create([
            'user_id' => $utusan->id,
            'tahun_anggaran' => 2027,
            'tahun_asal_proposal' => 2025,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Maju Bersama',
            'nama_ketua_individu' => 'Ahmad Fauzi',
            'nik' => '7301010101010012',
            'no_kk_ketua' => '7301010101010013',
            'no_hp' => '081234567890',
            'desil' => 1,
            'kabupaten' => 'Majene',
            'kecamatan' => 'Banggae',
            'desa_kelurahan' => 'Baru',
            'alamat_detail' => 'Jl. Test No. 2',
            'bidang_usaha' => 'Pertanian',
            'total_rab' => 15000000,
            'status' => UsulanStatus::REVISI_UTUSAN,
        ]);

        $this->actingAs($utusan)
            ->get(route('utusan.pengajuan.kube', ['edit' => $usulan->id]))
            ->assertOk()
            ->assertSee('KUBE Maju Bersama');
    }

    /**
     * Approval workflow: DRAFT → REVIEW_DINAS → CLEARED_RKA → FINAL_APPROVED.
     */
    public function test_full_status_flow(): void
    {
        $utusan = $this->makeUser(UserRole::UTUSAN_DEWAN);
        $verifikator = $this->makeUser(UserRole::VERIFIKATOR_DINAS);

        $usulan = UsulanPokir::create([
            'user_id' => $utusan->id,
            'tahun_anggaran' => 2027,
            'jenis_bantuan' => JenisBantuan::KUBE,
            'nama_kelompok_usaha' => 'KUBE Flow',
            'nama_ketua_individu' => 'Flow Tester',
            'nik' => '1234567890123456',
            'status' => UsulanStatus::DRAFT,
        ]);

        app(\App\Services\UsulanService::class)->submitUsulan($usulan->fresh());
        $this->assertEquals(UsulanStatus::REVIEW_DINAS, $usulan->fresh()->status);

        app(\App\Services\UsulanService::class)->approveUsulan($usulan->fresh(), 'OK');
        $this->assertEquals(UsulanStatus::CLEARED_RKA, $usulan->fresh()->status);

        app(\App\Services\UsulanService::class)->finalApprove($usulan->fresh());
        $this->assertEquals(UsulanStatus::FINAL_APPROVED, $usulan->fresh()->status);
    }
}