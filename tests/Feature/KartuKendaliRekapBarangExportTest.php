<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuKendaliRekapBarangExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pimpinan;
    protected User $pegawai;
    protected Ruangan $ruangan;
    protected KategoriBarang $kategori;
    protected BarangPersediaan $barangAktif;
    protected BarangPersediaan $barangKosong;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'R-UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'penanggung_jawab' => 'Kasubbag Umum',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Subbag Umum',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pengadministrasi BMN',
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS',
            'nip' => 'pimpinan',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Kepala BPS Kabupaten Cirebon',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai Subbag Umum',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pengolah Data',
        ]);

        $this->kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $this->barangAktif = BarangPersediaan::create([
            'kode_barang' => 'ATK-AKTIF',
            'nama_barang' => 'Kertas HVS A4 80gr Sinar Dunia',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 50,
            'stok_minimum' => 5,
        ]);

        $this->barangKosong = BarangPersediaan::create([
            'kode_barang' => 'ATK-KOSONG',
            'nama_barang' => 'Bantalan Stempel Kosong Abadi',
            'kategori_id' => $this->kategori->id,
            'satuan' => 'Buah',
            'stok_aktif' => 0,
            'stok_minimum' => 2,
        ]);

        // Transaksi mutasi masuk & keluar untuk barang aktif
        TransaksiPersediaan::create([
            'barang_id' => $this->barangAktif->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 60,
            'saldo_sebelum' => 0,
            'saldo_setelah' => 60,
            'tanggal_transaksi' => Carbon::now()->toDateString(),
            'sumber_atau_tujuan' => 'Pengadaan APBN 2026',
            'keterangan' => 'Pengadaan Triwulan I',
        ]);

        TransaksiPersediaan::create([
            'barang_id' => $this->barangAktif->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -10,
            'saldo_sebelum' => 60,
            'saldo_setelah' => 50,
            'tanggal_transaksi' => Carbon::now()->toDateString(),
            'sumber_atau_tujuan' => $this->ruangan->nama_ruangan,
            'keterangan' => 'Distribusi rutin',
        ]);
    }

    public function test_admin_and_pimpinan_can_view_cetak_rekap_barang_pdf(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.cetak-rekap', [
            'tahun' => Carbon::now()->year,
            'cakupan' => 'aktif',
        ]));

        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('LAPORAN REKAPITULASI POSISI NILAI DAN FISIK BARANG PERSEDIAAN');
        $responseAdmin->assertSee('Kertas HVS A4 80gr Sinar Dunia');
        $responseAdmin->assertDontSee('Bantalan Stempel Kosong Abadi');

        $responsePimpinan = $this->actingAs($this->pimpinan)->get(route('kartu-kendali.barang.cetak-rekap'));
        $responsePimpinan->assertStatus(200);
        $responsePimpinan->assertSee('LAPORAN REKAPITULASI POSISI NILAI DAN FISIK BARANG PERSEDIAAN');
    }

    public function test_pegawai_cannot_access_rekap_barang_exports(): void
    {
        $this->actingAs($this->pegawai)
            ->get(route('kartu-kendali.barang.cetak-rekap'))
            ->assertStatus(403);

        $this->actingAs($this->pegawai)
            ->get(route('kartu-kendali.barang.export-rekap.excel'))
            ->assertStatus(403);

        $this->actingAs($this->pegawai)
            ->get(route('kartu-kendali.barang.export-rekap.word'))
            ->assertStatus(403);
    }

    public function test_admin_can_download_rekap_barang_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.export-rekap.excel', [
            'tahun' => Carbon::now()->year,
            'cakupan' => 'aktif',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString(
            'Rekap_Posisi_Persediaan_ATK_BPS_',
            $response->headers->get('content-disposition')
        );
    }

    public function test_admin_can_download_rekap_barang_word(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.export-rekap.word', [
            'tahun' => Carbon::now()->year,
            'cakupan' => 'aktif',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'application/vnd.ms-word',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString(
            'Rekap_Persediaan_ATK_BPS_',
            $response->headers->get('content-disposition')
        );
        $response->assertSee('LAPORAN REKAPITULASI POSISI NILAI DAN FISIK BARANG PERSEDIAAN');
        $response->assertSee('Kertas HVS A4 80gr Sinar Dunia');
    }

    public function test_cakupan_filter_distinguishes_active_and_all_items(): void
    {
        // 1. Cakupan Aktif (Default): Hanya barang aktif
        $respAktif = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.cetak-rekap', [
            'cakupan' => 'aktif',
        ]));
        $respAktif->assertSee('Kertas HVS A4 80gr Sinar Dunia');
        $respAktif->assertDontSee('Bantalan Stempel Kosong Abadi');

        // 2. Cakupan Semua: Termasuk barang stok nol tanpa mutasi
        $respSemua = $this->actingAs($this->admin)->get(route('kartu-kendali.barang.cetak-rekap', [
            'cakupan' => 'semua',
        ]));
        $respSemua->assertSee('Kertas HVS A4 80gr Sinar Dunia');
        $respSemua->assertSee('Bantalan Stempel Kosong Abadi');
    }

    public function test_admin_sees_rekap_barang_export_banner_on_tab_2(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('kartu-kendali.index', ['tab' => 'barang']));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Cetak & Unduh Rekap Stok Barang', false);
        $responseAdmin->assertDontSee('Ekspor Excel & PDF di Tiap Kartu');
        $responseAdmin->assertSee(route('kartu-kendali.barang.cetak-rekap'), false);
        $responseAdmin->assertSee(route('kartu-kendali.barang.export-rekap.excel'), false);
        $responseAdmin->assertSee(route('kartu-kendali.barang.export-rekap.word'), false);

        $responsePegawai = $this->actingAs($this->pegawai)->get(route('kartu-kendali.index', ['tab' => 'barang']));
        $responsePegawai->assertRedirect(route('kartu-kendali.ruangan', $this->ruangan->id));

        $responseRuanganPegawai = $this->actingAs($this->pegawai)->get(route('kartu-kendali.ruangan', $this->ruangan->id));
        $responseRuanganPegawai->assertStatus(200);
        $responseRuanganPegawai->assertDontSee('Cetak & Unduh Rekap Stok Barang');
    }
}
