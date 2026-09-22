# SIM-PEP

**Sistem Informasi Manajemen Pokir Pemberdayaan Ekonomi Produktif**

Sistem informasi untuk mengelola usulan Pokir (Pokok Pikiran) DPRD terkait bantuan KUBE (Kelompok Usaha Bersama) dan UEP (Usaha Ekonomi Produktif) di Provinsi Sulawesi Barat.

## Fitur Utama

### 3 Role Pengguna
- **Utusan Dewan** — Input dan kelola usulan KUBE/UEP
- **Verifikator Dinas** — Verifikasi usulan melalui Kanban Board
- **Admin Provinsi** — Kelola user dan pengaturan sistem

### Alur Usulan
```
DRAFT → REVIEW_DINAS → CLEARED_RKA → FINAL_APPROVED
                  ↕
            REVISI_UTUSAN (edit & resubmit)
```

### Fitur Teknis
- **Wizard 3 Langkah** — Identitas → RAB → Upload Dokumen
- **Validasi DTSEN** — Auto-check NIK terhadap data DTSEN Kemensos
- **Validasi Pagu** — RAB otomatis dicek terhadap pagu per bidang usaha
- **Kanban Board** — Visualisasi alur verifikasi usulan
- **Multi-upload Dokumen** — Proposal, legalitas, foto kegiatan (3-5 foto)
- **Edit Mode** — Usulan yang direvisi bisa diedit dan diajukan ulang
- **Notifikasi** — Status usulan dikirimkan ke pengguna terkait
- **Export CSV** — Ekspor daftar usulan untuk pelaporan
- **Pencarian Wilayah** — Dropdown Kabupaten/Kecamatan (69 kecamatan)

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 13.19.0 |
| Frontend | Livewire 3.8.2 + Tailwind CSS v3 |
| Database | PostgreSQL (Supabase) |
| Auth | Laravel Breeze (Livewire/Volt) |
| Icons | Font Awesome 6.5 |
| Testing | PHPUnit (54 tests, 142 assertions) |

## Instalasi

### Prasyarat
- PHP 8.3+
- Composer
- Node.js 18+
- PostgreSQL

### Setup

```bash
# Clone repository
git clone https://github.com/nuralkadri07/SIM-PEP.git
cd SIM-PEP

# Install dependencies
composer install
npm install

# Konfigurasi environment
cp .env.example .env
php artisan key:generate

# Update .env dengan kredensial database Anda
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Migration & Seed
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Build frontend
npm run build

# Jalankan server
composer dev
```

### Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | nuralmumun@gmail.com | admin123 |
| Verifikator | verifikator@sipodev.local | password |
| Utusan | utusan@sipodev.local | password |
| Utusan 2 | utusan2@sipodev.local | password |

## Testing

```bash
# Jalankan semua test
composer test

# Test keamanan saja
php artisan test --filter=SecurityValidationTest
```

## Struktur Database

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna (3 role) |
| `usulan_pokir` | Data usulan KUBE/UEP |
| `anggota_kube` | Anggota tambahan KUBE |
| `data_rekening_uep` | Rekening bank UEP |
| `dokumen_arsip` | File dokumen usulan |
| `master_standar_pagu` | Pagu per bidang usaha |
| `master_desil_dtsen` | Data DTSEN Kemensos |
| `master_wilayah` | Data Kabupaten/Kecamatan |
| `log_audit_trail` | Log aktivitas sistem |
| `notifikasi` | Notifikasi pengguna |
| `drafts` | Draft usulan sementara |
| `gdrive_config` | Konfigurasi Google Drive |

## Aturan Bisnis

- NIK & No KK: tepat 16 digit, hanya angka
- Desil: 1-4 (Sangat Miskin s.d. Hampir Miskin)
- KUBE: RAB + usulan approved/cleared ≤ pagu_maksimal per bidang
- KUBE: minimal 1 anggota tambahan
- KK dalam 1 KUBE harus unik
- NIK tidak boleh duplikat dalam 1 KUBE
- UEP: nama rekening harus sama dengan nama KTP
- File: max 5MB, format PDF/JPG/PNG
- HP: format 08xxxxxxxxxx

## Screenshots

### Dashboard Utusan Dewan
Tampilan dashboard dengan statistik usulan dan notifikasi terbaru.

### Kanban Board Verifikasi
Visualisasi alur verifikasi: Review Dinas → Revisi Utusan → Cleared RKA.

### Form Pengajuan KUBE
Wizard 3 langkah: Identitas Kelompok → Bidang Usaha & RAB → Upload Dokumen.

## Lisensi

MIT License

## Kontak

**Muh. Nuralkadr Akram**
- GitHub: [@nuralkadri07](https://github.com/nuralkadri07)
