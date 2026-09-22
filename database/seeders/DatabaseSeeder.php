<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\MasterAnggotaDewan;
use App\Models\MasterStandarPagu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Anggota Dewan (45 anggota, 7 dapil)
        $this->call(MasterAnggotaDewanSeeder::class);
        echo "🏛️  Master Anggota Dewan: 45 anggota, 7 dapil\n";

        // 2. Admin Provinsi (Super Admin)
        User::firstOrCreate(
            ['email' => 'nuralmumun@gmail.com'],
            [
                'name' => 'Admin Provinsi',
                'password' => Hash::make('admin123'),
                'role' => UserRole::ADMIN_PROV,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Verifikator Dinas
        User::firstOrCreate(
            ['email' => 'verifikator@sipodev.local'],
            [
                'name' => 'Verifikator Dinas',
                'password' => Hash::make('password'),
                'role' => UserRole::VERIFIKATOR_DINAS,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Utusan Dewan — linked to anggota dewan Sulbar 1 (Mamasa)
        $dewan1 = MasterAnggotaDewan::where('nama', 'Suhadi Kandoa')->first();
        User::firstOrCreate(
            ['email' => 'utusan@sipodev.local'],
            [
                'name' => 'Utusan Dewan',
                'password' => Hash::make('password'),
                'role' => UserRole::UTUSAN_DEWAN,
                'anggota_dewan_id' => $dewan1?->id,
                'no_hp' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. Utusan Dewan 2 — linked to anggota dewan Sulbar 2 (Polman)
        $dewan2 = MasterAnggotaDewan::where('nama', 'Irfan Pahri Putra, S.I.Kom.')->first();
        User::firstOrCreate(
            ['email' => 'utusan2@sipodev.local'],
            [
                'name' => 'Utusan Dewan 2',
                'password' => Hash::make('password'),
                'role' => UserRole::UTUSAN_DEWAN,
                'anggota_dewan_id' => $dewan2?->id,
                'no_hp' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. Master Pagu KUBE (Tahun 2025 & 2026)
        $bidangList = [
            ['bidang_usaha' => 'Pertanian', 'pagu_maksimal' => 50000000],
            ['bidang_usaha' => 'Peternakan', 'pagu_maksimal' => 75000000],
            ['bidang_usaha' => 'Perikanan', 'pagu_maksimal' => 60000000],
            ['bidang_usaha' => 'Perdagangan', 'pagu_maksimal' => 40000000],
            ['bidang_usaha' => 'Jasa', 'pagu_maksimal' => 35000000],
            ['bidang_usaha' => 'Industri Rumahan', 'pagu_maksimal' => 45000000],
            ['bidang_usaha' => 'Kerajinan Tangan', 'pagu_maksimal' => 30000000],
            ['bidang_usaha' => 'Lainnya', 'pagu_maksimal' => 25000000],
        ];

        foreach ([2025, 2026, 2027] as $tahun) {
            foreach ($bidangList as $item) {
                MasterStandarPagu::firstOrCreate(
                    ['tahun_anggaran' => $tahun, 'bidang_usaha' => $item['bidang_usaha']],
                    ['pagu_maksimal' => $item['pagu_maksimal']]
                );
            }
        }

        echo "✅ Seeders berhasil dijalankan!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "👤 Admin Prov:     nuralmumun@gmail.com / admin123\n";
        echo "👤 Verifikator:    verifikator@sipodev.local / password\n";
        echo "👤 Utusan Dewan:   utusan@sipodev.local / password\n";
        echo "👤 Utusan Dewan 2: utusan2@sipodev.local / password\n";
        echo "💲 Master Pagu:    8 bidang usaha x 3 tahun\n";

        // 6. Master Wilayah (Kabupaten & Kecamatan)
        $this->call(MasterWilayahSeeder::class);
        echo "📍 Master Wilayah: 6 kabupaten, 50+ kecamatan\n";
    }
}
