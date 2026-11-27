<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangMasukExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected BarangPersediaan $barang1;
    protected BarangPersediaan $barang2;
    protected TransaksiPersediaan $transaksi1;
    protected TransaksiPersediaan $transaksi2;
    protected TransaksiPersediaan $transaksi3;

    protected function setUp(): void
    {
        parent::setUp();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'penanggung_jawab' => 'Kasubbag Umum',
        ]);

        $this->admin = User::create([
            'name' => 'Administrator BPS',
            'nip' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Pranata Komputer',
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai BPS',
            'nip' => 'user',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('user'),
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
            'jabatan' => 'Statistisi Pertama',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'KAT-ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
            'deskripsi' => 'Kategori ATK',
        ]);

        $this->barang1 = BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'nama_barang' => 'Kertas HVS A4 80gr',
            'kategori_id' => $kategori->id,
            'satuan' => 'Rim',
            'stok_aktif' => 30,
            'stok_minimum' => 5,
            'harga_satuan_estimasi' => 55000,
        ]);

        $this->barang2 = BarangPersediaan::create([
            'kode_barang' => '000002',
            'kode_sakti' => '1010301001',
            'nama_barang' => 'Spidol Whiteboard Snowman',
            'kategori_id' => $kategori->id,
            'satuan' => 'Pcs',
            'stok_aktif' => 50,
            'stok_minimum' => 10,
            'harga_satuan_estimasi' => 12000,
        ]);

        $this->transaksi1 = TransaksiPersediaan::create([
            'barang_id' => $this->barang1->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 20,
            'saldo_sebelum' => 10,
            'saldo_setelah' => 30,
            'tanggal_transaksi' => '2026-11-20',
            'nomor_dokumen' => 'KW-LUNAS-001',
            'sumber_atau_tujuan' => 'CV Mitra Sejahtera',
            'status_spj' => 'selesai',
            'keterangan' => 'Pengadaan rutin DIPA',
        ]);

        $this->transaksi2 = TransaksiPersediaan::create([
            'barang_id' => $this->barang2->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 15,
            'saldo_sebelum' => 35,
            'saldo_setelah' => 50,
            'tanggal_transaksi' => '2026-11-23',
            'nomor_dokumen' => 'SJ-PENDING-002',
            'sumber_atau_tujuan' => 'Toko ATK Berkah',
            'status_spj' => 'tertunda',
            'keterangan' => 'Langganan koran / ATK mendesak',
        ]);

        $this->transaksi3 = TransaksiPersediaan::create([
            'barang_id' => $this->barang1->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 5,
            'saldo_sebelum' => 5,
            'saldo_setelah' => 10,
            'tanggal_transaksi' => '2026-10-15',
            'nomor_dokumen' => 'KW-OKTOBER-003',
            'sumber_atau_tujuan' => 'Percetakan Media Grafika',
            'status_spj' => 'selesai',
            'keterangan' => 'Pengadaan bulan Oktober',
        ]);
    }

    public function test_admin_can_export_register_barang_masuk_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('Register_Penerimaan_Persediaan_', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition', ''));
    }

    public function test_admin_can_filter_export_register_barang_masuk_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.export', [
            'status_spj' => 'tertunda',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition', ''));
    }

    public function test_admin_can_view_cetak_register_barang_masuk(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.cetak'));

        $response->assertStatus(200);
        $response->assertSee('BUKU REGISTER PENERIMAAN BARANG PERSEDIAAN');
        $response->assertSee('BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $response->assertSee('Satker: 428646');
        $response->assertSee('KW-LUNAS-001');
        $response->assertSee('SJ-PENDING-002');
        $response->assertSee('CV Mitra Sejahtera');
        $response->assertSee('Toko ATK Berkah');
        $response->assertSee('Rina Kusuma Wardani');
        $response->assertSee('Bagus Prasetyo');
        $response->assertSee('TOTAL KESELURUHAN UNIT FISIK MASUK');
    }

    public function test_admin_can_filter_cetak_register_barang_masuk(): void
    {
        $responseSelesai = $this->actingAs($this->admin)->get(route('admin.barang-masuk.cetak', [
            'status_spj' => 'selesai',
        ]));

        $responseSelesai->assertStatus(200);
        $responseSelesai->assertSee('KW-LUNAS-001');
        $responseSelesai->assertDontSee('SJ-PENDING-002');

        $responseTertunda = $this->actingAs($this->admin)->get(route('admin.barang-masuk.cetak', [
            'status_spj' => 'tertunda',
        ]));

        $responseTertunda->assertStatus(200);
        $responseTertunda->assertSee('SJ-PENDING-002');
        $responseTertunda->assertDontSee('KW-LUNAS-001');
    }

    public function test_non_admin_cannot_access_export_or_cetak_barang_masuk(): void
    {
        $responseExport = $this->actingAs($this->pegawai)->get(route('admin.barang-masuk.export'));
        $responseExport->assertStatus(403);

        $responseCetak = $this->actingAs($this->pegawai)->get(route('admin.barang-masuk.cetak'));
        $responseCetak->assertStatus(403);
    }

    public function test_index_contains_detail_modal_and_export_buttons(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.index'));

        $response->assertStatus(200);
        $response->assertSee('Ekspor Excel');
        $response->assertSee('Cetak PDF');
        $response->assertSee('openDetail(');
        $response->assertSee('Rincian Transaksi Penerimaan');
        $response->assertSee('Buku Kendali Barang');
    }

    public function test_admin_can_filter_barang_masuk_by_month_and_year_on_index(): void
    {
        // Request filter bulan 11 (November) tahun 2026
        $responseNov = $this->actingAs($this->admin)->get(route('admin.barang-masuk.index', [
            'bulan' => '11',
            'tahun' => '2026',
        ]));

        $responseNov->assertStatus(200);
        $responseNov->assertSee('KW-LUNAS-001');
        $responseNov->assertSee('SJ-PENDING-002');
        $responseNov->assertDontSee('KW-OKTOBER-003');
        $responseNov->assertSee('name="bulan"', false);
        $responseNov->assertSee('name="tahun"', false);

        // Request filter bulan 10 (Oktober) tahun 2026
        $responseOkt = $this->actingAs($this->admin)->get(route('admin.barang-masuk.index', [
            'bulan' => '10',
            'tahun' => '2026',
        ]));

        $responseOkt->assertStatus(200);
        $responseOkt->assertSee('KW-OKTOBER-003');
        $responseOkt->assertDontSee('KW-LUNAS-001');
        $responseOkt->assertDontSee('SJ-PENDING-002');
    }

    public function test_admin_can_filter_barang_masuk_by_month_and_year_on_cetak_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.cetak', [
            'bulan' => '10',
            'tahun' => '2026',
        ]));

        $response->assertStatus(200);
        $response->assertSee('OKTOBER 2026');
        $response->assertSee('KW-OKTOBER-003');
        $response->assertDontSee('KW-LUNAS-001');
        $response->assertDontSee('SJ-PENDING-002');
    }

    public function test_admin_can_filter_barang_masuk_by_month_and_year_on_export_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.export', [
            'bulan' => '11',
            'tahun' => '2026',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition', ''));
    }
}
