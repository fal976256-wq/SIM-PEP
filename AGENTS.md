# AGENTS.md — SIPOKIR (SIM-PEP)

## Commands
- `composer setup` — install, migrate, seed, build (one-time)
- `composer dev` — artisan serve + queue + pail + vite concurrently
- `composer test` — 54 tests, 142 assertions. Clears config cache first
- `php artisan test --filter=SecurityValidationTest` — security tests only
- Admin: `nuralmumun@gmail.com` / `admin123`. Dev: `verifikator@`, `utusan@`, `utusan2@sipodev.local` (password: `password`)

## Routes
- `/utusan/pengajuan/kube` (NOT `/utusan/pengajuan-kube`) — KUBE form
- `/utusan/pengajuan/kube?edit={id}` — KUBE edit mode (returned usulan)
- `/utusan/pengajuan/uep` — UEP form
- `/utusan/pengajuan/uep?edit={id}` — UEP edit mode (returned usulan)
- `/verifikator/verifikasi` — KanbanBoard
- `/admin/manajemen-user` — ManajemenUser

## Architecture — Non-Obvious
- **Livewire components**: `app/Http/Livewire/{UtusanDewan,Verifikator,AdminProv}/` (NOT `app/Livewire/`)
  - `app/Livewire/` exists but only contains `Actions/` and `Forms/` — NOT where components live
- **Views**: `resources/views/livewire/{role}/component-name.blade.php` — real UI logic
- **Page views**: `resources/views/pages/{role}/page.blade.php` — thin wrappers only
- **Sidebar**: Alpine.js reactive `isDesktop` variable (NOT Tailwind lg: breakpoint). NEVER use `fixed lg:sticky`.
- **RoleMiddleware**: variadic — `role:UTUSAN_DEWAN` or `role:VERIFIKATOR_DINAS,ADMIN_PROV`
- **UsulanService** (`app/Services/UsulanService.php`): all status changes + audit log + validation helpers
- 13 models, 3 enums (UserRole, JenisBantuan, UsulanStatus), 23 migrations

## Tahun System
- **Tahun Anggaran** = `config('app.tahun_anggaran')` default 2027 — tahun realisasi dana
- **Tahun Asal Proposal** = dropdown di form (2024/2025/2026) — tahun proposal fisik dibuat
- **Tahun Pengajuan** = `YEAR(created_at)` — filter dashboard/list
- Dashboard: `whereYear('created_at', $tahunPengajuan)` (NOT `where('tahun_anggaran')`)
- Ubah tahun: edit `TAHUN_ANGGARAN` di `.env` atau `config/app.php`

## Business Rules (Hard Gates)
- NIK/No KK: exactly 16 digits + `ctype_digit()` numeric check
- Desil: must be 1-4 (manual dropdown)
- KUBE: RAB + existing approved/cleared usulans <= pagu_maksimal per bidang usaha (re-checked at submit)
- KUBE: minimal 1 anggota tambahan; sektor/bidang "Lainnya" → wajib isi keterangan teks manual
- KK uniqueness: all KK within 1 KUBE must differ
- NIK uniqueness: same person cannot hold 2+ positions in 1 KUBE
- UEP: bank account name must match KTP name (case-insensitive)
- UEP: No NIB harus angka (jika diisi)
- Dokumentasi Foto: minimal 3, maks 5 foto per KUBE
- File size: max 5MB per file; Phone: regex `08xxxxxxxxxx`
- Submit re-validates ALL steps (prevent Livewire direct call bypass)
- Desil mismatch block: submit blocked if NIK in DTSEN but desil ≠ DTSEN desil
- Draft files: `resolveFile()` verifies MIME via `finfo_file()` from actual file content
- **Edit mode:** `submitUsulan()` clears `catatan_verifikator` on resubmit; old files/members replaced via `deleteDokumenByJenis()` + `deleteAnggotaByUsulan()`

## Database
- PostgreSQL (Supabase) in dev + prod. Tests use SQLite `:memory:` via phpunit.xml override
- `dokumen_arsip`: freeform `jenis_dokumen` string, multi-row per usulan
- `master_wilayah`: 6 kabupaten, 69 kecamatan (cascading dropdown)
- `master_anggota_dewan`: 45 DPRD Sulbar, 7 dapil, 10 partai — seeded via `MasterAnggotaDewanSeeder`
- `users.anggota_dewan_id` → FK to `master_anggota_dewan` (nullable, 1:1 utusan↔dewan)
- `usulan_pokir.anggota_dewan_id` → FK to `master_anggota_dewan` (auto-filled from auth user)
- `drafts`: UNIQUE `(user_id, jenis_bantuan)`, files on private disk
- Migrations DB-agnostic (Laravel Blueprint) — safe for PG migration

