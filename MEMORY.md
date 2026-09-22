# MEMORY.md — SIPOKIR (SIM-PEP)

## Project Info
- **Path**: `/home/boot/Documents/KUMPULAN PROJECT DEV APP/SIPOKIR KUBE/SIPOKIR/sipodev`
- **Framework**: Laravel 13.19.0, Livewire 3.8.2, Tailwind CSS v3
- **Database**: SQLite (dev) / Supabase PostgreSQL (prod)
- **Auth**: Laravel Breeze (Livewire/Volt stack)
- **Admin**: `nuralmumun@gmail.com` / `admin123`
- **GitHub**: `https://github.com/nuralkadri07/SIM-PEP.git` (main branch)

## Dev Commands
- `composer dev` — artisan serve + queue + pail + vite concurrently
- `composer test` — 54 tests, 142 assertions
- Vite dev: `http://localhost:5173` (artisan serves at `http://localhost:8000`)
- Test files: `test-files/` directory (foto-ktp.png, proposal.pdf, etc.)

## E2E Test Results (July 22, 2026 — Playwright Browser)
### FASE 1: Login & Role Access ✅
- Login as utusan → /utusan/dashboard ✅
- Login as verifikator → /verifikator/dashboard ✅
- Login as admin → /admin/dashboard ✅

### FASE 2: KUBE Submission ✅
- 3-step wizard: Identitas → RAB → Upload Dokumen
- NIK DTSEN auto-validation: shows "belum terdata" for dummy NIKs (expected)
- Pagu validation: RAB Rp 15M within pagu Rp 50M ✅
- Submit → DRAFT → Daftar Usulan → Submit for Review → REVIEW_DINAS

### FASE 3: UEP Submission ✅
- 3-step wizard: Identitas & Rekening → Bidang & NIB → SKU & Legalitas
- Bank BSI, Nama Rekening = Nama KTP (match validation)
- Submit → DRAFT → Submit for Review → REVIEW_DINAS

### FASE 4: KanbanBoard Verification ✅
- KUBE #1: REVIEW_DINAS → Approve (Cleared RKA) → Final Approve → FINAL_APPROVED
- UEP #2: REVIEW_DINAS → Approve (Cleared RKA) → Final Approve → FINAL_APPROVED
- Both tabs (KUBE/UEP) load correctly

### FASE 5: Dashboard Verification ✅
- Verifikator Dashboard: 2 total usulan, stat cards correct
- 2 notifications generated for verifikator

### FASE 6: Admin Management ✅
- ManajemenUser: 4 users displayed correctly
- Table with Edit/Nonaktifkan actions

### FASE 7: DB & Tests ✅
- usulan_pokir: 2 records, both FINAL_APPROVED
- dokumen_arsip: 10 docs (KUBE) + 3 docs (UEP)
- anggota_kube: 2 anggota for KUBE #1
- All 54 tests pass (142 assertions)

### Test Data
- **KUBE #1**: KUBE Maju Bersama, Pertanian, RAB Rp 15M, Majene/Banggae → FINAL_APPROVED
- **UEP #2**: Toko Berkah, Perdagangan, BSI, Majene/Banggae → FINAL_APPROVED
- **Pengurus**: Ahmad Fauzi (Ketua), Siti Rahma (Sekretaris), Moh. Yusuf (Bendahara), Budi Santoso, Andi Saputra (Anggota)
- **UEP**: Rina Wati, NIK 7301010101010011, Desil 2
- **DB Status (Post-Edit Session)**: 4 users, 2 usulan (FINAL_APPROVED), 14 dokumen, 2 anggota, 1 rekening UEP, 11 notifikasi
- **Test usulan #3 (KUBE Test Revisi)**: deleted after E2E test

## Key Architecture Decisions
- Livewire components: `app/Http/Livewire/{Role}/` (NOT `app/Livewire/`)
- Views: `resources/views/livewire/{role}/component-name.blade.php`
- Sidebar: Alpine.js reactive `isDesktop` (NOT Tailwind lg: breakpoint)
- 3 roles: UTUSAN_DEWAN, VERIFIKATOR_DINAS, ADMIN_PROV

## Tahun System
- Tahun Anggaran: `config('app.tahun_anggaran')` = 2027
- Tahun Asal Proposal: dropdown (2024/2025/2026)
- Tahun Pengajuan: `YEAR(created_at)` — dashboard filter

