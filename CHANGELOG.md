# Changelog

Seluruh perubahan penting pada proyek **SIPABIS (Sistem Informasi Persediaan Barang Pakai Habis)** dicatat dalam berkas ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan penomoran versi mengacu pada [Semantic Versioning](https://semver.org/).

---

## [1.0.0] — 2026-11-27

### Rilis Perdana (`v1.0.0` — Public Portfolio & Demo Edition)

Peluncuran publik edisi portofolio dan demo dari platform terintegrasi tata kelola persediaan barang pakai habis **SIPABIS** (studi kasus instansi pemerintah, mencakup alur end-to-end bagi **Pegawai**, **Admin Persediaan**, dan **Pimpinan**) dengan seluruh data pegawai, penanggung jawab unit kerja, NIP, email, dan spesimen tanda tangan berbentuk **data simulasi/dummy** (`ghcr.io/diva-baihaqi/sipabis-demo`).

### Ditambahkan
- **Portal Publik & Katalog Barang**: halaman beranda interaktif, katalog barang terpopuler, daftar semua barang (727 item riil dari 8 klasifikasi), alur pelayanan visual, serta Halaman khusus **Buku Panduan Pengguna** (`/panduan`) dengan 3 tab peran, pencarian lintas bab, pratinjau/unduh PDF resmi (`BUKU-SIPABIS-BPS-Kabupaten-Cirebon.pdf`), dan dukungan cetak rapi.
- **Alur Permintaan ATK Digital**: formulir pengajuan dengan katalog bergambar, *searchable visual combobox*, pratinjau lightbox foto, filter riwayat real-time tanpa reload (status, kategori, kata kunci), dan cetak Formulir Permintaan Persediaan **PSD-133** standar BPS.
- **Verifikasi, Serah Terima & Barang Masuk Admin**: antrean verifikasi dan penyesuaian kuota, serah terima yang memotong stok secara transaksional, pencatatan pengadaan barang masuk beserta nomor SPJ, serta koreksi/pembatalan dengan sinkronisasi stok otomatis.
- **Manajemen Katalog & Optimasi Foto**: CRUD kategori dan barang berindeks unik komposit (`kode_sakti`, `kode_barang`), unggah foto *drag & drop* maupun tempel *clipboard* (`Ctrl+V`) dengan kompresi WebP otomatis `< 100 KB` terkelompok per subfolder kategori.
- **Kartu Kendali & Laporan Multi-Format**: Buku Kendali per ruangan dan Kartu Stok per barang dengan saldo awal & berjalan otomatis serta nilai rupiah; mendukung ekspor **Excel**, **Word**, dan cetak **PDF** per unit kerja maupun rekap kolektif.
- **Stock Opname & Pengesahan BASO**: pembukaan sesi dengan *snapshot* stok dibekukan, pencatatan hitung fisik & selisih otomatis, finalisasi penyesuaian stok, hingga pengesahan resmi **Berita Acara Stock Opname (BASO)** oleh Pimpinan.
- **Asisten AI Multi-Provider**: widget chatbot asisten virtual dan panel admin **Manajemen AI** mendukung protokol Google Gemini & OpenAI-compatible (Groq, OpenRouter, Mistral, DeepSeek, OpenAI, custom), enkripsi API key, mode hemat token, sandbox uji coba, dan *auto-fallback*.
- **Keamanan Akun, Profil & Pengalaman Pengguna**: login NIP dengan *rate-limiting*, halaman **Profil Saya** dengan statistik aktivitas *role-aware* real-time, halaman logout beranimasi, penanganan ramah `419 Page Expired`, serta informasi waktu WIB real-time dan unit kerja di topbar.
- **Infrastruktur Repo, Docker & CI/CD**: *workflow* GitHub Actions (MySQL 8.0 + Vite build + 230 automated tests PHPUnit), image kontainer **GHCR**, serta tata kelola `.github/` lengkap (*Contributing*, *Code of Conduct*, *Security Policy*, template isu & PR, Dependabot).
