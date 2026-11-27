<div align="center">

<img src="public/images/logo.png" alt="Logo SIPABIS" width="96">

# SIPABIS

### Sistem Informasi Persediaan Barang Pakai Habis · Edisi Demo Portofolio
**Arsitektur Aplikasi Persediaan Kantor (Inspirasi Kasus BPS Kabupaten Cirebon) · Data Simulasi Publik**

Platform web untuk mengelola persediaan ATK dan barang pakai habis kantor dari ujung ke ujung: pengajuan, verifikasi, serah terima, barang masuk, kartu kendali, sampai Berita Acara Stock Opname.

<p>
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 3.4">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=black" alt="Alpine.js 3">
</p>
<p>
  <a href="https://github.com/Diva-Baihaqi/SIPABIS-Demo/actions/workflows/ci.yml"><img src="https://github.com/Diva-Baihaqi/SIPABIS-Demo/actions/workflows/ci.yml/badge.svg" alt="CI Status"></a>
  <a href="https://github.com/Diva-Baihaqi/SIPABIS-Demo/releases/tag/v1.0.0"><img src="https://img.shields.io/badge/Release-v1.0.0-10B981?style=flat-square&logo=github" alt="Release v1.0.0"></a>
  <a href="https://github.com/Diva-Baihaqi/SIPABIS-Demo/pkgs/container/sipabis-demo"><img src="https://img.shields.io/badge/Package-ghcr.io%2Fsipabis--demo-2496ED?style=flat-square&logo=docker&logoColor=white" alt="GHCR Container Package"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-F59E0B?style=flat-square" alt="License MIT"></a>
</p>
<p>
  <img src="https://img.shields.io/badge/Vite-5-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite 5">
  <img src="https://img.shields.io/badge/Chart.js-4-FF6384?style=flat-square&logo=chartdotjs&logoColor=white" alt="Chart.js 4">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/PHPUnit-230_tests-3C9CD7?style=flat-square" alt="PHPUnit 230 tests">
  <img src="https://img.shields.io/badge/AI-Multi--Provider-10B981?style=flat-square" alt="AI Multi-Provider">
</p>

<p>
  <a href="#-tentang-proyek">Tentang</a> •
  <a href="#-fitur-unggulan">Fitur</a> •
  <a href="#-galeri-tampilan">Galeri</a> •
  <a href="#-alur-kerja-sistem">Alur Kerja</a> •
  <a href="#-teknologi">Teknologi</a> •
  <a href="#-instalasi">Instalasi</a> •
  <a href="#-pengujian">Pengujian</a>
</p>

<img src=".github/assets/screenshots/public-homepage.webp" alt="Halaman Beranda SIPABIS" width="100%">

</div>

---