## Completed Features (All Phases)
1. UI/UX overhaul (12 tasks) — Tailwind JIT fix, mobile sidebar, Font Awesome icons, accessibility
2. KUBE Pengurus — Ketua + Sekretaris + Bendahara + Anggota Tambahan
3. Desil dropdown 1-4 (manual, no DTKS lookup)
4. Multi-upload Foto Kegiatan Usaha (3-5 foto)
5. "Lainnya" dropdown pattern — sektorUsaha, bidangUsaha, namaBank with conditional text input
6. Disclaimer checkbox — full legal text, required before login, server-side validation
7. Address field split — kabupaten, kecamatan, desa_kelurahan, alamat_detail
8. DTSEN auto-validation — NIK lookup, manual "Cek Bansos" fallback
9. Period filtering — tahun dropdown on dashboards
10. Kanban pagination — per-column limit with "show more"
11. Mobile accordion pengurus — Alpine.js responsive collapse
12. E2E security & quality audit (27 fixes across 20 files)
13. Supabase PostgreSQL migration — 20 migrations + seed data
14. Git + GitHub push — 4 commits on main
15. Edit mechanism for returned usulans — REVISI_UTUSAN → edit → resubmit (9 files, 7 tahap)

## Login Page Layout
- Guest layout (`guest.blade.php`): branding → card (sm:max-w-md) → copyright
- Disclaimer: INSIDE Livewire component boundary (`wire:id`), NOT in `<x-slot>` (slots render outside wire:id — wire:model DEAD)
- `.disclaimer-breakout` CSS: `width:100vw; left:50%; transform:translateX(-50%); max-width:48rem` for full-width breakout
- Login template: wrapped in single root `<div>` (Livewire Volt requires exactly 1 root element)
- Info dinas: Dinas Sosial + Bidang Pemberdayaan below "Pemerintah Daerah Provinsi Sulawesi Barat"
- `wire:click` in `<x-slot name="header">` is DEAD — keep all interactive elements in component body
- **Gotcha**: named slots (`<x-slot name="disclaimer">`) render OUTSIDE Livewire's wire:id boundary — `wire:model`, `wire:click`, and error messages in slots are ALL broken

## Gotchas to Remember
- Livewire blade: `$this->methodName()` not `$methodName()` in `@php` blocks
- `exportCsv()` must NOT have `: void` return type (PHP 8.3 fatal)
- Vite: `server.host: 'localhost'` in `vite.config.js` — JANGAN hapus
- `public/storage` symlink must exist — run `php artisan storage:link`
- Tailwind v3 via `@tailwindcss/vite` plugin (despite plugin name, NOT v4)

## Database (20 migrations)
- 12 models: User, MasterStandarPagu, MasterDesilSen, MasterWilayah, UsulanPokir, DataRekeningUep, DokumenArsip, LogAuditTrail, GdriveConfig, AnggotaKube, Draft, Notifikasi
- 3 enums: UserRole, JenisBantuan, UsulanStatus
- `dokumen_arsip`: freeform `jenis_dokumen` string, multi-row per usulan
- `master_wilayah`: 6 kabupaten, 69 kecamatan
- `drafts`: UNIQUE `(user_id, jenis_bantuan)`, files on private disk

## Session Terakhir (Login Fix)
- **Commit terakhir**: `186e1a4` — fix(auth): login fix - disclaimer inside Livewire boundary + layout redesign
- **Perubahan yang dilakukan**:
  1. Login template: pindahkan disclaimer checkbox ke dalam Livewire component boundary
  2. Guest layout: hapus named slot `<x-slot name="disclaimer">`, hapus `overflow-hidden`
  3. CSS: tambah `.disclaimer-breakout` class (full-width breakout pattern)
  4. Login template: wrap dalam single root `<div>` (Livewire Volt requirement)
  5. Tambah info Dinas Sosial + Bidang di branding section
- **Root cause**: `<x-slot name="disclaimer">` render OUTSIDE `wire:id` → `wire:model` sync never worked
- **54/54 tests pass**, 142 assertions

## Session Terakhir (E2E Testing + Bug Fix)
- **Bug ditemukan & di-fix**: `PengajuanKube` missing `updatedTotalRab()` hook — `$paguMaksimal` tidak terisi karena `validatePagu()` early return saat `totalRab` kosong, dan tidak ada hook saat user isi RAB
- **Fix**: tambah `updatedTotalRab()` method di `PengajuanKube.php:229`
- **54/54 tests pass** setelah fix
- **Test data**: file dummy di `test-files/` (PNG + PDF)
- **Browser session**: Login sebagai `utusan@sipodev.local` di `/utusan/pengajuan/kube`
- **Routes penting**: `/utusan/pengajuan/kube` (bukan `/utusan/pengajuan-kube`)

