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

class KartuKendaliExportAllTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pimpinan;
    protected User $pegawaiSosial;
    protected Ruangan $ruanganSosial;
    protected Ruangan $ruanganProduksi;
    protected BarangPersediaan $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruanganSosial = Ruangan::create([
            'kode_ruangan' => 'R-SOSIAL',
            'nama_ruangan' => 'Tim Statistik Sosial',
            'penanggung_jawab' => 'Ketua Tim Sosial',
        ]);

        $this->ruanganProduksi = Ruangan::create([
            'kode_ruangan' => 'R-PRODUKSI',
            'nama_ruangan' => 'Tim Statistik Produksi',
            'penanggung_jawab' => 'Ketua Tim Produksi',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Subbag Umum',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Pengadministrasi BMN',
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala BPS',
            'nip' => 'pimpinan',
            'email' => 'pimpinan@bps.go.id',
            'password' => bcrypt('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Kepala BPS Kabupaten Cirebon',
        ]);

        $this->pegawaiSosial = User::create([
            'name' => 'Pegawai Sosial',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruanganSosial->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $this->barang = BarangPersediaan::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 100,
            'stok_minimum' => 10,
        ]);

        // Catat transaksi mutasi keluar ke ruangan sosial
        TransaksiPersediaan::create([
            'barang_id' => $this->barang->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => -5,
            'saldo_sebelum' => 100,
            'saldo_setelah' => 95,
            'tanggal_transaksi' => Carbon::now()->toDateString(),
            'sumber_atau_tujuan' => $this->ruanganSosial->nama_ruangan,
            'keterangan' => 'Pengambilan ATK untuk survei sosial',
        ]);
    }

    public function test_admin_and_pimpinan_can_view_cetak_semua_ruangan_pdf_layout(): void
    {
        // Admin
        $respAdmin = $this->actingAs($this->admin)->get(route('kartu-kendali.cetak-semua', [
            'tahun' => Carbon::now()->year,
        ]));
        $respAdmin->assertStatus(200);
        $respAdmin->assertSee('BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $respAdmin->assertSee('BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS');
        $respAdmin->assertSee('REKAPITULASI DISTRIBUSI ATK SELURUH UNIT KERJA / DIVISI');
        $respAdmin->assertSee($this->ruanganSosial->nama_ruangan);
        $respAdmin->assertSee($this->ruanganProduksi->nama_ruangan);
        $respAdmin->assertSee('Kertas HVS A4 80gr');

        // Pimpinan
        $respPimpinan = $this->actingAs($this->pimpinan)->get(route('kartu-kendali.cetak-semua'));
        $respPimpinan->assertStatus(200);
        $respPimpinan->assertSee('REKAPITULASI DISTRIBUSI ATK SELURUH UNIT KERJA / DIVISI');
    }

    public function test_admin_can_export_semua_ruangan_to_excel_multi_sheet(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.export-semua.excel', [
            'tahun' => Carbon::now()->year,
        ]));

        $response->assertStatus(200);
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
        $this->assertStringContainsString('Buku_Kendali_BPS_Semua_Divisi_', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_export_semua_ruangan_to_word_document(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.export-semua.word', [
            'tahun' => Carbon::now()->year,
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-word', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Buku_Kendali_BPS_Semua_Divisi_', $response->headers->get('Content-Disposition'));
        $response->assertSee('BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $response->assertSee('REKAPITULASI DISTRIBUSI ATK SELURUH UNIT KERJA / DIVISI');
        $response->assertSee($this->ruanganSosial->nama_ruangan);
    }

    public function test_admin_and_pegawai_can_export_single_ruangan_to_word(): void
    {
        // Admin dapat mengekspor Word untuk ruangan sosial
        $respAdmin = $this->actingAs($this->admin)->get(route('kartu-kendali.ruangan.export-word', [
            'id' => $this->ruanganSosial->id,
        ]));
        $respAdmin->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-word', $respAdmin->headers->get('Content-Type'));
        $this->assertStringContainsString('Buku_Kendali_Tim_Statistik_Sosial', $respAdmin->headers->get('Content-Disposition'));

        // Pegawai dapat mengekspor Word untuk ruangannya sendiri
        $respPegawai = $this->actingAs($this->pegawaiSosial)->get(route('kartu-kendali.ruangan.export-word', [
            'id' => $this->ruanganSosial->id,
        ]));
        $respPegawai->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-word', $respPegawai->headers->get('Content-Type'));
    }

    public function test_pegawai_cannot_access_export_semua_ruangan(): void
    {
        // Pegawai dilarang mengakses cetak semua divisi
        $respCetak = $this->actingAs($this->pegawaiSosial)->get(route('kartu-kendali.cetak-semua'));
        $respCetak->assertStatus(403);

        // Pegawai dilarang mengekspor Excel semua divisi
        $respExcel = $this->actingAs($this->pegawaiSosial)->get(route('kartu-kendali.export-semua.excel'));
        $respExcel->assertStatus(403);

        // Pegawai dilarang mengekspor Word semua divisi
        $respWord = $this->actingAs($this->pegawaiSosial)->get(route('kartu-kendali.export-semua.word'));
        $respWord->assertStatus(403);

        // Pegawai dilarang mengekspor Word ruangan lain
        $respOtherWord = $this->actingAs($this->pegawaiSosial)->get(route('kartu-kendali.ruangan.export-word', [
            'id' => $this->ruanganProduksi->id,
        ]));
        $respOtherWord->assertStatus(403);
    }

    public function test_index_page_displays_collective_export_toolbar_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kartu-kendali.index'));
        $response->assertStatus(200);
        $response->assertSee('Cetak & Ekspor Buku Kendali Kolektif', false);
        $response->assertSee('Semua 7 Divisi');
        $response->assertSee(route('kartu-kendali.cetak-semua'));
        $response->assertSee(route('kartu-kendali.export-semua.excel'));
        $response->assertSee(route('kartu-kendali.export-semua.word'));
    }
}
