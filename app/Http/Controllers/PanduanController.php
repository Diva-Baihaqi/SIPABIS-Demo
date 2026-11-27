<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PanduanController extends Controller
{
    private const BOOK_RELATIVE_PATH = 'doc/BUKU_SIPABIS.pdf';
    private const BOOK_DOWNLOAD_NAME = 'BUKU-SIPABIS-BPS-Kabupaten-Cirebon.pdf';
    private const BOOK_COVER_THUMB = 'images/cover-sipabis-thumb.webp';

    /**
     * Menampilkan halaman khusus Buku Panduan Penggunaan SIPABIS dengan 3 tab peran.
     */
    public function index(Request $request): View
    {
        $requestedTab = $request->query('tab', 'pegawai');
        $activeTab = in_array($requestedTab, ['pegawai', 'pimpinan', 'admin'], true)
            ? $requestedTab
            : 'pegawai';

        $guides = [
            'pegawai' => $this->buildPegawaiGuide(),
            'pimpinan' => $this->buildPimpinanGuide(),
            'admin' => $this->buildAdminGuide(),
        ];

        $bookMeta = $this->buildBookMeta();

        return view('panduan.index', compact('guides', 'activeTab', 'bookMeta'));
    }

    /**
     * Mengunduh berkas resmi Buku Panduan SIPABIS (PDF B5).
     */
    public function downloadBuku(): BinaryFileResponse
    {
        $path = $this->bookFilePath();
        abort_unless(is_file($path), 404, 'Berkas Buku Panduan SIPABIS belum tersedia.');

        return response()->download($path, self::BOOK_DOWNLOAD_NAME, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Membuka pratinjau berkas resmi Buku Panduan SIPABIS langsung di peramban.
     */
    public function previewBuku(): BinaryFileResponse
    {
        $path = $this->bookFilePath();
        abort_unless(is_file($path), 404, 'Berkas Buku Panduan SIPABIS belum tersedia.');

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.self::BOOK_DOWNLOAD_NAME.'"',
        ]);
    }

    /**
     * Lokasi berkas PDF utama Buku SIPABIS di folder publik (single source of truth).
     */
    private function bookFilePath(): string
    {
        return public_path(self::BOOK_RELATIVE_PATH);
    }

    /**
     * Metadata dinamis untuk tombol & kartu unduh Buku SIPABIS.
     */
    private function buildBookMeta(): array
    {
        $path = $this->bookFilePath();
        $available = is_file($path);
        $sizeLabel = null;

        if ($available) {
            $bytes = filesize($path);
            if ($bytes !== false) {
                $mb = $bytes / (1024 * 1024);
                $sizeLabel = number_format($mb, 1, ',', '.').' MB';
            }
        }

        $coverExists = is_file(public_path(self::BOOK_COVER_THUMB));

        return [
            'available' => $available,
            'title' => 'Buku Panduan SIPABIS',
            'subtitle' => 'Panduan Lengkap, Arsitektur & Tata Kelola Persediaan ATK',
            'pages' => 69,
            'format' => 'PDF B5',
            'edition' => 'Edisi Pertama • 2026',
            'size_label' => $sizeLabel,
            'cover_exists' => $coverExists,
            'cover_url' => $coverExists ? asset(self::BOOK_COVER_THUMB) : null,
        ];
    }

    /**
     * Menyusun panduan komprehensif untuk User Pegawai (Tab 1).
     */
    private function buildPegawaiGuide(): array
    {
        $chapters = [
            [
                'id' => 'pegawai-login',
                'number' => '01',
                'category' => 'Persiapan Awal',
                'title' => 'Cara Masuk Sistem (Login NIP) & Keluar Aman',
                'summary' => 'Langkah pertama sebelum meminta ATK adalah masuk menggunakan NIP resmi Anda agar setiap permintaan otomatis tercatat ke tim kerja/ruangan Anda.',
                'when_to_use' => 'Dipakai setiap kali ingin mengajukan barang, mengecek riwayat ruangan, atau mencetak bukti PSD-133.',
                'menu_path' => 'Homepage → Akses Pegawai → Halaman Login',
                'steps' => [
                    [
                        'title' => 'Buka halaman masuk SIPABIS',
                        'detail' => 'Dari halaman depan (Homepage), klik tombol hijau limau "Akses Pegawai" di pojok kanan atas layar.',
                    ],
                    [
                        'title' => 'Masukkan NIP dan Kata Sandi',
                        'detail' => 'Ketik Nomor Induk Pegawai (NIP) 18 digit tanpa spasi pada kolom ID, lalu masukkan kata sandi Anda (akun baru biasanya memakai NIP sebagai sandi awal).',
                    ],
                    [
                        'title' => 'Keluar dengan aman setelah selesai',
                        'detail' => 'Jika memakai komputer bersama di kantor, klik ikon Keluar (pintu panah) di bagian paling bawah sidebar kiri. Anda akan diarahkan ke halaman konfirmasi keluar aman.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Segera ubah kata sandi bawaan Anda di menu Profil Saya setelah berhasil masuk pertama kali agar akun lebih aman.'],
                    ['type' => 'warning', 'text' => 'Salah memasukkan sandi lebih dari 5 kali berturut-turut akan membuat sistem mengunci percobaan login selama ~1 menit untuk keamanan. Tunggu sebentar atau hubungi Admin Subbag Umum.'],
                ],
            ],
            [
                'id' => 'pegawai-dashboard',
                'number' => '02',
                'category' => 'Beranda Kerja',
                'title' => 'Mengenal Tampilan Dashboard & Info Topbar',
                'summary' => 'Dashboard merangkum seluruh aktivitas permintaan ATK Anda dan tren pemakaian barang di unit ruangan Anda selama 6 bulan terakhir.',
                'when_to_use' => 'Otomatis tampil pertama kali setiap berhasil masuk ke SIPABIS.',
                'menu_path' => 'Menu Utama → Dashboard',
                'steps' => [
                    [
                        'title' => 'Cek kartu status pengajuan Anda',
                        'detail' => 'Di bagian atas dashboard terdapat ringkasan jumlah pengajuan yang masih menunggu verifikasi, pengajuan yang sudah disetujui dan siap diambil, serta total riwayat yang sudah selesai.',
                    ],
                    [
                        'title' => 'Pantau grafik serapan tim ruangan',
                        'detail' => 'Grafik tren membantu Anda dan rekan satu fungsi melihat bulan apa saja pemakaian ATK paling tinggi (misalnya saat pelatihan sensus atau survei besar).',
                    ],
                    [
                        'title' => 'Pastikan identitas ruangan & jam WIB di topbar',
                        'detail' => 'Lihat pojok kanan atas layar: di situ tertera waktu realtime WIB dan kartu Unit Kerja Anda (misal: Fungsi Statistik Sosial). Jika ruangan yang tampil tidak sesuai mutasi terbaru, lapor Admin untuk dipindahkan.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Kartu jam realtime WIB di atas berguna untuk mengecek apakah masih masuk jam pelayanan ambil barang di Subbag Umum (Senin–Kamis 08.00–16.00 WIB, Jumat 08.00–16.30 WIB, istirahat 12.00–13.00 WIB).'],
                ],
            ],
            [
                'id' => 'pegawai-katalog',
                'number' => '03',
                'category' => 'Cek Stok Gudang',
                'title' => 'Mengecek Katalog Persediaan Sebelum Meminta',
                'summary' => 'Daripada bolak-balik ke ruang Subbag Umum hanya untuk menanyakan "Kertas A4 masih ada nggak?", Anda bisa melihat stok riil langsung dari meja kerja.',
                'when_to_use' => 'Sebelum membuat pengajuan ATK baru atau saat menyusun kebutuhan perlengkapan rapat/survei.',
                'menu_path' => 'Menu Utama → Katalog Persediaan',
                'steps' => [
                    [
                        'title' => 'Cari nama barang atau saring kategori',
                        'detail' => 'Ketik nama barang di kotak pencarian (misal "amplop", "tinta", "spidol") atau pilih dari 8 kategori persediaan BPS seperti Perlengkapan Sensus & Survei atau Tinta Printer.',
                    ],
                    [
                        'title' => 'Perhatikan status ketersediaan stok',
                        'detail' => 'Cek angka stok aktif yang tersedia serta kode/foto barangnya agar tidak tertukar antara barang sejenis (misal kertas F4 vs A4).',
                    ],
                ],
                'statuses' => [
                    ['label' => 'Stok Tersedia', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'meaning' => 'Jumlah barang cukup aman di rak gudang dan siap untuk diajukan.'],
                    ['label' => 'Stok Menipis', 'class' => 'bg-amber-50 text-amber-800 border-amber-200', 'meaning' => 'Sisa barang berada di bawah batas minimum; ajukan secukupnya agar terbagi merata ke unit lain.'],
                    ['label' => 'Stok Habis (0)', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'meaning' => 'Barang kosong di gudang; Anda tidak bisa mengajukannya langsung, gunakan fitur Usulan Barang Baru.'],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Gunakan balon chat Asisten AI di pojok kanan bawah jika ingin tanya cepat "Stok kertas F4 masih sisa berapa?" tanpa membuka tabel katalog.'],
                ],
            ],
            [
                'id' => 'pegawai-ajukan-atk',
                'number' => '04',
                'category' => 'Transaksi Utama',
                'title' => 'Ajukan ATK Baru (Etalase Foto & Lightbox Zoom)',
                'summary' => 'Formulir pengajuan ATK digital dilengkapi galeri foto barang agar Anda tidak salah pilih ukuran map, jenis pulpen, atau tipe toner printer.',
                'when_to_use' => 'Setiap kali membutuhkan ATK untuk operasional kantor harian, rapat koordinasi, maupun pelatihan petugas sensus/survei.',
                'menu_path' => 'Layanan ATK Pegawai → Ajukan ATK Baru',
                'steps' => [
                    [
                        'title' => 'Tulis keperluan / nama kegiatan dengan jelas',
                        'detail' => 'Pada kotak Bagian 1 ("Peruntukan / Keperluan Pengajuan ATK"), tulis nama kegiatannya secara spesifik, contoh: "Pelatihan Petugas Sakernas 2026" atau "Operasional Ruangan Fungsi Nerwilis bulan November".',
                    ],
                    [
                        'title' => 'Pilih barang lewat Etalase Foto atau Dropdown Cari Barang',
                        'detail' => 'Di Bagian 2, Anda bisa langsung mengklik kartu barang pada Etalase Katalog Visual di atas tabel, atau memilihnya lewat dropdown pencarian di dalam baris tabel. Barang yang dipilih otomatis muncul thumbnail fotonya beserta sisa stok gudang.',
                    ],
                    [
                        'title' => 'Klik foto barang jika ragu (Lightbox Zoom)',
                        'detail' => 'Klik thumbnail foto barang di baris item maupun di etalase untuk memperbesar gambar ukuran penuh supaya Anda yakin barang tersebut memang yang dicari.',
                    ],
                    [
                        'title' => 'Isi jumlah & catatan khusus tiap barang',
                        'detail' => 'Masukkan jumlah yang dibutuhkan (sistem menjaga agar tidak melebihi stok gudang), lalu tambahkan catatan opsional seperti "warna hitam" atau "tinta biru". Klik "+ Tambah Baris Barang" jika butuh beberapa jenis barang sekaligus.',
                    ],
                    [
                        'title' => 'Kirim pengajuan ke Subbag Umum',
                        'detail' => 'Periksa ulang ringkasan jumlah jenis barang di bagian bawah, lalu klik tombol "Kirim Pengajuan ke Subbag Umum" dan konfirmasi pada jendela dialog yang muncul.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Gabungkan beberapa barang untuk satu kegiatan yang sama ke dalam satu formulir pengajuan agar nomor dokumen PSD-133 rapi dan mempermudah pengambilan barang sekaligus.'],
                    ['type' => 'warning', 'text' => 'Barang yang stoknya 0 (habis) tidak bisa diajukan di formulir ini. Jika tetap butuh, ajukan lewat menu Usulan Barang Baru.'],
                ],
            ],
            [
                'id' => 'pegawai-status-psd133',
                'number' => '05',
                'category' => 'Pantau & Ambil',
                'title' => 'Pantau Status, Pembatalan, & Cetak Formulir PSD-133',
                'summary' => 'Setelah dikirim, pantau perkembangan permintaan Anda di sini agar tahu kapan waktunya turun mengambil barang fisik ke Subbag Umum.',
                'when_to_use' => 'Setelah mengirim pengajuan ATK, saat ingin membatalkan, atau saat hendak mencetak tanda bukti dinas.',
                'menu_path' => 'Layanan ATK Pegawai → Permintaan ATK Saya',
                'steps' => [
                    [
                        'title' => 'Cek status tiket pengajuan secara berkala',
                        'detail' => 'Buka "Permintaan ATK Saya" dan gunakan filter/pencarian untuk melihat tiket Anda. Klik tombol Detail untuk melihat apakah ada catatan atau penyesuaian jumlah kuota dari Admin.',
                    ],
                    [
                        'title' => 'Batalkan jika ada kesalahan (saat masih Menunggu)',
                        'detail' => 'Selama Admin belum memverifikasi (status masih Menunggu), tombol Hapus/Batalkan masih aktif jika Anda salah pilih barang atau kegiatan batal.',
                    ],
                    [
                        'title' => 'Ambil barang fisik saat status Disetujui',
                        'detail' => 'Begitu status berubah menjadi Disetujui, silakan datang ke ruang Subbag Umum untuk serah terima fisik barang. Saat Admin menekan Serah Terima, status tiket menjadi Selesai.',
                    ],
                    [
                        'title' => 'Cetak Formulir Standar BPS (PSD-133)',
                        'detail' => 'Klik ikon/tombol Cetak (PSD-133) pada detail pengajuan untuk menghasilkan formulir permintaan barang resmi BPS yang siap diprint atau disimpan sebagai PDF arsip kegiatan.',
                    ],
                ],
                'statuses' => [
                    ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-800 border-amber-200', 'meaning' => 'Tiket baru masuk antrean Subbag Umum dan belum diperiksa Admin. Masih bisa dibatalkan sendiri.'],
                    ['label' => 'Disetujui', 'class' => 'bg-sky-50 text-sky-700 border-sky-200', 'meaning' => 'Kuota sudah disetujui Admin; barang fisik siap Anda ambil langsung ke Subbag Umum.'],
                    ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'meaning' => 'Barang sudah diserahkan ke Anda dan resmi masuk ke Buku Kendali Ruangan.'],
                    ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'meaning' => 'Pengajuan tidak dapat dipenuhi. Cek catatan alasan penolakan dari Admin di halaman detail.'],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Jumlah yang disetujui Admin terkadang disesuaikan dengan stok tersisa agar unit fungsi lain tetap kebagian. Selalu cek kolom "Disetujui" di detail tiket sebelum mengambil barang.'],
                ],
            ],
            [
                'id' => 'pegawai-usulan-barang',
                'number' => '06',
                'category' => 'Pengadaan Baru',
                'title' => 'Mengusulkan Barang Baru (Usulan Barang)',
                'summary' => 'Butuh ATK jenis baru yang belum ada di katalog, atau barang tertentu sudah habis dan sangat mendesak untuk kegiatan? Sampaikan aspirasi Anda lewat fitur ini.',
                'when_to_use' => 'Saat barang yang dibutuhkan belum tersedia di Katalog Persediaan atau perlu direncanakan untuk belanja pengadaan berikutnya.',
                'menu_path' => 'Layanan ATK Pegawai → Usulan Barang Baru',
                'steps' => [
                    [
                        'title' => 'Klik tombol Ajukan Usulan Baru',
                        'detail' => 'Masukkan nama barang yang diusulkan dengan spesifik (contoh: "Flashdisk 64GB USB 3.0" atau "Map Plastik Kancing F4"), satuan, dan perkiraan jumlah kebutuhan.',
                    ],
                    [
                        'title' => 'Tuliskan urgensi / alasan pengadaan',
                        'detail' => 'Jelaskan kegiatan apa yang membutuhkan barang tersebut sehingga Admin & Pimpinan Subbag Umum mudah menilai prioritasnya.',
                    ],
                    [
                        'title' => 'Pantau status tindak lanjut Admin',
                        'detail' => 'Anda bisa melihat langsung apakah usulan masih Menunggu, Disetujui untuk dibeli, Sudah Terealisasi (barang sudah masuk katalog), atau Ditolak beserta penjelasannya. Usulan yang masih Menunggu juga bisa Anda batalkan.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Cek dulu daftar usulan yang sudah ada sebelum membuat usulan baru agar tidak terjadi usulan ganda dengan barang yang persis sama.'],
                ],
            ],
            [
                'id' => 'pegawai-buku-kendali',
                'number' => '07',
                'category' => 'Laporan Ruangan',
                'title' => 'Membaca Buku Kendali Ruangan Sendiri',
                'summary' => 'Semua ATK yang sudah diambil oleh seluruh pegawai dalam satu ruangan/fungsi tercatat rapi dan transparan di Buku Kendali Ruangan Anda.',
                'when_to_use' => 'Saat tim fungsi ingin mengevaluasi pemakaian kertas/tinta bulanan atau menyusun laporan SPJ kegiatan.',
                'menu_path' => 'Layanan ATK Pegawai → Buku Kendali Ruangan',
                'steps' => [
                    [
                        'title' => 'Buka Buku Kendali Ruangan',
                        'detail' => 'Menu ini otomatis membuka catatan khusus ruangan tempat Anda ditempatkan (misal Subbagian Umum, Statistik Sosial, Produksi, Distribusi, Nerwilis, atau IPDS).',
                    ],
                    [
                        'title' => 'Saring periode tanggal atau cari nama barang',
                        'detail' => 'Gunakan filter bulan/tahun atau kotak pencarian untuk melacak kapan suatu barang diambil dan siapa pemohonnya.',
                    ],
                    [
                        'title' => 'Cetak atau unduh ke Excel / Word',
                        'detail' => 'Jika butuh dokumen rekap untuk dilampirkan di laporan ruangan, klik tombol Cetak PDF, Export Excel, atau Export Word yang tersedia di bagian atas.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Semua anggota satu ruangan dapat melihat rekap ruangan yang sama sehingga koordinasi pemakaian stok antar rekan tim jauh lebih terbuka.'],
                ],
            ],
            [
                'id' => 'pegawai-profil-ai',
                'number' => '08',
                'category' => 'Profil & AI',
                'title' => 'Ubah Profil, Foto, Sandi & Tanya Asisten AI SIPABIS',
                'summary' => 'Perbarui data diri Anda dengan mudah dan manfaatkan Asisten AI untuk bertanya seputar stok maupun cara pemakaian aplikasi kapan saja.',
                'when_to_use' => 'Saat ingin mengganti password, foto profil, atau butuh jawaban cepat tanpa harus mencari-cari menu.',
                'menu_path' => 'Lainnya → Profil Saya & Widget Chat Pojok Kanan Bawah',
                'steps' => [
                    [
                        'title' => 'Kelola akun di menu Profil Saya',
                        'detail' => 'Buka "Profil Saya" di sidebar atau klik foto profil Anda di pojok kiri bawah sidebar. Anda bisa memperbarui nama, foto profil, serta mengganti kata sandi secara mandiri.',
                    ],
                    [
                        'title' => 'Gunakan Asisten AI SIPABIS di pojok kanan bawah',
                        'detail' => 'Klik ikon chat hijau di sudut kanan bawah layar. Anda boleh mengetik pertanyaan santai seperti "Stok kertas A4 tinggal berapa?" atau "Cara cetak formulir PSD-133 gimana?".',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Untuk menjaga akurasi informasi stok kantor, Asisten AI merujuk pada katalog SIPABIS. Jika Admin sedang menonaktifkan layanan AI sementara untuk perawatan, ikon chat akan disembunyikan otomatis.'],
                ],
            ],
            [
                'id' => 'pegawai-kebiasaan-baik',
                'number' => '09',
                'category' => 'Tips Lapangan',
                'title' => 'Kebiasaan Baik Mengajukan ATK di Kantor BPS',
                'summary' => 'Beberapa trik sederhana agar pengajuan ATK Anda lebih cepat disetujui Admin Subbag Umum dan pencatatan kantor tetap tertib.',
                'when_to_use' => 'Sebagai pegangan praktis sehari-hari seluruh pegawai.',
                'menu_path' => 'Praktik Terbaik Operasional',
                'steps' => [
                    [
                        'title' => 'Ajukan sebelum hari-H kegiatan (H-1 atau H-2)',
                        'detail' => 'Khusus pelatihan petugas sensus/survei yang membutuhkan barang cukup banyak (map, tas, pulpen, buku catatan), ajukan minimal H-1 agar Admin punya waktu menyiapkan fisik barangnya.',
                    ],
                    [
                        'title' => 'Jangan membuat pengajuan berulang lalu dibiarkan',
                        'detail' => 'Begitu status sudah Disetujui, segera ambil barangnya ke Subbag Umum agar stok di aplikasi tidak tertahan dan segera berpindah ke status Selesai.',
                    ],
                    [
                        'title' => 'Gunakan catatan item secara spesifik',
                        'detail' => 'Jika butuh warna tinta atau jenis map tertentu, tulis jelas di kolom Catatan Item supaya saat Anda datang mengambil, barang sudah dipilihkan sesuai keinginan.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Jika butuh panduan alur cepat di dalam dashboard setelah login, Anda juga dapat membuka menu "Lainnya → Alur Pengguna".'],
                ],
            ],
        ];

        $faqs = [
            [
                'q' => 'Saya lupa kata sandi login SIPABIS, harus bagaimana?',
                'a' => 'Tenang, Anda cukup menghubungi Administrator / pengelola barang di Subbag Umum dan menyebutkan NIP Anda. Admin memiliki tombol "Reset Password" yang akan langsung mengembalikan kata sandi ke NIP Anda dalam 1 klik.',
            ],
            [
                'q' => 'Kenapa barang yang saya cari tidak bisa diketik jumlah lebih dari stok?',
                'a' => 'Sistem memang menjaga secara otomatis agar jumlah permintaan tidak melampaui sisa fisik di gudang. Jika kebutuhan kegiatan Anda melebihi stok yang tersisa, ajukan sesuai sisa yang ada lalu sampaikan kekurangan kebutuhannya melalui menu Usulan Barang Baru.',
            ],
            [
                'q' => 'Saya minta 10 buah, tapi pas dicek cuma disetujui 5 buah. Kenapa?',
                'a' => 'Admin berwenang menyesuaikan kuota yang disetujui apabila persediaan gudang harus dibagi ke beberapa tim fungsi yang sama-sama berkegiatan. Anda dapat membaca penjelasan resminya pada kolom Catatan Verifikasi di halaman detail tiket.',
            ],
            [
                'q' => 'Barang yang saya ajukan salah tulis atau acaranya batal, bisa dibatalkan?',
                'a' => 'Bisa, selama statusnya masih "Menunggu" (belum diverifikasi Admin). Buka menu "Permintaan ATK Saya", lalu klik tombol batalkan/hapus pada pengajuan tersebut. Jika sudah berstatus Disetujui, sampaikan langsung ke Admin Subbag Umum.',
            ],
            [
                'q' => 'Kenapa saat klik foto barang di form pengajuan gambarnya kecil?',
                'a' => 'Klik langsung gambar thumbnail barang tersebut (atau pada etalase visual) untuk membuka fitur Zoom Lightbox layar penuh sehingga bentuk, ukuran, atau kode barang terlihat sangat jelas.',
            ],
        ];

        return [
            'key' => 'pegawai',
            'tab_label' => 'Panduan Pegawai',
            'role_badge' => 'Tab 1 • Pegawai Organik / Tim Fungsi',
            'hero_title' => 'Panduan Lengkap untuk Pegawai BPS',
            'hero_desc' => 'Mulai dari cek stok di meja kerja, mengajukan ATK dengan galeri foto barang, mencetak formulir PSD-133, mengusulkan barang baru, hingga melihat Buku Kendali ruangan Anda.',
            'read_time' => '9 Bab Lengkap • Bahasa Sehari-hari',
            'accent' => 'teal',
            'roadmap' => [
                ['step' => '1', 'title' => 'Masuk Pakai NIP', 'desc' => 'Login ke akun unit ruangan'],
                ['step' => '2', 'title' => 'Cek Katalog & Foto', 'desc' => 'Pastikan stok & jenis barang'],
                ['step' => '3', 'title' => 'Ajukan ATK Baru', 'desc' => 'Isi keperluan & kuantitas'],
                ['step' => '4', 'title' => 'Tunggu Disetujui', 'desc' => 'Cek tiket & cetak PSD-133'],
                ['step' => '5', 'title' => 'Ambil di Subbag Umum', 'desc' => 'Masuk Buku Kendali Ruangan'],
            ],
            'chapters' => $this->enrichSearchText($chapters),
            'faqs' => $faqs,
        ];
    }

    /**
     * Menyusun panduan komprehensif untuk User Pimpinan (Tab 2).
     */
    private function buildPimpinanGuide(): array
    {
        $chapters = [
            [
                'id' => 'pimpinan-peran',
                'number' => '01',
                'category' => 'Peran Eksekutif',
                'title' => 'Peran & Wewenang Pimpinan di SIPABIS',
                'summary' => 'Akun Pimpinan (Kepala BPS Kabupaten Cirebon) dirancang ringkas tanpa menu input teknis gudang, berfokus penuh pada pengawasan, evaluasi anggaran ATK, dan pengesahan dokumen resmi.',
                'when_to_use' => 'Digunakan untuk pemantauan berkala, persiapan rapat evaluasi anggaran, dan penandatanganan digital Berita Acara Stock Opname.',
                'menu_path' => 'Login Pimpinan → Dashboard & Menu Pengawasan',
                'steps' => [
                    [
                        'title' => 'Masuk menggunakan akun Pimpinan',
                        'detail' => 'Gunakan NIP dan sandi akun Pimpinan. Setelah masuk, sidebar otomatis menampilkan menu khusus pengawasan seperti Dashboard Eksekutif, Usulan Barang Pegawai, Buku Kartu Kendali, dan Stock Opname (BASO).',
                    ],
                    [
                        'title' => 'Fokus pada 3 pilar kendali pimpinan',
                        'detail' => 'Tiga aktivitas inti Pimpinan di sistem ini adalah: (1) Memantau kesehatan stok & nilai persediaan di Dashboard, (2) Mengaudit serapan 7 unit kerja di Buku Kartu Kendali, dan (3) Mengesahkan Stock Opname (BASO) di akhir periode.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Pimpinan tetap memiliki akses "Layanan ATK Pegawai" apabila sewaktu-waktu perlu mengajukan kebutuhan ATK langsung untuk ruang kerja pimpinan.'],
                ],
            ],
            [
                'id' => 'pimpinan-dashboard',
                'number' => '02',
                'category' => 'Ringkasan Makro',
                'title' => 'Membaca Dashboard Eksekutif Terpadu',
                'summary' => 'Satu layar untuk membaca kondisi gudang persediaan BPS Kabupaten Cirebon secara cepat: nilai aset rupiah, perputaran barang, dan dokumen yang menunggu tanda tangan.',
                'when_to_use' => 'Saat ingin meninjau kondisi logistik kantor secara cepat dalam 1–2 menit.',
                'menu_path' => 'Utama → Dashboard',
                'steps' => [
                    [
                        'title' => 'Perhatikan banner pengingat BASO di kanan atas hero',
                        'detail' => 'Jika Admin sudah selesai menghitung fisik gudang dan memfinalisasi opname, tombol kuning berkedip "X BASO Perlu Disahkan" akan langsung muncul supaya Anda tidak terlewat.',
                    ],
                    [
                        'title' => 'Evaluasi kartu metrik utama & nilai persediaan',
                        'detail' => 'Cermati jumlah katalog aktif di 8 kategori persediaan, jumlah permintaan ATK yang sudah tuntas, stok menipis yang perlu perhatian pengadaan, serta total nilai aset persediaan dalam Rupiah.',
                    ],
                    [
                        'title' => 'Bandingkan grafik tren & distribusi per tim kerja',
                        'detail' => 'Amati grafik perbandingan barang masuk (pengadaan) vs keluar (pemakaian) per bulan serta grafik distribusi penyerapan pada seluruh 7 unit fungsi kantor.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Jika ada stok barang strategis (seperti kertas atau toner) terus menipis sementara usulan pegawai banyak masuk, hasil pantauan Dashboard menjadi dasar kuat untuk menyetujui belanja pengadaan berikutnya.'],
                ],
            ],
            [
                'id' => 'pimpinan-usulan-permintaan',
                'number' => '03',
                'category' => 'Aspirasi Unit',
                'title' => 'Memantau Usulan Barang Pegawai & Pengajuan Pribadi',
                'summary' => 'Lihat barang apa saja yang sedang diusulkan oleh tim-tim fungsi di luar katalog standar, sekaligus ajukan kebutuhan ATK pimpinan jika diperlukan.',
                'when_to_use' => 'Saat meninjau rencana pengadaan baru dari aspirasi pegawai atau mengecek status pengajuan ATK pimpinan.',
                'menu_path' => 'Manajemen Persediaan → Usulan Barang Pegawai',
                'steps' => [
                    [
                        'title' => 'Buka daftar Usulan Barang Pegawai',
                        'detail' => 'Di sini tampil daftar usulan pengadaan barang dari seluruh ruangan beserta nama pengusul, alasan kegiatannya, dan apakah sudah ditindaklanjuti oleh Admin Subbag Umum.',
                    ],
                    [
                        'title' => 'Gunakan layanan permintaan pribadi bila perlu',
                        'detail' => 'Melalui kategori menu "Layanan ATK Pegawai", pimpinan dapat mengajukan ATK baru dan memantau Buku Kendali ruang pimpinan sama mudahnya dengan pegawai lain.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Gunakan daftar Usulan Barang Pegawai sebagai bahan diskusi saat rapat pimpinan bersama Kasubbag Umum sebelum belanja triwulanan/semesteran.'],
                ],
            ],
            [
                'id' => 'pimpinan-kartu-kendali',
                'number' => '04',
                'category' => 'Audit & Laporan',
                'title' => 'Audit 7 Unit Kerja & Buku Kartu Kendali Barang',
                'summary' => 'Bandingkan pemakaian ATK antar seluruh 7 unit fungsi dan telusuri rincian saldo keluar-masuk per barang hingga nilai Rupiahnya secara transparan.',
                'when_to_use' => 'Untuk audit internal, mengecek kewajaran pemakaian antar fungsi, atau menyiapkan lampiran pemeriksaan.',
                'menu_path' => 'Manajemen Persediaan → Buku Kartu Kendali',
                'steps' => [
                    [
                        'title' => 'Tinjau Rekap Pemakaian 7 Unit Kerja',
                        'detail' => 'Pada halaman Buku Kartu Kendali, lihat tabel rekap seluruh unit kerja (Subbag Umum hingga 6 fungsi teknis/seksi). Klik detail ruangan mana saja untuk melihat daftar pegawai pengambil dan jenis barangnya.',
                    ],
                    [
                        'title' => 'Periksa Kartu Stok Per Barang (Masuk–Keluar–Sisa)',
                        'detail' => 'Pindah ke tab daftar barang untuk meneliti mutasi suatu item tertentu, lengkap dengan saldo awal, tanggal keluar/masuk, harga satuan, serta nilai nominal Rupiah tersisa.',
                    ],
                    [
                        'title' => 'Cetak atau unduh laporan (PDF / Excel / Word)',
                        'detail' => 'Tersedia tombol cetak PDF format dinas, export Excel, maupun export Word baik untuk 1 ruangan, 1 barang, maupun Rekap Semua Ruangan sekaligus.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Format cetak rekap barang sudah menyesuaikan struktur akuntabilitas persediaan BMN sehingga nyaman dibaca saat evaluasi SAKTI.'],
                ],
            ],
            [
                'id' => 'pimpinan-sahkan-baso',
                'number' => '05',
                'category' => 'Pengesahan Resmi',
                'title' => 'Pengesahan Stock Opname (BASO) & Cetak Neraca SAKTI',
                'summary' => 'Tahap puncak kontrol persediaan: memeriksa hasil audit fisik gudang yang disusun Admin dan memberikan Pengesahan Pimpinan secara digital.',
                'when_to_use' => 'Setiap akhir bulan/triwulan/semester ketika Admin telah menyelesaikan Stock Opname dan berstatus Final.',
                'menu_path' => 'Manajemen Persediaan → Stock Opname (BASO)',
                'steps' => [
                    [
                        'title' => 'Pilih sesi opname berstatus "Final (Menunggu Pengesahan)"',
                        'detail' => 'Buka menu Stock Opname (BASO). Anda akan melihat daftar periode opname beserta statusnya. Klik Detail pada sesi yang sudah difinalisasi Admin.',
                    ],
                    [
                        'title' => 'Teliti tabel saldo sistem vs hasil hitung fisik rak',
                        'detail' => 'Periksa jumlah barang menurut catatan aplikasi, jumlah fisik nyata hasil hitungan tim di gudang, dan kolom Selisih (baik kurang maupun lebih) beserta catatan keterangannya.',
                    ],
                    [
                        'title' => 'Klik tombol "Sahkan BASO (Pimpinan)"',
                        'detail' => 'Jika data hasil opname sudah diyakini valid, klik tombol Pengesahan Pimpinan dan konfirmasi. Status dokumen langsung terkunci sah menjadi "Disahkan".',
                    ],
                    [
                        'title' => 'Cetak Berita Acara (Neraca SAKTI atau Fisik Internal)',
                        'detail' => 'Klik Cetak BASO dan pilih format yang dibutuhkan: "Format Neraca SAKTI" (menampilkan nilai persediaan untuk rekonsiliasi keuangan/BMN) atau "Format Fisik Internal" (pemeriksaan barang pakai habis gudang). Tersedia juga opsi filter stok dan Export Excel.',
                    ],
                ],
                'statuses' => [
                    ['label' => 'Draft', 'class' => 'bg-amber-50 text-amber-800 border-amber-200', 'meaning' => 'Admin masih dalam proses menghitung barang fisik di rak gudang; belum siap disahkan.'],
                    ['label' => 'Final', 'class' => 'bg-sky-50 text-sky-700 border-sky-200', 'meaning' => 'Admin sudah menyelesaikan pencatatan fisik & mengunci selisih. Menunggu Pengesahan Pimpinan.'],
                    ['label' => 'Disahkan', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'meaning' => 'Sudah disetujui resmi oleh Kepala BPS Kabupaten Cirebon dan siap dicetak sebagai dokumen BASO.'],
                ],
                'tips' => [
                    ['type' => 'warning', 'text' => 'Pastikan kolom selisih sudah ditanyakan/dikonfirmasi ke Admin gudang sebelum menekan tombol Sahkan BASO, karena dokumen yang sudah disahkan bersifat resmi sebagai arsip tutup buku periode.'],
                ],
            ],
        ];

        $faqs = [
            [
                'q' => 'Apakah Pimpinan perlu menyetujui satu per satu permintaan pulpen atau kertas dari pegawai?',
                'a' => 'Tidak perlu. Persetujuan kuota harian dan serah terima barang dijalankan oleh Admin / pengurus barang Subbag Umum agar proses cepat. Pimpinan cukup mengawasi rekapnya di Dashboard dan Buku Kartu Kendali, serta mengesahkan BASO periodik.',
            ],
            [
                'q' => 'Apa bedanya cetak BASO Format Neraca SAKTI dengan Format Fisik Internal?',
                'a' => 'Format Neraca SAKTI menyajikan rincian nilai rupiah saldo persediaan untuk keperluan pencocokan dengan laporan SAKTI/keuangan, sedangkan Format Fisik Internal berfokus pada kuantitas fisik barang di gudang dengan opsi menyaring hanya barang bersaldo atau semua barang.',
            ],
            [
                'q' => 'Kenapa tombol "Sahkan BASO" belum muncul pada sebuah sesi opname?',
                'a' => 'Kemungkinan Admin masih berada pada tahap penghitungan (status masih "Draft") dan belum menekan tombol "Finalisasi". Begitu Admin melakukan finalisasi, tombol pengesahan untuk Pimpinan akan otomatis aktif.',
            ],
            [
                'q' => 'Apakah seluruh laporan Buku Kendali bisa dibawa untuk bahan rapat tanpa buka laptop?',
                'a' => 'Ya, setiap laporan di Buku Kartu Kendali dan BASO dilengkapi tombol Cetak PDF (siap print A4/F4 resmi ber-kop BPS Kabupaten Cirebon) serta Export Excel dan Word.',
            ],
        ];

        return [
            'key' => 'pimpinan',
            'tab_label' => 'Panduan Pimpinan',
            'role_badge' => 'Tab 2 • Kepala BPS Kabupaten Cirebon',
            'hero_title' => 'Panduan Pengawasan Eksekutif & Pengesahan',
            'hero_desc' => 'Panduan ringkas namun menyeluruh bagi Pimpinan dalam memantau nilai persediaan, mengaudit distribusi 7 unit kerja, hingga mengesahkan Berita Acara Stock Opname (BASO) format SAKTI.',
            'read_time' => '5 Bab Utama • Kontrol & Audit',
            'accent' => 'emerald',
            'roadmap' => [
                ['step' => '1', 'title' => 'Dashboard Makro', 'desc' => 'Cek nilai aset & tren bulanan'],
                ['step' => '2', 'title' => 'Pantau Usulan', 'desc' => 'Tinjau usulan pengadaan'],
                ['step' => '3', 'title' => 'Audit 7 Ruangan', 'desc' => 'Bandingkan Buku Kendali'],
                ['step' => '4', 'title' => 'Periksa Opname', 'desc' => 'Teliti selisih fisik vs sistem'],
                ['step' => '5', 'title' => 'Sahkan & Cetak BASO', 'desc' => 'Format Neraca SAKTI & Fisik'],
            ],
            'chapters' => $this->enrichSearchText($chapters),
            'faqs' => $faqs,
        ];
    }

    /**
     * Menyusun panduan komprehensif untuk User Admin (Tab 3).
     */
    private function buildAdminGuide(): array
    {
        $chapters = [
            [
                'id' => 'admin-peran',
                'number' => '01',
                'category' => 'Peran Operasional',
                'title' => 'Tugas Inti Administrator Gudang (Subbag Umum)',
                'summary' => 'Admin memegang kendali penuh atas akurasi stok gudang: mencatat barang masuk dari vendor, memverifikasi & menyerahkan barang ke pegawai, opname fisik, kelola user, hingga mengatur Asisten AI.',
                'when_to_use' => 'Menjadi panduan kerja harian pengurus barang / admin Subbag Umum BPS Kabupaten Cirebon.',
                'menu_path' => 'Sidebar → Manajemen Persediaan & Administrasi',
                'steps' => [
                    [
                        'title' => 'Jaga prinsip "Fisik dan Aplikasi Selalu Sama"',
                        'detail' => 'Setiap barang baru datang wajib dicatat di Penerimaan Barang, dan setiap barang diambil pegawai wajib diproses Serah Terima di aplikasi agar angka saldo selalu cocok dengan isi lemari gudang.',
                    ],
                    [
                        'title' => 'Gunakan pintasan aksi cepat di Dashboard Admin',
                        'detail' => 'Dashboard Admin menyediakan 3 tombol pintasan utama di banner atas: Catat Barang Masuk, Periksa Permintaan, dan Cek Fisik Stok (Opname), ditambah daftar antrean permintaan baru dan stok kritis.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Biasakan mengecek kartu "Stok Hampir Habis" di Dashboard Admin setiap awal minggu untuk bahan pengajuan pengadaan sebelum stok benar-benar menyentuh angka 0.'],
                ],
            ],
            [
                'id' => 'admin-verifikasi-serah-terima',
                'number' => '02',
                'category' => 'Pelayanan Pegawai',
                'title' => 'Verifikasi & Serah Terima Permintaan ATK (2 Langkah)',
                'summary' => 'Proses permintaan pegawai terdiri dari dua tahap terpisah agar stok tidak berkurang sebelum barang fisik benar-benar diambil oleh yang bersangkutan.',
                'when_to_use' => 'Setiap kali ada pengajuan ATK baru masuk dari pegawai.',
                'menu_path' => 'Manajemen Persediaan → Verifikasi Permintaan',
                'steps' => [
                    [
                        'title' => 'Tahap 1 — Verifikasi Kuota Permohonan',
                        'detail' => 'Buka tiket berstatus "Menunggu". Periksa keperluan kegiatan dan jumlah barang yang diminta. Jika persediaan terbatas, ubah angka di kolom "Jumlah Disetujui" secukupnya, tambahkan catatan bila perlu, lalu klik Setujui (atau Tolak beserta alasan jelas jika tidak bisa dipenuhi).',
                    ],
                    [
                        'title' => 'Siapkan barang fisik di ruang Subbag Umum',
                        'detail' => 'Setelah status menjadi "Disetujui", pegawai akan tahu barang siap diambil (stok gudang belum terpotong di tahap ini, sehingga tetap aman jika pegawai membatalkan atau belum mengambil).',
                    ],
                    [
                        'title' => 'Tahap 2 — Eksekusi Serah Terima Saat Pengambilan',
                        'detail' => 'Ketika pegawai datang mengambil barang di meja Subbag Umum, buka detail tiket tersebut dan klik tombol hijau "Serah Terima". Seketika itu stok gudang otomatis terpotong (dilindungi mekanisme anti-minus) dan langsung masuk ke Buku Kendali ruangan pemohon.',
                    ],
                ],
                'statuses' => [
                    ['label' => 'Tahap 1: Setujui Kuota', 'class' => 'bg-sky-50 text-sky-700 border-sky-200', 'meaning' => 'Menetapkan jumlah yang boleh diambil; stok fisik gudang masih utuh sampai pegawai datang.'],
                    ['label' => 'Tahap 2: Serah Terima', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'meaning' => 'Barang diserahkan ke tangan pegawai; stok langsung terpotong atomik dan tercatat di Buku Kendali.'],
                ],
                'tips' => [
                    ['type' => 'warning', 'text' => 'Jangan pernah menyerahkan fisik barang tanpa mengklik tombol "Serah Terima" di sistem. Jika lupa klik, stok di aplikasi akan lebih besar daripada fisik rak.'],
                ],
            ],
            [
                'id' => 'admin-barang-masuk',
                'number' => '03',
                'category' => 'Pengadaan & SPJ',
                'title' => 'Penerimaan Barang Masuk (Belanja Vendor) & Update SPJ',
                'summary' => 'Mencatat setiap pengadaan barang baru ke gudang lengkap dengan nilai belanja riil dan status kelengkapan dokumen SPJ.',
                'when_to_use' => 'Setiap kali barang belanjaan dari rekanan/vendor datang diterima di kantor BPS Kabupaten Cirebon.',
                'menu_path' => 'Manajemen Persediaan → Penerimaan Barang',
                'steps' => [
                    [
                        'title' => 'Catat barang masuk baru',
                        'detail' => 'Klik "+ Catat Barang Masuk". Pilih barang dari katalog, isi tanggal transaksi, jumlah masuk, harga satuan beli riil, nama vendor/toko, serta nomor dokumen/faktur pembelian.',
                    ],
                    [
                        'title' => 'Tentukan dan perbarui status SPJ',
                        'detail' => 'Jika barang sudah datang tetapi berkas kuitansi/SPJ masih diproses, pilih status SPJ "Tertunda". Nanti bila berkas SPJ telah lengkap ditandatangani, cukup klik tombol ubah status SPJ di tabel menjadi "Selesai" tanpa perlu input ulang.',
                    ],
                    [
                        'title' => 'Filter periode & ekspor laporan pengadaan',
                        'detail' => 'Gunakan filter bulan dan tahun untuk melihat penerimaan pada periode tertentu, lalu klik "Cetak PDF" atau "Export Excel" untuk kebutuhan pelaporan Subbag Umum.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Mengisi harga satuan riil dengan teliti akan membuat perhitungan nilai aset Rupiah di Kartu Kendali dan BASO Neraca SAKTI sangat akurat.'],
                ],
            ],
            [
                'id' => 'admin-katalog-kategori',
                'number' => '04',
                'category' => 'Master Data',
                'title' => 'Mengelola Katalog Persediaan, Kategori & Foto Barang',
                'summary' => 'Menjaga master data barang persediaan tetap rapi, berkode BMN tepat, dan memiliki foto agar pegawai mudah membedakan barang saat mengajukan.',
                'when_to_use' => 'Saat ada item persediaan jenis baru, perlu mengubah lokasi rak/stok minimum, atau ingin memperbarui foto barang.',
                'menu_path' => 'Utama → Katalog Persediaan & Manajemen Persediaan → Kategori Barang',
                'steps' => [
                    [
                        'title' => 'Kelola Kategori Barang terlebih dahulu',
                        'detail' => 'Di menu Kategori Barang, Anda dapat menambah atau memperbarui kelompok barang (total standar 8 kategori BPS seperti Alat Tulis Kantor, Kertas, Tinta Printer, Perlengkapan Sensus, dll.).',
                    ],
                    [
                        'title' => 'Tambah atau ubah data barang persediaan',
                        'detail' => 'Di menu Katalog Persediaan, klik Tambah/Edit Barang. Lengkapi kode barang standar BPS/BMN, nama barang yang jelas (sertakan ukuran/warna jika penting), satuan, batas stok minimum pengingat, dan lokasi lemari/rak.',
                    ],
                    [
                        'title' => 'Unggah foto produk (otomatis dioptimasi WebP)',
                        'detail' => 'Pilih foto barang dari galeri/kamera Anda (JPG/PNG/WebP). Sistem otomatis mengecilkan ukuran gambar dengan cerdas menjadi format WebP ringan (<100KB) dalam subfolder kategori sehingga halaman Ajukan ATK tetap super cepat dimuat.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Menambahkan foto barang asli rak kantor sangat membantu pegawai mengurangi salah pesan antara kertas A4 vs F4 atau tinta warna vs hitam.'],
                    ['type' => 'warning', 'text' => 'Stok awal barang baru sebaiknya dibiarkan 0 lalu diisi melalui Penerimaan Barang Masuk agar seluruh riwayat nilai belanjanya terekam rapi di Buku Kartu Kendali.'],
                ],
            ],
            [
                'id' => 'admin-usulan',
                'number' => '05',
                'category' => 'Tindak Lanjut',
                'title' => 'Menindaklanjuti Usulan Barang Baru Pegawai',
                'summary' => 'Menerima atau memberi tanggapan atas usulan pengadaan barang baru yang dikirim oleh rekan-rekan tim fungsi.',
                'when_to_use' => 'Saat meninjau usulan baru pegawai atau memperbarui status usulan yang barangnya sudah berhasil dibeli.',
                'menu_path' => 'Manajemen Persediaan → Usulan Barang Pegawai',
                'steps' => [
                    [
                        'title' => 'Periksa daftar usulan masuk',
                        'detail' => 'Lihat nama barang yang diusulkan, siapa pengusul dan ruangannya, jumlah perkiraan kebutuhan, serta alasan keperluannya.',
                    ],
                    [
                        'title' => 'Perbarui status & berikan catatan Admin',
                        'detail' => 'Ubah status menjadi Disetujui (masuk rencana beli), Selesai/Terealisasi (barang sudah masuk katalog siap diminta), atau Ditolak (disertai catatan alasan yang informatif bagi pegawai).',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Jika barang usulan sudah dibeli dan ditambahkan ke Katalog Persediaan, tandai usulannya selesai dan tuliskan kode/nama barangnya di catatan agar pegawai tahu sudah bisa diajukan.'],
                ],
            ],
            [
                'id' => 'admin-stock-opname',
                'number' => '06',
                'category' => 'Audit Periodik',
                'title' => 'Membuka Sesi, Input Fisik & Finalisasi Stock Opname (BASO)',
                'summary' => 'Prosedur resmi pencocokan stok fisik di rak gudang dengan saldo aplikasi hingga menghasilkan Berita Acara Stock Opname (BASO) siap sah.',
                'when_to_use' => 'Dilaksanakan rutin setiap akhir bulan, akhir triwulan, atau tutup tahun anggaran.',
                'menu_path' => 'Manajemen Persediaan → Stock Opname (BASO)',
                'steps' => [
                    [
                        'title' => '1. Buka sesi opname baru (Snapshot Otomatis)',
                        'detail' => 'Klik "Buka Sesi Opname", masukkan periode (misal: "Triwulan IV / November 2026") dan tanggal pemeriksaan. Sistem langsung mengunci saldo aplikasi seluruh katalog saat itu.',
                    ],
                    [
                        'title' => '2. Hitung fisik barang di lemari dan input angka nyata',
                        'detail' => 'Hitung barang fisik di rak gudang lalu masukkan jumlah aslinya di kolom "Stok Fisik". Sistem otomatis menghitung selisih (+ atau -) terhadap saldo sistem. Simpan progres sementara kapan saja selama masih berstatus Draft.',
                    ],
                    [
                        'title' => '3. Finalisasi untuk penyesuaian saldo otomatis',
                        'detail' => 'Jika seluruh hitungan sudah dicek ulang dan benar, klik "Finalisasi". Sistem otomatis melakukan penyesuaian (koreksi stok) pada barang yang selisih sehingga saldo aplikasi kembali sama dengan kenyataan fisik gudang.',
                    ],
                    [
                        'title' => '4. Cetak / Ekspor & serahkan ke Pimpinan untuk disahkan',
                        'detail' => 'Setelah final, minta Pimpinan (Kepala BPS) membuka sesi ini dari akunnya dan menekan tombol "Sahkan BASO". Dokumen BASO format Neraca SAKTI maupun Fisik Internal dan Excel siap dicetak resmi.',
                    ],
                ],
                'tips' => [
                    ['type' => 'warning', 'text' => 'Sesi yang masih berstatus Draft bisa dihapus bila keliru membuka periode. Jika sesi Final terpaksa dihapus sebelum disahkan Pimpinan, sistem akan otomatis membalikkan koreksi stoknya agar saldo tidak rusak.'],
                ],
            ],
            [
                'id' => 'admin-kartu-kendali',
                'number' => '07',
                'category' => 'Laporan Resmi',
                'title' => 'Buku Kartu Kendali & Ekspor Multi-Format (PDF, Excel, Word)',
                'summary' => 'Pusat arsip dan pelaporan mutasi seluruh barang gudang serta pemakaian ATK di seluruh 7 unit kerja BPS Kabupaten Cirebon.',
                'when_to_use' => 'Saat butuh mencetak kartu stok barang per item, kartu kendali per unit fungsi, atau rekap semua ruangan.',
                'menu_path' => 'Manajemen Persediaan → Buku Kartu Kendali',
                'steps' => [
                    [
                        'title' => 'Pilih tab Rekap Ruangan atau Kartu Stok Barang',
                        'detail' => 'Anda dapat menelusuri riwayat pengeluaran per unit kerja (7 ruangan) atau per item barang persediaan lengkap dengan saldo awal, transaksi masuk, transaksi keluar, dan nilai Rupiahnya.',
                    ],
                    [
                        'title' => 'Cetak atau unduh sesuai format yang diminta',
                        'detail' => 'Klik tombol Cetak PDF untuk mencetak langsung dengan tata letak surat dinas BPS Cirebon, atau klik Export Excel (.xlsx) / Export Word (.docx) jika perlu diolah lebih lanjut.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Gunakan tombol "Cetak Semua Ruangan" atau "Rekap Semua Barang" di bagian atas jika ingin mengunduh laporan gabungan kantor dalam sekali klik.'],
                ],
            ],
            [
                'id' => 'admin-users',
                'number' => '08',
                'category' => 'Administrasi Akun',
                'title' => 'Manajemen Pengguna, Mutasi Ruangan & Reset Password',
                'summary' => 'Mengatur akun seluruh pegawai, mutasi unit fungsi, menonaktifkan akun pindahan tanpa merusak riwayat, hingga reset kata sandi jika ada yang lupa.',
                'when_to_use' => 'Saat ada CPNS/pegawai baru masuk, rotasi unit kerja di kantor, pegawai pensiun/pindah, atau lupa password.',
                'menu_path' => 'Administrasi → Manajemen Pengguna',
                'steps' => [
                    [
                        'title' => 'Tambah atau ubah pengguna & ruangan',
                        'detail' => 'Klik Tambah Pengguna, isi nama lengkap, NIP 18 digit, peran (Pegawai / Pimpinan / Admin), dan pilih unit kerja/ruangan. Bila pegawai berpindah tim fungsi, edit profilnya dan ganti Ruangan.',
                    ],
                    [
                        'title' => 'Reset Password ke NIP dalam 1 klik',
                        'detail' => 'Jika ada pegawai melapor lupa sandi, cari namanya lalu klik tombol "Reset Password". Kata sandi akun tersebut otomatis dikembalikan ke NIP 18 digitnya agar bisa langsung login kembali.',
                    ],
                    [
                        'title' => 'Nonaktifkan akun pindahan (jangan langsung hapus jika ada transaksi)',
                        'detail' => 'Gunakan tombol ubah status Aktif/Nonaktif bila ada pegawai pindah/pensiun agar tidak bisa login lagi sementara riwayat transaksi Buku Kendali tetap utuh tercatat.',
                    ],
                ],
                'tips' => [
                    ['type' => 'warning', 'text' => 'Demi keamanan sistem, Admin tidak dapat menghapus, menonaktifkan, atau menurunkan peran akunnya sendiri saat sedang login.'],
                ],
            ],
            [
                'id' => 'admin-ai',
                'number' => '09',
                'category' => 'Pengaturan AI',
                'title' => 'Manajemen Asisten AI (Ganti API Key Gratis Tanpa Edit Kode)',
                'summary' => 'Pusat kendali Asisten AI SIPABIS: ganti API Key cukup tempel & klik konek, pilih model gratis ($0) tercepat, pasang provider cadangan otomatis, hingga tes chat langsung.',
                'when_to_use' => 'Saat pertama kali memasang API key AI, ketika kuota gratis penyedia AI habis/berhenti merespons, atau ingin mengubah aturan jawab asisten.',
                'menu_path' => 'Administrasi → Manajemen AI',
                'steps' => [
                    [
                        'title' => 'Nyalakan atau matikan Asisten AI secara global',
                        'detail' => 'Di bagian paling atas halaman Manajemen AI terdapat saklar Status Global. Jika dimatikan, ikon chat Asisten AI otomatis hilang dari seluruh halaman pengguna.',
                    ],
                    [
                        'title' => 'Paste API Key & klik Konek (Quick Setup)',
                        'detail' => 'Saat AI mati atau perlu ganti penyedia, tempelkan (paste) API Key baru dari penyedia mana saja (misal Groq, Google Gemini, OpenRouter `:free`, Mistral). Sistem otomatis mengenali jenis provider dari awalan key-nya, lalu memilihkan preset model gratis ($0) yang paling cepat & hemat token. Klik Konek untuk uji koneksi live dan langsung mengaktifkannya.',
                    ],
                    [
                        'title' => 'Cek daftar model gratis live & atur cadangan otomatis',
                        'detail' => 'Klik "Cek Model Gratis Live" untuk menarik model gratis aktif terkini langsung dari server penyedia. Anda juga dapat menyimpan beberapa API key lalu menandai salah satunya sebagai "Cadangan (Fallback)", sehingga jika penyedia utama sedang limit/down, sistem otomatis berpindah ke cadangan.',
                    ],
                    [
                        'title' => 'Atur hemat token, aturan sistem & uji di Sandbox Chat',
                        'detail' => 'Aktifkan mode hemat token (nonaktifkan thinking panjang, batas token output efisien), sesuaikan aturan instruksi AI, lalu uji jawabannya di kotak "Live Sandbox Chat" sebelum dipakai seluruh pegawai.',
                    ],
                ],
                'tips' => [
                    ['type' => 'tip', 'text' => 'Gunakan provider Groq atau OpenRouter versi `:free` bersama Google Gemini sebagai cadangan supaya Asisten AI kantor selalu hidup tanpa biaya berlangganan dan tanpa perlu mengedit kode program.'],
                    ['type' => 'warning', 'text' => 'Seluruh API Key yang disimpan otomatis dienkripsi oleh SIPABIS dan ditampilkan tersamar di layar demi keamanan kredensial kantor.'],
                ],
            ],
        ];

        $faqs = [
            [
                'q' => 'Asisten AI tiba-tiba tidak membalas atau muncul error kuota habis, apa solusinya?',
                'a' => 'Tidak perlu memanggil programmer atau mengubah kode! Buka menu Administrasi → Manajemen AI, cukup tempel API Key baru dari penyedia gratis lain (seperti Groq, Gemini, atau OpenRouter :free) pada panel Quick Setup lalu klik Konek. Atau aktifkan Cadangan Otomatis (Fallback) agar sistem pindah sendiri bila satu provider limit.',
            ],
            [
                'q' => 'Pegawai sudah terlanjur mengambil barang fisik, tapi lupa saya klik "Serah Terima". Apa efeknya?',
                'a' => 'Stok di sistem belum terpotong dan belum tercatat ke Buku Kendali ruangan pegawai tersebut. Segera buka tiket permintaannya di Verifikasi Permintaan dan klik Serah Terima agar stok kembali sinkron.',
            ],
            [
                'q' => 'Apakah saya bisa menghapus user pegawai yang sudah pernah mengajukan ATK sebelumnya?',
                'a' => 'Tidak disarankan dan sistem mencegah penghapusan permanen user yang memiliki riwayat persediaan agar data Buku Kendali dan audit tidak rusak. Cukup ubah status akunnya menjadi Nonaktif pada halaman Manajemen Pengguna.',
            ],
            [
                'q' => 'Saat memasukkan Penerimaan Barang baru, berkas nota/kuitansi SPJ belum lengkap. Apakah pencatatan barang masuk harus ditunda?',
                'a' => 'Jangan ditunda. Tetap catat barang masuk saat barang fisik tiba di kantor agar stok langsung bertambah dan bisa diminta pegawai, lalu pilih status SPJ "Tertunda". Nanti bila berkas SPJ beres, tinggal ubah statusnya menjadi "Selesai".',
            ],
            [
                'q' => 'Bagaimana jika saat Stock Opname ada barang yang jumlah fisiknya berbeda dari aplikasi?',
                'a' => 'Masukkan jumlah fisik yang benar-benar ada di rak gudang apa adanya pada kolom Stok Fisik, dan tulis penyebabnya di catatan (jika diketahui). Saat Anda menekan Finalisasi, sistem akan otomatis mencatat penyesuaian koreksi stok agar saldo sistem akurat kembali.',
            ],
        ];

        return [
            'key' => 'admin',
            'tab_label' => 'Panduan Admin',
            'role_badge' => 'Tab 3 • Pengelola Gudang / Subbag Umum',
            'hero_title' => 'Panduan Lengkap Pengoperasian Administrator',
            'hero_desc' => 'Panduan teknis-praktis harian bagi Admin Subbag Umum: verifikasi & serah terima barang, belanja vendor & SPJ, katalog foto WebP, Stock Opname (BASO), manajemen akun NIP, hingga pengaturan Asisten AI.',
            'read_time' => '9 Bab Komprehensif • Operasional Penuh',
            'accent' => 'amber',
            'roadmap' => [
                ['step' => '1', 'title' => 'Catat Barang Masuk', 'desc' => 'Input belanja & pantau SPJ'],
                ['step' => '2', 'title' => 'Verifikasi Tiket', 'desc' => 'Setujui / sesuaikan kuota'],
                ['step' => '3', 'title' => 'Serah Terima Fisik', 'desc' => 'Potong stok & masuk kendali'],
                ['step' => '4', 'title' => 'Stock Opname (BASO)', 'desc' => 'Hitung rak & finalisasi audit'],
                ['step' => '5', 'title' => 'Kelola User & AI', 'desc' => 'Reset sandi & ganti API key'],
            ],
            'chapters' => $this->enrichSearchText($chapters),
            'faqs' => $faqs,
        ];
    }

    /**
     * Menghasilkan teks pencarian gabungan (lowercase) untuk fitur live search di browser.
     */
    private function enrichSearchText(array $chapters): array
    {
        return array_map(function (array $chapter): array {
            $parts = [
                $chapter['number'] ?? '',
                $chapter['category'] ?? '',
                $chapter['title'] ?? '',
                $chapter['summary'] ?? '',
                $chapter['when_to_use'] ?? '',
                $chapter['menu_path'] ?? '',
            ];

            foreach ($chapter['steps'] ?? [] as $step) {
                $parts[] = $step['title'] ?? '';
                $parts[] = $step['detail'] ?? '';
            }

            foreach ($chapter['statuses'] ?? [] as $status) {
                $parts[] = $status['label'] ?? '';
                $parts[] = $status['meaning'] ?? '';
            }

            foreach ($chapter['tips'] ?? [] as $tip) {
                $parts[] = $tip['text'] ?? '';
            }

            $chapter['search_text'] = mb_strtolower(implode(' ', array_filter($parts)), 'UTF-8');

            return $chapter;
        }, $chapters);
    }
}
