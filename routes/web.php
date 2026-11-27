<?php

use App\Http\Controllers\Admin\AiManagementController;
use App\Http\Controllers\Admin\BarangMasukController;
use App\Http\Controllers\Admin\KategoriBarangController;
use App\Http\Controllers\Admin\PermintaanApprovalController as AdminPermintaanApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AlurPenggunaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuKendaliController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\Pegawai\PermintaanController as PegawaiPermintaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UsulanBarangController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', function () {
    $totalBarang = \App\Models\BarangPersediaan::count();
    $totalKategori = \App\Models\KategoriBarang::count();
    $totalRuangan = \App\Models\Ruangan::count();
    $kategoriList = \App\Models\KategoriBarang::withCount('barang')->get();
    $topKategori = \App\Models\KategoriBarang::withCount('barang')
        ->having('barang_count', '>', 0)
        ->orderBy('barang_count', 'desc')
        ->take(4)
        ->get();
    $barangContoh = \App\Models\BarangPersediaan::select('id', 'kode_barang', 'nama_barang', 'satuan', 'stok_aktif', 'stok_minimum', 'kategori_id', 'lokasi_rak', 'keterangan', 'foto')
        ->with('kategori:id,nama_kategori')
        ->orderByRaw('CASE WHEN foto IS NOT NULL THEN 0 ELSE 1 END')
        ->orderBy('nama_barang', 'asc')
        ->get();

    return view('welcome', compact('totalBarang', 'totalKategori', 'totalRuangan', 'kategoriList', 'topKategori', 'barangContoh'));
});

// Halaman Khusus Buku Panduan Pengguna SIPABIS (Publik, 3 Tab: Pegawai, Pimpinan, Admin)
Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan.index');
Route::get('/panduan/buku/download', [PanduanController::class, 'downloadBuku'])->name('panduan.buku.download');
Route::get('/panduan/buku/pratinjau', [PanduanController::class, 'previewBuku'])->name('panduan.buku.preview');

// AI Chatbot Virtual Assistant (Public & Authenticated)
Route::post('/api/chat', [ChatbotController::class, 'chat'])
    ->middleware('throttle:30,1')
    ->name('api.chat');

