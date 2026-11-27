# Kebijakan Keamanan (Security Policy)

## Versi yang Didukung

Pembaruan dan perbaikan celah keamanan diprioritaskan pada versi rilis aktif berikut:

| Versi | Status Dukungan |
| --- | --- |
| `1.x` (`main`) | :white_check_mark: Didukung penuh |
| `< 1.0` | :x: Tidak lagi didukung |

---

## Mekanisme Pelindungan Bawaan di SIPABIS

SIPABIS menerapkan lapisan pengamanan berikut:
- **Enkripsi API Key AI**: seluruh kredensial penyedia AI pada tabel `ai_providers` disimpan menggunakan Laravel `'encrypted'` *cast* dan disamarkan (`masked_api_key`) di antarmuka maupun log error.
- **Kontrol Akses Berbasis Peran (`RoleMiddleware`)**: pemisahan hak akses ketat antara `admin`, `pimpinan`, dan `pegawai` (termasuk pembatasan akses buku kendali antar unit kerja dan pengesahan BASO).
- **Pembatasan Percobaan Login**: proteksi *rate-limiting* pada endpoint autentikasi (`LoginRequest`) dan pembatasan endpoint `/api/chat`.
- **Anti CSRF & Validasi Transaksional**: seluruh aksi mutasi stok menggunakan transaksi database (`DB::transaction`) dan proteksi token CSRF.

---

## Melaporkan Celah Keamanan

**Mohon jangan membuka *Public Issue*** apabila Anda menemukan kerentanan keamanan (seperti kebocoran data, *bypass* otorisasi, injeksi SQL/XSS, atau paparan kredensial).

Silakan laporkan secara privat melalui fitur **GitHub Private Vulnerability Reporting** / pesan langsung kepada pengelola repositori (`@Diva-Baihaqi`) dengan menyertakan:
1. Deskripsi singkat kerentanan dan dampaknya.
2. Langkah reproduksi yang jelas (*proof of concept* minimal).
3. Lokasi berkas/rute terkait (jika diketahui).

Laporan yang masuk akan ditinjau secepat mungkin untuk verifikasi dan penerbitan tambalan (*patch*) keamanan sebelum diumumkan secara terbuka.
