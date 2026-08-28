<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Filament-v5-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/PWA-Supported-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA">
</p>

<h1 align="center">Dont Forget</h1>

<p align="center">
  <strong>Sistem Informasi Pengajuan Lupa Absen & Tata Usaha</strong><br>
  <sub>Web app untuk pegawai Balai Wilayah VIII Makassar dalam pengajuan surat permohonan izin / pemberitahuan lupa absen, dengan fitur cetak naskah dinas A4, dashboard admin, dan instalasi PWA.</sub>
</p>

<p align="center">
  <a href="https://dontforget.sidaengpumakassar.com" target="_blank">🌐 Live Demo</a> &nbsp;&bull;&nbsp;
  <a href="#fitur">✨ Fitur</a> &nbsp;&bull;&nbsp;
  <a href="#instalasi">📦 Instalasi</a> &nbsp;&bull;&nbsp;
  <a href="#deploy">🚀 Deploy</a> &nbsp;&bull;&nbsp;
  <a href="#kontribusi">🤝 Kontribusi</a>
</p>

---

## ✨ Fitur

### Halaman Publik
- **Form Pengajuan Lupa Absen** — Pilih nama pegawai, sistem otomatis mengisi NIP, pangkat, jabatan, bagian, dan atasan langsung
- **Live Preview Surat** — Pratinjau langsung surat permohonan format naskah dinas (A4, margin 4-2-2-2 cm)
- **Cetak / PDF** — Tombol cetak langsung ke printer atau simpan sebagai PDF via browser
- **Hitung Otomatis Tanggal** — Pengajuan otomatis H+1 kerja (Jumat→Senin H+3, Sabtu→Senin H+2)
- **PWA (Installable)** — Dapat dipasang ke layar utama sebagai app, support offline

### Panel Admin (Filament v5)
- **Dashboard** — Stats overview: pengajuan perlu verifikasi, disetujui bulan ini, ditolak, total pegawai
- **Trend Pengajuan Chart** — Grafik pengajuan absen per bulan
- **Manajemen Pegawai** — CRUD pegawai dengan relasi atasan (hierarchy 33 data pegawai)
- **Manajemen Pengajuan Absen** — CRUD pengajuan dengan filter status (pending/disetujui/ditolak)
- **PWA Settings** — Konfigurasi nama app, warna theme, icon, cache version, offline mode
- **Search & Filter** — Pencarian pegawai/NIP, filter status pengajuan

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13.26.1 |
| **Admin Panel** | Filament v5 |
| **Language** | PHP 8.4.24 |
| **Database** | SQLite (default) / MariaDB |
| **Frontend** | Tailwind CSS 4 + Alpine.js 3 |
| **Build** | Vite 8 |
| **Deployment** | Laravel Herd + Cloudflare |

---

## 📁 Struktur Project

```
Dont_Forget_Bapekom8/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   └── PwaSettings.php          # Halaman konfigurasi PWA
│   │   ├── Resources/
│   │   │   ├── Pegawais/                 # CRUD Pegawai
│   │   │   │   ├── PegawaiResource.php
│   │   │   │   ├── Schemas/PegawaiForm.php
│   │   │   │   └── Tables/PegawaisTable.php
│   │   │   └── PengajuanAbsens/          # CRUD Pengajuan Absen
│   │   │       ├── PengajuanAbsenResource.php
│   │   │       ├── Schemas/PengajuanAbsenForm.php
│   │   │       └── Tables/PengajuanAbsensTable.php
│   │   └── Widgets/
│   │       ├── StatsOverview.php         # Statistik dashboard
│   │       └── TrendPengajuanChart.php   # Grafik trend
│   ├── Http/Controllers/
│   │   └── PengajuanController.php       # Controller form publik
│   ├── Models/
│   │   ├── Pegawai.php                   # Model pegawai
│   │   ├── PengajuanAbsen.php            # Model pengajuan
│   │   ├── PwaSetting.php                # Model pengaturan PWA
│   │   └── User.php                      # User (FilamentUser)
│   └── Providers/Filament/
│       └── AdminPanelProvider.php        # Konfigurasi panel admin
├── database/
│   ├── migrations/
│   │   ├── *_create_pegawai_table.php
│   │   ├── *_create_pengajuan_absen_table.php
│   │   └── *_create_pwa_settings_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── PegawaiSeeder.php
│       └── PegawaiImportSeeder.php       # Import dari Google Sheets
├── resources/views/
│   ├── pengajuan.blade.php              # Halaman utama (form + preview)
│   └── welcome.blade.php
├── routes/
│   ├── web.php                           # Routes publik
│   └── console.php
└── public/
    ├── images/pwa/                       # Icon PWA
    └── index.php
```