// Guest Authentication Routes (Login via NIP only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout Routes (Akses publik/auth untuk halaman konfirmasi & status keluar)
Route::get('/logout', [AuthController::class, 'showLogoutPage'])->name('logout.page');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Common Routes
Route::middleware('auth')->group(function () {

    // Central Dashboard Redirector based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pimpinan' => redirect()->route('pimpinan.dashboard'),
            default => redirect()->route('pegawai.dashboard'),
        };
    })->name('dashboard');

    // Common convenience redirects for sidebar navigation
    Route::get('/permintaan', function () {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.permintaan.index');
        }
        return redirect()->route('pegawai.permintaan.index');
    })->name('permintaan');

    // Cetak Formulir Permintaan Barang Standar BPS (PSD-133)
    Route::get('/permintaan/{id}/cetak', [PegawaiPermintaanController::class, 'cetak'])->name('permintaan.cetak');

    Route::get('/barang-masuk', function () {
        return redirect()->route('admin.barang-masuk.index');
    })->name('barang-masuk');

    // Modul Katalog Persediaan Barang (Bisa diakses seluruh pengguna, kelola hanya Admin)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::middleware('role:admin')->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // Modul Usulan Pengadaan Barang Baru (Permintaan fitur BPS Pak Eko / Kasubbag Umum)
    Route::prefix('usulan-barang')->name('usulan-barang.')->group(function () {
        Route::get('/', [UsulanBarangController::class, 'index'])->name('index');
        Route::get('/create', [UsulanBarangController::class, 'create'])->name('create');
        Route::post('/', [UsulanBarangController::class, 'store'])->name('store');
        Route::delete('/{usulan}', [UsulanBarangController::class, 'destroy'])->name('destroy');
    });
    Route::middleware('role:admin')->prefix('admin/usulan-barang')->name('admin.usulan-barang.')->group(function () {
        Route::match(['post', 'patch'], '/{usulan}/status', [UsulanBarangController::class, 'updateStatus'])->name('update-status');
    });

    // Modul Buku Kendali & Kartu Stok Per Ruangan
    Route::prefix('kartu-kendali')->name('kartu-kendali.')->group(function () {
        Route::get('/', [KartuKendaliController::class, 'index'])->name('index');
        Route::get('/cetak-semua', [KartuKendaliController::class, 'cetakSemuaRuangan'])->name('cetak-semua');
        Route::get('/export-semua/excel', [KartuKendaliController::class, 'exportSemuaRuanganExcel'])->name('export-semua.excel');
        Route::get('/export-semua/word', [KartuKendaliController::class, 'exportSemuaRuanganWord'])->name('export-semua.word');
        Route::get('/ruangan/{id}', [KartuKendaliController::class, 'ruangan'])->name('ruangan');
        Route::get('/ruangan/{id}/cetak', [KartuKendaliController::class, 'cetakRuangan'])->name('ruangan.cetak');
        Route::get('/ruangan/{id}/export', [KartuKendaliController::class, 'exportRuangan'])->name('ruangan.export');
        Route::get('/ruangan/{id}/export-word', [KartuKendaliController::class, 'exportRuanganWord'])->name('ruangan.export-word');
        Route::get('/barang/{id}', [KartuKendaliController::class, 'barang'])->name('barang');
        Route::get('/barang/{id}/cetak', [KartuKendaliController::class, 'cetakBarang'])->name('barang.cetak');
        Route::get('/barang/{id}/export', [KartuKendaliController::class, 'exportBarang'])->name('barang.export');
        Route::get('/barang-rekap/cetak', [KartuKendaliController::class, 'cetakRekapBarang'])->name('barang.cetak-rekap');
        Route::get('/barang-rekap/export/excel', [KartuKendaliController::class, 'exportRekapBarangExcel'])->name('barang.export-rekap.excel');
        Route::get('/barang-rekap/export/word', [KartuKendaliController::class, 'exportRekapBarangWord'])->name('barang.export-rekap.word');
    });

    // Modul Stock Opname & Berita Acara Stock Opname (BASO)
    Route::prefix('stok-opname')->name('stok-opname.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/{id}', [StockOpnameController::class, 'show'])->name('show');
        Route::post('/{id}/update-fisik', [StockOpnameController::class, 'updateFisik'])->name('update-fisik');
        Route::post('/{id}/finalisasi', [StockOpnameController::class, 'finalisasi'])->name('finalisasi');
        Route::post('/{id}/sahkan', [StockOpnameController::class, 'sahkan'])->name('sahkan');
        Route::get('/{id}/cetak', [StockOpnameController::class, 'cetak'])->name('cetak');
        Route::get('/{id}/export', [StockOpnameController::class, 'exportExcel'])->name('export');
        Route::delete('/{id}', [StockOpnameController::class, 'destroy'])->name('destroy');
    });

    // Admin Group
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Barang Masuk (Pengadaan Persediaan)
        Route::get('/barang-masuk/export', [BarangMasukController::class, 'exportExcel'])->name('barang-masuk.export');
        Route::get('/barang-masuk/cetak', [BarangMasukController::class, 'cetak'])->name('barang-masuk.cetak');
        Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::get('/barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
        Route::patch('/barang-masuk/{id}/update-spj', [BarangMasukController::class, 'updateSpj'])->name('barang-masuk.update-spj');
        Route::put('/barang-masuk/{id}', [BarangMasukController::class, 'update'])->name('barang-masuk.update');
        Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');

        // Permintaan ATK (Verifikasi & Serah Terima)
        Route::get('/permintaan', [AdminPermintaanApprovalController::class, 'index'])->name('permintaan.index');
        Route::get('/permintaan/{id}', [AdminPermintaanApprovalController::class, 'show'])->name('permintaan.show');
        Route::post('/permintaan/{id}/verifikasi', [AdminPermintaanApprovalController::class, 'verifikasi'])->name('permintaan.verifikasi');
        Route::post('/permintaan/{id}/tolak', [AdminPermintaanApprovalController::class, 'tolak'])->name('permintaan.tolak');
        Route::post('/permintaan/{id}/serah-terima', [AdminPermintaanApprovalController::class, 'serahTerima'])->name('permintaan.serah-terima');

        // Manajemen Pengguna (Administrasi BPS Cirebon)
        Route::get('/users/export', [UserController::class, 'exportExcel'])->name('users.export');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);

        // Manajemen Kategori Barang Persediaan (BPS Cirebon)
        Route::resource('kategori', KategoriBarangController::class)->except(['show']);

        // Manajemen AI (Setup Multi-Provider, API Key & Efisiensi Token)
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/', [AiManagementController::class, 'index'])->name('index');
            Route::post('/quick-connect', [AiManagementController::class, 'quickConnect'])->name('quick-connect');
            Route::post('/fetch-models', [AiManagementController::class, 'fetchModels'])->name('fetch-models');
            Route::post('/toggle-enabled', [AiManagementController::class, 'toggleEnabled'])->name('toggle-enabled');
            Route::put('/settings', [AiManagementController::class, 'updateSettings'])->name('settings.update');
            Route::post('/sandbox-chat', [AiManagementController::class, 'sandboxChat'])->name('sandbox-chat');
            Route::post('/providers/{provider}/activate', [AiManagementController::class, 'activateProvider'])->name('providers.activate');
            Route::post('/providers/{provider}/toggle-fallback', [AiManagementController::class, 'toggleFallback'])->name('providers.toggle-fallback');
            Route::post('/providers/{provider}/test', [AiManagementController::class, 'testProvider'])->name('providers.test');
            Route::delete('/providers/{provider}', [AiManagementController::class, 'destroyProvider'])->name('providers.destroy');
        });
    });

    // Pegawai Group
    Route::prefix('pegawai')->name('pegawai.')->group(function () {
        Route::middleware('role:pegawai')->get('/dashboard', [DashboardController::class, 'pegawai'])->name('dashboard');

        // Permintaan ATK Pegawai & Pimpinan
        Route::middleware('role:pegawai,pimpinan')->group(function () {
            Route::get('/permintaan', [PegawaiPermintaanController::class, 'index'])->name('permintaan.index');
            Route::get('/permintaan/create', [PegawaiPermintaanController::class, 'create'])->name('permintaan.create');
            Route::post('/permintaan', [PegawaiPermintaanController::class, 'store'])->name('permintaan.store');
            Route::get('/permintaan/{id}', [PegawaiPermintaanController::class, 'show'])->name('permintaan.show');
            Route::delete('/permintaan/{id}', [PegawaiPermintaanController::class, 'destroy'])->name('permintaan.destroy');
        });
    });

    // Pimpinan Group
    Route::prefix('pimpinan')->name('pimpinan.')->middleware('role:pimpinan')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pimpinan'])->name('dashboard');
    });

    // Modul Alur Pengguna (Panduan Alur Sistem per Role)
    Route::get('/alur-pengguna', [AlurPenggunaController::class, 'index'])->name('alur-pengguna.index');

    // Modul Profil Pengguna & Data Kepegawaian BPS
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
    Route::get('/profil/stats', [ProfileController::class, 'stats'])->name('profil.stats');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.update-password');
});