## Session Edit Mechanism (July 22, 2026)
### Implementasi Alur Revisi (Return → Edit → Resubmit)
**7 Tahap selesai, 54/54 tests pass, E2E via MCP Playwright LULUS**

### Apa yang dibangun:
1. **Tahap 1 — UsulanService**: `deleteDokumenByJenis()`, `deleteAnggotaByUsulan()`, `submitUsulan()` clear catatan
2. **Tahap 2 — PengajuanKube**: `$editId`, `$isEditMode`, `$catatanVerifikator`, `$existingFiles`, `loadFromUsulan()`, submit branching (create vs update)
3. **Tahap 3 — PengajuanUep**: Edit mode support (sama seperti KUBE)
4. **Tahap 4 — DaftarUsulan blade**: tombol Edit amber untuk status REVISI_UTUSAN, link `?edit={id}`
5. **Tahap 5 — Form blades**: edit mode banner, catatan verifikator, file thumbnails existing
6. **Tahap 6 — Page views**: pass `?edit={id}` query param ke Livewire `mount(?int $editId)`
7. **Tahap 7 — Testing**: 54/54 tests + E2E flow via MCP Playwright

### Alur Revisi:
1. Verifikator klik "Return (Revisi)" → status = REVISI_UTUSAN + catatan tersimpan
2. Utusan lihat tombol amber "Edit" di DaftarUsulan → klik → form load data existing
3. Banner "Mode Edit — Usulan #X" + catatan verifikator ditampilkan
4. File existing ditampilkan sebagai thumbnail dengan badge "Ada"
5. User edit data/file → klik "Submit"
6. Usulan di-update + file lama diganti + anggota diganti + catatan di-clear + status → REVIEW_DINAS

### File yang diubah (9 files):
- `app/Services/UsulanService.php` — +2 methods, modified submitUsulan
- `app/Http/Livewire/UtusanDewan/PengajuanKube.php` — edit mode (loadFromUsulan, submit branching)
- `app/Http/Livewire/UtusanDewan/PengajuanUep.php` — edit mode support
- `resources/views/livewire/utusan-dewan/daftar-usulan.blade.php` — Edit button
- `resources/views/livewire/utusan-dewan/pengajuan-kube.blade.php` — edit indicators
- `resources/views/livewire/utusan-dewan/pengajuan-uep.blade.php` — edit indicators
- `resources/views/pages/utusan-dewan/pengajuan-kube.blade.php` — pass editId
- `resources/views/pages/utusan-dewan/pengajuan-uep.blade.php` — pass editId

### E2E Test Results (MCP Playwright):
- ✅ Login utusan → buat KUBE → status REVIEW_DINAS
- ✅ Login verifikator → Return with catatan → status REVISI_UTUSAN
- ✅ Login utusan → `/utusan/pengajuan/kube?edit=3` → edit mode aktif + catatan tampil
- ✅ Edit nama + upload files → Submit → status REVIEW_DINAS + catatan cleared
- ✅ DB verified: nama updated, catatan null, 10 docs, 1 anggota

## Session Master Anggota Dewan (July 24, 2026)
### Master Data 45 DPRD Sulawesi Barat
- **Commit**: `cd39f1a` — feat: master_anggota_dewan — 45 DPRD Sulbar from 7 dapil
- **New files**:
  - `database/migrations/2026_07_24_000001_create_master_anggota_dewan_table.php`
  - `database/migrations/2026_07_24_000002_add_anggota_dewan_id_to_users_table.php`
  - `database/migrations/2026_07_24_000003_add_anggota_dewan_id_to_usulan_pokir_table.php`
  - `app/Models/MasterAnggotaDewan.php`
  - `database/seeders/MasterAnggotaDewanSeeder.php`
- **Modified files**: User model, UsulanPokir model, ManajemenUser (component+view), PengajuanKube, PengajuanUep, KanbanBoard, layouts/app.blade.php, DatabaseSeeder
- **DB schema**:
  - `master_anggota_dewan`: id, dapil, kabupaten, nama, partai, periode, is_active
  - `users.anggota_dewan_id` → FK nullable → master_anggota_dewan
  - `usulan_pokir.anggota_dewan_id` → FK nullable → master_anggota_dewan