## 📑 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Unggulan](#-fitur-unggulan)
- [Hak Akses per Peran](#-hak-akses-per-peran)
- [Galeri Tampilan](#-galeri-tampilan)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Teknologi](#-teknologi)
- [Arsitektur & Struktur Folder](#-arsitektur--struktur-folder)
- [Instalasi](#-instalasi)
- [Akun Demo](#-akun-demo)
- [Konfigurasi Asisten AI](#-konfigurasi-asisten-ai)
- [Pengujian](#-pengujian)
- [Deployment ke Production](#-deployment-ke-production)
- [Kontribusi](#-kontribusi)
- [Lisensi & Kredit](#-lisensi--kredit)

---

## 📌 Tentang Proyek

> [!NOTE]
> **Edisi Demo & Portofolio Publik (`SIPABIS-Demo`)** — Seluruh data pegawai, penanggung jawab ruangan, NIP, email, dan spesimen tanda tangan pada repositori publik ini menggunakan **data dummy/simulasi** untuk menjaga privasi instansi. Arsitektur kode, alur bisnis, ekspor laporan, dan pengujian otomatis sepenuhnya identik dengan sistem produksi.

**SIPABIS** dibangun untuk menggantikan pencatatan persediaan manual (kertas dan spreadsheet terpisah) di lingkungan **BPS Kabupaten Cirebon**. Semua proses persediaan barang pakai habis kini tercatat di satu tempat, bisa dilacak, dan siap dicetak sesuai format dokumen resmi.

| Masalah Sebelumnya | Solusi di SIPABIS |
|---|---|
| Permintaan ATK lewat kertas/chat, sulit dilacak statusnya | Tiket digital dengan status `Diajukan → Disetujui → Selesai` yang terpantau real-time |
| Stok gudang tidak diketahui pasti | Katalog 700+ barang lengkap dengan foto, stok aktif, dan peringatan stok kritis |
| Kartu kendali & buku persediaan diisi manual | Kartu stok, buku kendali per ruangan, dan saldo berjalan dihitung otomatis |
| Stock opname memakan waktu dan rawan selisih | Sesi opname dengan *snapshot* stok, input hitung fisik, dan BASO siap disahkan pimpinan |
| Laporan harus disusun ulang tiap periode | Ekspor Excel/Word dan cetak PDF dengan satu klik |

### Angka Singkat

<div align="center">

| 📦 Katalog Barang | 🗂️ Kategori | 🏢 Unit Kerja | 👥 Akun Simulasi | ✅ Automated Tests |
|:---:|:---:|:---:|:---:|:---:|
| **727** | **8** | **7** | **18** | **230+** |

</div>

---

## ✨ Fitur Unggulan

<table>
<tr>
<td width="50%" valign="top">

### 🛒 Permintaan ATK Digital
- Formulir pengajuan dengan **katalog bergambar**, pencarian langsung, dan filter kategori
- *Combobox* pintar berisi foto, kategori, dan sisa stok tiap barang
- Riwayat permintaan dengan **filter live tanpa reload** (status, kategori, kata kunci)
- Cetak **Formulir Permintaan Persediaan (PSD-133)** standar BPS

</td>
<td width="50%" valign="top">

### ✅ Verifikasi & Serah Terima
- Antrean verifikasi untuk admin dengan filter status & ruangan
- Setujui, sesuaikan kuota, atau tolak permintaan disertai alasan
- Serah terima otomatis **memotong stok** dan mencatat transaksi keluar
- Validasi stok mencegah barang keluar melebihi persediaan

</td>
</tr>
<tr>
<td valign="top">

### 📦 Katalog & Barang Masuk
- Katalog 700+ barang dengan kode BMN, lokasi rak, dan stok minimum
- Upload foto **drag & drop** atau **tempel dari clipboard** (`Ctrl+V`)
- Foto otomatis dikompres ke **WebP < 100 KB** dan dirapikan per kategori
- Pencatatan pengadaan/barang masuk lengkap dengan nomor SPJ, ekspor Excel, dan cetak

</td>
<td valign="top">

### 📒 Kartu Kendali & Buku Persediaan
- **Buku kendali per ruangan** dan **kartu stok per barang**
- Saldo awal, mutasi masuk/keluar, dan saldo berjalan dihitung otomatis
- Nilai rupiah persediaan per transaksi
- Ekspor **Excel** dan **Word**, serta cetak rekap seluruh ruangan

</td>
</tr>
<tr>
<td valign="top">

### 🧾 Stock Opname & BASO
- Buka sesi opname dengan *snapshot* stok sistem yang dibekukan
- Input hasil hitung fisik, selisih dihitung otomatis
- Alur dokumen `Draft → Final → Disahkan` oleh pimpinan
- Cetak dan ekspor **Berita Acara Stock Opname** (ringkas & lengkap)

</td>
<td valign="top">

### 🤖 Asisten AI SIPABIS
- Chatbot yang membantu pegawai mencari barang dan memahami alur layanan
- **Multi-provider**: Gemini, Groq, OpenRouter, Mistral, DeepSeek, OpenAI, atau *custom endpoint*
- Tempel API key, sistem mendeteksi penyedianya, lalu tersambung
- API key **terenkripsi**, mode hemat token, dan **auto-fallback** saat kuota habis

</td>
</tr>
<tr>
<td valign="top">

### 📊 Dashboard per Peran
- Ringkasan katalog, stok kritis, antrean, dan sirkulasi bulanan
- Grafik tren barang masuk vs keluar 6 bulan terakhir (Chart.js)
- Distribusi kategori barang
- Statistik profil yang **diperbarui otomatis** tanpa refresh halaman

</td>
<td valign="top">

### 📘 Buku Panduan & Usulan Barang
- Halaman **Panduan** publik dengan 3 tab: Pegawai, Pimpinan, Admin
- Mode baca fokus per bab, pencarian lintas bab, dan versi cetak
- Pegawai dapat **mengusulkan barang baru** yang belum ada di katalog
- Admin meninjau usulan dan memperbarui statusnya

</td>
</tr>
</table>

**Fitur pendukung lainnya:** login cukup dengan NIP, manajemen pengguna (aktif/nonaktif, reset kata sandi, ekspor), manajemen kategori, halaman logout beranimasi, halaman *Page Expired* (419) yang ramah, jam WIB real-time di topbar, dan tampilan responsif untuk desktop maupun ponsel.

---

## 🔐 Hak Akses per Peran

SIPABIS memiliki tiga peran dengan menu dan wewenang yang berbeda.

| Modul | 👤 Pegawai | 👔 Pimpinan | 🛠️ Admin |
|---|:---:|:---:|:---:|
| Dashboard sesuai peran | ✅ | ✅ | ✅ |
| Lihat katalog persediaan | ✅ | ✅ | ✅ |
| Ajukan permintaan ATK | ✅ | ✅ | — |
| Usulkan barang baru | ✅ | ✅ | Tinjau |
| Verifikasi & serah terima permintaan | — | — | ✅ |
| Kelola barang, kategori & foto | — | — | ✅ |
| Input barang masuk / pengadaan | — | — | ✅ |
| Buku kendali & kartu stok | Ruangan sendiri | ✅ | ✅ |
| Stock opname (buka, hitung, finalisasi) | — | Lihat | ✅ |
| Pengesahan BASO | — | ✅ | — |
| Manajemen pengguna | — | — | ✅ |
| Manajemen AI | — | — | ✅ |
| Profil & ubah kata sandi | ✅ | ✅ | ✅ |

---

## 🖼️ Galeri Tampilan

### 🌐 Halaman Publik

<table>
<tr>
<td width="50%"><img src=".github/assets/screenshots/public-login.webp" alt="Halaman Login"><p align="center"><b>Login dengan NIP</b></p></td>
<td width="50%"><img src=".github/assets/screenshots/public-katalog-populer.webp" alt="Katalog Populer"><p align="center"><b>Katalog Barang Populer</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/public-alur-pelayanan.webp" alt="Alur Pelayanan"><p align="center"><b>Alur Pelayanan</b></p></td>
<td><img src=".github/assets/screenshots/public-daftar-barang.webp" alt="Daftar Semua Barang"><p align="center"><b>Daftar Semua Barang</b></p></td>
</tr>
</table>

<div align="center">
<img src=".github/assets/screenshots/public-ai-chatbot.webp" alt="Asisten AI SIPABIS" width="420">
<p><b>Asisten AI SIPABIS</b></p>
</div>

<details open>
<summary><h3>🛠️ Peran Admin (Subbagian Umum)</h3></summary>

<table>
<tr>
<td width="50%"><img src=".github/assets/screenshots/admin-dashboard.webp" alt="Dashboard Admin"><p align="center"><b>Dashboard Admin</b></p></td>
<td width="50%"><img src=".github/assets/screenshots/admin-katalog.webp" alt="Katalog Persediaan"><p align="center"><b>Katalog Persediaan</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/admin-verifikasi-permintaan.webp" alt="Verifikasi Permintaan"><p align="center"><b>Verifikasi Permintaan ATK</b></p></td>
<td><img src=".github/assets/screenshots/admin-detail-tiket.webp" alt="Detail Tiket"><p align="center"><b>Detail Tiket & Serah Terima</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/admin-barang-masuk.webp" alt="Barang Masuk"><p align="center"><b>Penerimaan Barang Masuk</b></p></td>
<td><img src=".github/assets/screenshots/admin-tambah-barang.webp" alt="Tambah Barang"><p align="center"><b>Tambah Barang Baru</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/admin-input-pengadaan.webp" alt="Input Pengadaan Barang Baru"><p align="center"><b>Input Pengadaan Barang Baru</b></p></td>
<td><img src=".github/assets/screenshots/admin-usulan-barang.webp" alt="Usulan Barang"><p align="center"><b>Tinjauan Usulan Barang</b></p></td>
</tr>
</table>

</details>

<details open>
<summary><h3>👤 Peran Pegawai</h3></summary>

<table>
<tr>
<td width="50%"><img src=".github/assets/screenshots/pegawai-dashboard.webp" alt="Dashboard Pegawai"><p align="center"><b>Dashboard Pegawai</b></p></td>
<td width="50%"><img src=".github/assets/screenshots/pegawai-form-permintaan.webp" alt="Form Permintaan"><p align="center"><b>Formulir Pengajuan ATK</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/pegawai-permintaan.webp" alt="Riwayat Permintaan"><p align="center"><b>Riwayat Permintaan ATK</b></p></td>
<td><img src=".github/assets/screenshots/pegawai-usulan-barang.webp" alt="Usulan Barang Baru"><p align="center"><b>Usulan Barang Baru</b></p></td>
</tr>
</table>

</details>

<details open>
<summary><h3>👔 Peran Pimpinan</h3></summary>

<table>
<tr>
<td width="50%"><img src=".github/assets/screenshots/pimpinan-dashboard.webp" alt="Dashboard Pimpinan"><p align="center"><b>Dashboard Pimpinan</b></p></td>
<td width="50%"><img src=".github/assets/screenshots/pimpinan-usulan.webp" alt="Usulan Barang Baru"><p align="center"><b>Usulan Barang Baru</b></p></td>
</tr>
<tr>
<td><img src=".github/assets/screenshots/pimpinan-baso.webp" alt="BASO"><p align="center"><b>Pengesahan BASO</b></p></td>
<td><img src=".github/assets/screenshots/pimpinan-alur.webp" alt="Alur Pimpinan"><p align="center"><b>Panduan Alur Pimpinan</b></p></td>
</tr>
</table>

</details>

---

## 🔄 Alur Kerja Sistem

### Siklus Permintaan ATK

```mermaid
flowchart LR
    A["👤 Pegawai<br/>Ajukan Permintaan"] --> B{"🛠️ Admin<br/>Verifikasi"}
    B -- Disetujui --> C["📦 Barang Disiapkan"]
    B -- Ditolak --> X["❌ Ditolak<br/>+ Alasan"]
    C --> D["🤝 Serah Terima"]
    D --> E["✅ Selesai<br/>Stok Otomatis Berkurang"]
    E --> F["📒 Tercatat di<br/>Kartu Kendali"]
    A -. Dibatalkan pemohon .-> Y["⏹️ Dibatalkan"]
```

### Siklus Stock Opname & BASO

```mermaid
flowchart LR
    S1["🛠️ Admin<br/>Buka Sesi Opname"] --> S2["❄️ Snapshot<br/>Stok Sistem"]
    S2 --> S3["🔢 Input<br/>Hitung Fisik"]
    S3 --> S4["📝 Status: Draft"]
    S4 --> S5["🔒 Finalisasi<br/>Status: Final"]
    S5 --> S6["👔 Pimpinan<br/>Sahkan BASO"]
    S6 --> S7["✅ Status: Disahkan<br/>Cetak / Ekspor"]
```

### Status Permintaan

| Status | Arti |
|---|---|
| 🟡 `diajukan` | Permintaan terkirim, menunggu verifikasi admin |
| 🔵 `disetujui` | Disetujui admin, barang siap diambil |
| 🟢 `selesai` | Barang sudah diserahterimakan, stok berkurang |
| 🔴 `ditolak` | Ditolak admin disertai alasan |
| ⚪ `dibatalkan` | Dibatalkan oleh pemohon sebelum diproses |

---

## 🧰 Teknologi

| Lapisan | Teknologi |
|---|---|
| **Backend** | Laravel 11, PHP 8.2+ |
| **Frontend** | Blade, Tailwind CSS 3.4 (`@tailwindcss/forms`, `@tailwindcss/typography`), Alpine.js 3 |
| **Visualisasi Data** | Chart.js 4 |
| **Build Tool** | Vite 5 + `laravel-vite-plugin` |
| **Database** | MySQL / MariaDB (SQLite untuk pengujian lokal) |
| **Dokumen & Laporan** | PhpSpreadsheet (ekspor Excel), ekspor Word, tampilan cetak PDF |
| **Pengolahan Gambar** | PHP GD, konversi otomatis ke WebP |
| **AI** | Gateway multi-provider (protokol Gemini & OpenAI-compatible) |
| **Pengujian** | PHPUnit 10, Mockery, Faker |
| **Kualitas Kode** | Laravel Pint |

---

## 🏗️ Arsitektur & Struktur Folder

Logika bisnis dipisahkan ke **Service Layer** supaya controller tetap ramping dan mudah diuji.

```text
sipabis-demo/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # Verifikasi permintaan, barang masuk, pengguna, kategori, AI
│   │   ├── Pegawai/            # Permintaan ATK pegawai & pimpinan
│   │   ├── BarangController.php
│   │   ├── KartuKendaliController.php
│   │   ├── StockOpnameController.php
│   │   ├── PanduanController.php
│   │   └── ...
│   ├── Models/                 # BarangPersediaan, Permintaan, StokOpname, TransaksiPersediaan, ...
│   └── Services/
│       ├── PermintaanApprovalService.php   # Alur pengajuan → verifikasi → serah terima
│       ├── StockMovementService.php        # Mutasi stok masuk/keluar yang aman
│       ├── KartuKendaliService.php         # Saldo berjalan & buku kendali
│       ├── StockOpnameService.php          # Snapshot, selisih, & BASO
│       ├── SpreadsheetExportService.php    # Ekspor Excel
│       ├── ImageOptimizationService.php    # Kompresi foto WebP
│       └── AiGatewayService.php            # Gateway AI multi-provider + fallback
├── database/
│   ├── data/                   # Data awal (barang, kategori, ruangan, pegawai) dalam JSON
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/ · js/
│   └── views/                  # admin, pegawai, barang, kartu-kendali, stok-opname, panduan, ...
├── routes/web.php
└── tests/
    ├── Feature/                # Pengujian alur end-to-end per modul
    └── Unit/                   # Pengujian service layer
```

### Skema Data Utama

```mermaid
erDiagram
    RUANGAN ||--o{ USERS : "menaungi"
    KATEGORI_BARANG ||--o{ BARANG_PERSEDIAAN : "mengelompokkan"
    USERS ||--o{ PERMINTAAN : "mengajukan"
    RUANGAN ||--o{ PERMINTAAN : "unit pengusul"
    PERMINTAAN ||--|{ PERMINTAAN_DETAIL : "berisi"
    BARANG_PERSEDIAAN ||--o{ PERMINTAAN_DETAIL : "diminta"
    BARANG_PERSEDIAAN ||--o{ TRANSAKSI_PERSEDIAAN : "mutasi"
    STOK_OPNAME ||--|{ STOK_OPNAME_DETAIL : "berisi"
    BARANG_PERSEDIAAN ||--o{ STOK_OPNAME_DETAIL : "dihitung"
    USERS ||--o{ USULAN_BARANG : "mengusulkan"
```

---

## 🚀 Instalasi

### Prasyarat

- PHP **8.2+** dengan ekstensi `gd`, `pdo_mysql`, `mbstring`, `zip`, `xml`
- Composer 2
- Node.js **18+** dan npm
- MySQL 8 / MariaDB 10.6+
- Disarankan: [Laragon](https://laragon.org/) (Windows) atau Laravel Herd/Valet

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/Diva-Baihaqi/SIPABIS-Demo.git
cd SIPABIS-Demo

# 2. Install dependensi PHP & JavaScript
composer install
npm install

# 3. Siapkan file environment
cp .env.example .env
php artisan key:generate
```

Atur koneksi database dan zona waktu di `.env`:

```dotenv
APP_NAME=SIPABIS
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipabis
DB_USERNAME=root
DB_PASSWORD=
```

Lanjutkan dengan migrasi, data awal, dan build aset:

```bash
# 4. Buat tabel & isi data awal (ruangan, kategori, pengguna, 727 barang)
php artisan migrate --seed

# 5. Hubungkan folder storage publik
php artisan storage:link

# 6. Build aset frontend
npm run build        # production
# atau
npm run dev          # mode pengembangan dengan hot reload

# 7. Jalankan server
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser. 🎉

### Menjalankan dengan Docker / GHCR

Image kontainer resmi tersedia di **GitHub Container Registry** ([Packages](https://github.com/Diva-Baihaqi/SIPABIS-Demo/pkgs/container/sipabis-demo)):

```bash
# Unduh image rilis terbaru dari GHCR
docker pull ghcr.io/diva-baihaqi/sipabis-demo:latest

# Atau bangun & jalankan aplikasi beserta MySQL 8.0 via Docker Compose
APP_KEY="base64:$(openssl rand -base64 32)" docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

---

## 🔑 Akun Demo

Seeder menyediakan akun uji berikut. Login cukup dengan **NIP** sebagai username.

| Peran | Username (NIP) | Kata Sandi |
|---|---|---|
| 🛠️ Admin | `admin` | `admin` |
| 👤 Pegawai | `user` | `user` |
| 👔 Pimpinan | `pimpinan` | `pimpinan` |

> [!WARNING]
> Akun di atas hanya untuk pengembangan dan pengujian. **Hapus atau ganti kata sandinya** sebelum aplikasi dipakai di server production. Akun pegawai hasil impor memakai NIP sebagai kata sandi awal, jadi minta setiap pegawai segera menggantinya lewat menu **Profil Saya**.

---

## 🤖 Konfigurasi Asisten AI

Asisten AI dapat diatur langsung dari aplikasi, tanpa perlu mengubah kode:

1. Login sebagai **Admin**, lalu buka menu **Administrasi → Manajemen AI**.
2. Tempel API key pada panel **Koneksi Cepat**. Sistem akan mengenali penyedia dari format key-nya.
3. Klik **Konek**. Koneksi langsung diuji dan, jika berhasil, diaktifkan.
4. (Opsional) Tambahkan penyedia lain sebagai **cadangan** agar chatbot tetap berjalan saat kuota penyedia utama habis.
5. Atur mode hemat token, batas panjang jawaban, *temperature*, dan aturan perilaku asisten.

| Penyedia | Keterangan |
|---|---|
| Google Gemini | Protokol native Gemini, mendukung mode hemat token |
| Groq · OpenRouter · Mistral · DeepSeek · OpenAI | Protokol OpenAI-compatible |
| Custom | Endpoint lain yang kompatibel dengan OpenAI API |

> [!NOTE]
> API key disimpan **terenkripsi** di database dan tidak pernah ditampilkan utuh di antarmuka. Sebagai alternatif, Gemini juga bisa diatur lewat `.env` (`GEMINI_API_KEY`, `GEMINI_MODEL`).

---

## 🧪 Pengujian

Proyek ini dilengkapi **230+ automated tests** (Feature & Unit) yang mencakup autentikasi, permintaan ATK, verifikasi & serah terima, mutasi stok, kartu kendali, stock opname, ekspor laporan, upload foto, manajemen pengguna, gateway AI, halaman panduan, hingga statistik profil.

```bash
# Jalankan seluruh pengujian
php artisan test

# Jalankan satu file pengujian
php artisan test tests/Feature/PermintaanPegawaiTest.php

# Filter berdasarkan nama
php artisan test --filter=StockOpname
```

Pengujian memakai trait `RefreshDatabase`, sehingga database uji dibuat ulang setiap kali pengujian dijalankan.

---

## 🌍 Deployment ke Production

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Daftar periksa sebelum rilis:

- [ ] `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] `APP_URL` sesuai domain, gunakan HTTPS
- [ ] `APP_TIMEZONE=Asia/Jakarta`
- [ ] Akun demo (`admin`, `user`, `pimpinan`) dihapus atau kata sandinya diganti
- [ ] Folder `storage/` dan `bootstrap/cache/` dapat ditulis oleh web server
- [ ] Backup database terjadwal

---

## 🤝 Kontribusi

Kontribusi sangat terbuka! Silakan baca panduan lengkap pada [CONTRIBUTING.md](.github/CONTRIBUTING.md), patuhi [CODE_OF_CONDUCT.md](.github/CODE_OF_CONDUCT.md), dan laporkan celah keamanan melalui alur di [SECURITY.md](.github/SECURITY.md).

Ringkasan alur singkat:
1. Buat branch baru dari `main`: `git checkout -b feat/nama-fitur`
2. Tulis pengujian terlebih dahulu, lalu implementasikan perubahan
3. Pastikan seluruh pengujian lulus (`php artisan test`) dan kode rapi (`./vendor/bin/pint --test`)
4. Gunakan format [Conventional Commits](https://www.conventionalcommits.org/) dan buka Pull Request menggunakan template yang tersedia

Lihat catatan lengkap setiap versi pada [CHANGELOG.md](CHANGELOG.md).

---

## 📄 Lisensi & Kredit

Perangkat lunak ini dirilis di bawah [MIT License](LICENSE). SIPABIS dikembangkan untuk kebutuhan operasional **Subbagian Umum BPS Kabupaten Cirebon**; logo serta identitas visual Badan Pusat Statistik adalah milik BPS dan hanya digunakan untuk keperluan resmi.

Dibangun di atas [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev), [Chart.js](https://www.chartjs.org), dan [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet).

<div align="center">

---

**SIPABIS** · Badan Pusat Statistik Kabupaten Cirebon

<sub>Melayani dengan BerAKHLAK 🇮🇩</sub>

</div>
