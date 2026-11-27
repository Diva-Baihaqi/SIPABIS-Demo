<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HumanizedKartuStokAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected Ruangan $ruangan;
    protected KategoriBarang $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R-IPDS',
            'nama_ruangan' => 'Tim Integrasi Pengolahan & Diseminasi Statistik',
            'penanggung_jawab' => 'Ketua Tim IPDS',
            'nip_penanggung_jawab' => '198501012010011002',
        ]);

        $this->admin = User::create([
            'name' => 'Administrator Persediaan',
            'nip' => '198001012005011001',
            'email' => 'admin.persediaan@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pengelola BMN Persediaan',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai BPS Pengusul',
            'nip' => '199201012015011003',
            'email' => 'pegawai@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $this->kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Alat tulis kantor habis pakai',
        ]);
    }

    public function test_dashboard_admin_calculates_stok_kritis_correctly_excluding_zero_stock_items(): void
    {
        // 1. Barang normal stok cukup
        BarangPersediaan::create([
            'kode_barang' => 'ATK-01',
            'nama_barang' => 'Kertas A4',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 20,
            'stok_minimum' => 5,
        ]);

        // 2. Barang aktif tapi stok menipis (<= minimum)
        BarangPersediaan::create([
            'kode_barang' => 'ATK-02',
            'nama_barang' => 'Tinta Printer',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Botol',
            'stok_aktif' => 2,
            'stok_minimum' => 5,
        ]);

        // 3. Barang master kosong (stok = 0)
        BarangPersediaan::create([
            'kode_barang' => 'ATK-03',
            'nama_barang' => 'Katalog Kosong',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 0,
            'stok_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // stokMenipis harus 1 (hanya ATK-02 yang aktif tapi menipis, bukan ATK-03 yang kosong)
        $response->assertViewHas('stokMenipis', 1);
        $response->assertSee('Stok Hampir Habis');
        $response->assertSee('Perlu Dibeli Lagi');
        $response->assertSee('Permintaan Baru');
        $response->assertSee('Keluar-Masuk Bulan Ini');
    }

    public function test_kartu_stok_renders_humanized_balance_cards_and_balances_correctly(): void
    {
        $pulpen = BarangPersediaan::create([
            'kode_barang' => 'ATK-PULPEN-09',
            'nama_barang' => 'Pulpen Gel Cair BPS',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Buah',
            'stok_aktif' => 7,
            'stok_minimum' => 2,
            'harga_satuan_estimasi' => 4500,
        ]);

        TransaksiPersediaan::create([
            'barang_id' => $pulpen->id,
            'user_id' => $this->pegawai->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -1,
            'saldo_sebelum' => 8,
            'saldo_setelah' => 7,
            'tanggal_transaksi' => '2026-11-20',
            'nomor_dokumen' => 'REQ/2026/11/0088',
            'sumber_atau_tujuan' => $this->ruangan->nama_ruangan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.barang', $pulpen->id));
        $response->assertStatus(200);

        // Verifikasi saldo awal 8, saldo akhir 7
        $response->assertViewHas('saldoAwal', 8);
        $response->assertViewHas('totalMasuk', 0);
        $response->assertViewHas('totalKeluar', 1);
        $response->assertViewHas('saldoAkhir', 7);

        // Verifikasi microcopy ramah
        $response->assertSee('Stok Awal Lemari');
        $response->assertSee('Barang Baru Datang (+)');
        $response->assertSee('Barang Diambil Pegawai (-)');
        $response->assertSee('Sisa Fisik di Gudang');
        $response->assertSee('Buku Catatan Riwayat Stok Barang');
    }

    public function test_buku_kendali_ruangan_renders_humanized_labels(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('kartu-kendali.ruangan', $this->ruangan->id));
        $response->assertStatus(200);

        $response->assertSee('Riwayat Pemakaian ATK di Ruangan');
        $response->assertSee('Total Barang yang Diambil');
        $response->assertSee('Macam Barang yang Dipakai');
        $response->assertSee('Berapa Kali Mengambil');
    }

    public function test_pegawai_dashboard_renders_humanized_labels(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('pegawai.dashboard'));
        $response->assertStatus(200);

        $response->assertSee('Minta ATK Baru');
        $response->assertSee('Total Permintaan');
        $response->assertSee('Menunggu Persetujuan');
        $response->assertSee('Siap Diambil');
        $response->assertSee('Sudah Diterima');
    }
}