- **Seeder**: 45 rows across 7 dapil, 10 partai
  - Sulbar 1 (Mamasa): 6 — PKB, PDI-P, Golkar, NasDem, Hanura, PAN
  - Sulbar 2 (Polman): 8 — Gerindra, PDI-P, Golkar×2, NasDem, PKS, PAN, Demokrat
  - Sulbar 3 (Polman): 7 — PKB, PDI-P, Golkar, NasDem, PAN, Demokrat, Gerindra
  - Sulbar 4 (Majene): 5 — PKB, Gerindra, Golkar, PAN, Demokrat
  - Sulbar 5 (Mamuju): 9 — Gerindra, PDI-P, Golkar×2, NasDem, PAN, Demokrat×3
  - Sulbar 6 (Mamuju Tengah): 4 — Golkar×2, Demokrat, PPP
  - Sulbar 7 (Pasangkayu): 6 — Gerindra, PDI-P, Golkar, NasDem, Hanura, Demokrat
- **1:1 link**: `utusan@sipodev.local` → Suhadi Kandoa (PKB, Sulbar 1), `utusan2@sipodev.local` → Irfan Pahri Putra (Golkar, Sulbar 2)
- **ManajemenUser**: dropdown `<select>` grouped by dapil with `<optgroup>`, auto-fills name on selection
- **Submit**: auto-fills `anggota_dewan_id` from `auth()->user()->anggota_dewan_id`
- **KanbanBoard**: eager-loads `anggotaDewan`, shows partai badge on cards + detail
- **Sidebar**: shows "Partai — DAPIL X" instead of role label for utusan dewan
- **Tests**: 54/54 pass, 142 assertions
- **DB status**: 4 users (2 linked to anggota dewan), 45 anggota dewan, 2 usulan, 14 docs, 2 anggota, 1 rekening, 11 notifikasi

## Session Google Drive Integration (July 24, 2026)
### GAS Web App for File Storage
- **Commit**: `f4dfd06` — feat: Google Drive integration via GAS web app
- **New files**:
  - `gdrive-gas/Code.gs` — GAS web app (upload/delete/deleteFolder/test)
  - `app/Services/GdriveService.php` — Laravel HTTP client to GAS
  - `app/Http/Livewire/AdminProv/GdriveConfig.php` — admin settings
  - `config/gdrive.php` — GAS_WEBAPP_URL config
  - `database/migrations/2026_07_24_100000_add_gdrive_to_dokumen_arsip_table.php`
  - `resources/views/livewire/admin-prov/gdrive-config.blade.php`
  - `resources/views/pages/admin-prov/gdrive-config.blade.php`
- **Modified files**: DokumenArsip model, UsulanService, routes/web.php, layouts/app.blade.php
- **Folder structure**: `SIM-PEP/{tahun}/{KUBE|UEP}/{usulan_id}/`
- **Upload method**: Base64 via JSON POST (reliabel server-to-server)
- **Primary storage**: GDrive + local cache fallback
- **Admin panel**: `/admin/gdrive` — set GAS URL, test connection
- **Integration points**:
  - `UsulanService::uploadDokumen()` → auto-syncs to GDrive after local save
  - `UsulanService::deleteDokumenByJenis()` → deletes from GDrive + local
  - `dokumen_arsip.gdrive_file_id` + `gdrive_link` — nullable for local-only files
- **Setup steps**:
  1. Copy `gdrive-gas/Code.gs` → Google Apps Script → Deploy as Web App
  2. Copy deployment URL → paste di Admin → GDrive config
  3. Atau set `GAS_WEBAPP_URL` di `.env` → `php artisan config:clear`
- **Tests**: 54/54 pass, 142 assertions

## Pending / Future Work
- Google Drive setup: deploy Code.gs ke GAS → set GAS_WEBAPP_URL di .env → php artisan config:clear
- External DTSEN API not used — CSV upload only
- Last push: commit `ed78913` (rules.md) → `f4dfd06` (gdrive) → `03cc803` (docs)

## Testing
- 54 tests, 142 assertions (AuthTest, UsulanTest, SecurityValidationTest, Breeze auth)
- After model/enum changes: always run `composer test`
- SecurityValidationTest: NIK/No KK 16-digit, phone regex, file size, role access