---

## 📊 Database Schema

### `pegawai`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Auto increment |
| `nip` | string(50) | Nomor Induk Pegawai (unique) |
| `nama` | string(150) | Nama lengkap |
| `pangkat_gol` | string(50) | Pangkat / Golongan |
| `jabatan` | string(100) | Nama jabatan |
| `bagian` | string(100) | Bagian / Unit kerja |
| `atasan_id` | FK → pegawai (nullable) | Atasan langsung |

### `pengajuan_absen`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Auto increment |
| `nomor_surat` | string(100) | Nomor surat (nullable) |
| `pegawai_id` | FK → pegawai | Pemohon |
| `atasan_id` | FK → pegawai | Penandatangan |
| `jenis_absen` | string(100) | Masuk / Pulang |
| `alasan` | text | Alasan lupa absen |
| `tanggal_lupa` | date | Tanggal kejadian |
| `tanggal_pengajuan` | date | Tanggal pengajuan surat |
| `kota_surat` | string(50) | Kota pembuatan surat |
| `status` | string(20) | pending / disetujui / ditolak |

### `pwa_settings`
| Kolom | Tipe | Default |
|-------|------|---------|
| `app_name` | string | Dont Forget |
| `theme_color` | string(20) | #111827 |
| `background_color` | string(20) | #ffffff |
| `icon_192` | string | /images/pwa/icon-192.png |
| `icon_512` | string | /images/pwa/icon-512.png |
| `cache_version` | string | v1 |
| `offline_enabled` | boolean | true |

---

## 🚀 Instalasi

### Prerequisites

- [PHP 8.4+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js 20+](https://nodejs.org/)
- [SQLite](https://www.sqlite.org/) atau MariaDB

### Setup

```bash
# Clone repository
git clone https://github.com/syahril-akbar/Dont_Forget_Bapekom8.git
cd Dont_Forget_Bapekom8

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
touch database/database.sqlite
php artisan migrate

# Seed data pegawai
php artisan db:seed --class=PegawaiSeeder

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

Buka `http://localhost:8000` di browser.

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@dontforget.test` | `password` |

> ⚠️ **Ganti password default** sebelum deploy ke production!

---

## ⚙️ Konfigurasi

### Environment Variables

```env
APP_NAME=SIPAT
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (default SQLite)
DB_CONNECTION=sqlite

# Atau gunakan MariaDB
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_dontforget
# DB_USERNAME=root
# DB_PASSWORD=

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

### Timezone

Konfigurasi timezone di `config/app.php`:

```php
'timezone' => 'Asia/Makassar', // WITA
```

---

## 🚢 Deploy

### Laravel Herd (Recommended)

```bash
# Install Herd dari https://herd.laravel.com
# Buat directory junction
mklink /J C:\Users\{username}\Herd\dontforget.sidaengpumakassar.com C:\Users\{username}\Herd\Dont_Forget_Bapekom8

# Herd akan auto-detect site
# Akses via: https://dontforget.sidaengpumakassar.com
```

### Production Config

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dontforget.sidaengpumakassar.com
```

```bash
# Cache config & views
php artisan config:cache
php artisan view:cache

# ⚠️ JANGAN jalankan route:cache untuk Filament!
```

### Cloudflare (SSL + CDN)

1. Tambahkan DNS A record → IP server
2. SSL Mode: **Flexible** (browser→CF HTTPS, CF→origin HTTP :80)
3. Nginx config: force `HTTPS on` untuk Mixed Content fix

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=AdminLoginTest
```

---

## 📡 API Routes

| Method | URI | Keterangan |
|--------|-----|------------|
| `GET` | `/` | Halaman utama (form pengajuan) |
| `POST` | `/pengajuan` | Submit pengajuan baru |
| `GET` | `/admin` | Panel admin (login required) |
| `GET` | `/manifest.webmanifest` | PWA manifest |
| `GET` | `/sw.js` | Service worker |

---

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch baru: `git checkout -b fitur/nama-fitur`
3. Commit perubahan: `git commit -m 'feat: tambahkan nama fitur'`
4. Push ke branch: `git push origin fitur/nama-fitur`
5. Buka Pull Request

### Contribution Guidelines

- Gunakan **conventional commits** (`feat:`, `fix:`, `chore:`, dll.)
- Pastikan tidak ada error PHP: `php artisan test`
- Untuk perubahan database, buat migration baru (jangan edit migration lama)
- Update README jika ada perubahan fitur

---

## 📄 License

MIT License - Silakan gunakan untuk keperluan pribadi maupun komersial.

---

<p align="center">
  Dibuat dengan ❤️ untuk Balai Wilayah VIII Makassar
</p>