## Google Drive Integration (via GAS)
- `gdrive-gas/Code.gs` — deploy ke Google Apps Script → Deploy as Web App
- `GdriveService` — HTTP POST base64 JSON ke GAS web app
- `dokumen_arsip`: has `gdrive_file_id` + `gdrive_link` (nullable, local-only fallback)
- Folder structure: `SIM-PEP/{tahun}/{KUBE|UEP}/{usulan_id}/`
- `uploadDokumen()` auto-syncs to GDrive; `deleteDokumenByJenis()` cleans up GDrive
- Config: `config/gdrive.php` + env `GAS_WEBAPP_URL`
- Admin panel: `/admin/gdrive` — set URL + test connection

## Testing
- After model/enum changes: always run `composer test`
- Test files: `test-files/` directory (PNG + PDF for E2E browser testing via Playwright)
- **E2E (Playwright):** All 7 phases passed. KUBE+UEP → FINAL_APPROVED. DB verified. Full report in MEMORY.md

## E2E Playwright Notes
- **Livewire `fill()` bug:** Playwright `fill()` does NOT trigger Livewire `wire:model.live` bindings — component state stays empty. Use `type()` + `dispatchEvent('input')` instead
- **Livewire JS API:** Use `Livewire.find(id).set('prop', value)` to set component state directly — faster than fill/type for complex forms
- **File uploads:** Use `input[type="file"].setInputFiles()` directly — dropzone buttons don't trigger filechooser reliably
- **Dialog confirm:** `page.getByRole('button', { name: 'Submit' })` triggers native confirm dialog → `browser_handle_dialog(accept=true)` or `page.on('dialog', d => d.accept())`
- **Status flow verified:** DRAFT → (Submit from DaftarUsulan) → REVIEW_DINAS → Approve → CLEARED_RKA → Final Approve → FINAL_APPROVED
- **Revision flow verified:** REVIEW_DINAS → Return (Revisi) → REVISI_UTUSAN → Edit (?edit={id}) → Submit → REVIEW_DINAS

## UI Conventions
- Bahasa Indonesia for ALL user-facing strings (no `__('...')` wrappers)
- `wire:loading` on all submit/action buttons
- `overflow-x-auto` on ALL tables
- Empty states link to both KUBE and UEP forms
- `wire:model.live` for real-time binding, `wire:model` for lazy

## Gotchas
- `tailwind.config.js` is legacy — Tailwind v3 via `@tailwindcss/vite` plugin (despite plugin name, NOT v4)
- Vite server: `server.host: 'localhost'` in `vite.config.js` — JANGAN dihapus, IPv6 `[::]` breaks CSS loading
- Font Awesome 6.5 via CDN (not npm) — `layouts/app.blade.php` <head>
- Livewire blade: `$this->methodName()` not `$methodName()` in `@php` blocks
- Don't put HTML in `placeholder=""` or `<i>` inside `<option>`
- `exportCsv()` must NOT have `: void` return type (PHP 8.3 fatal)
- **NEVER** put `wire:click` inside `<x-slot name="header">` — slots are rendered by layout, not Livewire component, so `wire:click` is dead. Keep buttons in component body.
- Header slot in `app.blade.php` uses `{!! $header !!}` (not `{{ $header }}`) — needed for Livewire directives
- Guest layout (`guest.blade.php`): branding → card (sm:max-w-md) → copyright. Disclaimer INSIDE Livewire component with `.disclaimer-breakout` CSS. NEVER use named slots for interactive elements (renders outside wire:id).
- `public/storage` symlink must exist — run `php artisan storage:link`
- Livewire Volt requires exactly ONE root element per component view. Multiple root elements → `MultipleRootElementsDetectedException` (500 error)
- Livewire `updatedXxx()` hooks: if field A validates against field B, add hooks for BOTH. Example: `updatedBidangUsaha()` calls `validatePagu()` but returns early if `totalRab` empty → must also add `updatedTotalRab()` to re-trigger validation
- Livewire mount signature: `mount(?int $editId = null)` — use nullable type hint for optional query params
- Edit mode: page view passes `request()->query('edit')` to Livewire via `:editId` prop

## Security
- File uploads: MIME validation (`pdf`, `jpg`, `jpeg`, `png`) + `Str::slug()` sanitization
- `resolveFile()`: verifies MIME via `finfo_file()` from actual file content (defense-in-depth)
- KanbanBoard: `authorizeVerifikator()` + `DB::transaction()` + `lockForUpdate()` on approve/return/finalApprove
- LIKE wildcards (`%`, `_`) escaped in search queries (ManajemenUser, DaftarUsulan, SinkronisasiDesil)
- Draft files on private disk; `resolveFile()` checks Livewire upload first, fallback to draft
- `validatePagu()`: sums existing approved/cleared usulans for same bidang+year before checking limit

## Rules File
- `RULES.md` — AI coding assistant guidelines (core directives, coding standards, workflow, testing, project context)
- Apply to all new projects as baseline rules
