# Panduan Kontribusi — SIPABIS

Terima kasih atas ketertarikan Anda untuk berkontribusi dalam pengembangan **SIPABIS (Sistem Informasi Persediaan Barang Pakai Habis)** untuk BPS Kabupaten Cirebon.

---

## Alur Pengembangan

1. **Sinkronkan branch utama**
   ```bash
   git checkout main
   git pull origin main
   ```
2. **Buat branch baru** sesuai jenis perubahan:
   - `feat/<nama-fitur>` untuk fitur baru
   - `fix/<nama-perbaikan>` untuk perbaikan bug
   - `docs/<topik>` untuk dokumentasi
   - `refactor/<bagian>` untuk perapihan kode
3. **Terapkan pendekatan TDD** (*Test-Driven Development*):
   - Tulis test gagal terlebih dahulu di `tests/Feature/` atau `tests/Unit/`.
   - Implementasikan kode seminimal dan serapi mungkin hingga test lulus.
   - Jangan meletakkan logika bisnis kompleks di Controller; gunakan **Service Layer** di `app/Services/`.

---

## Standar Kode & Verifikasi Wajib

Sebelum membuat Pull Request, pastikan semua pemeriksaan berikut lulus tanpa galat:

```bash
# 1. Jalankan seluruh suite pengujian (wajib 0 failures)
php artisan test

# 2. Periksa format kode Laravel Pint
./vendor/bin/pint --test

# 3. Pastikan aset frontend terkompilasi bersih
npm run build
```

### Aturan Gaya CSS & Blade
- Proyek ini memakai **Tailwind CSS v3** (`tailwind.config.js`). Hindari utilitas khusus v4 seperti `shadow-xs` pada komponen baru; gunakan `shadow-sm` atau kelas yang terdefinisi pada konfigurasi.
- Gunakan kelas `.no-print` pada elemen navigasi/tombol interaktif di halaman yang mendukung fitur cetak (`window.print()`).

---

## Konvensi Commit

Gunakan format [Conventional Commits](https://www.conventionalcommits.org/):

- `feat(modul): deskripsi singkat fitur`
- `fix(modul): deskripsi perbaikan bug`
- `docs: pembaruan dokumentasi`
- `test(modul): penambahan pengujian otomatis`
- `chore: pemeliharaan dependensi atau CI`

Contoh:
```text
feat(permintaan): tambah filter kategori pada riwayat permintaan
```

---

## Catatan Keamanan & Privasi

- **Dilarang keras** melakukan commit terhadap `.env`, kunci API (`AI/apikey.txt`, token OAuth, kredensial SMTP/DB), atau data pegawai/transaksi sensitif nyata.
- Seluruh input formulir wajib divalidasi dan rute administratif dilindungi middleware peran (`role:admin`, `role:pimpinan`, `role:pegawai`).
