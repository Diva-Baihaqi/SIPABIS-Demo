<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksiPersediaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangMasukCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected BarangPersediaan $barang;
    protected TransaksiPersediaan $transaksi;

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

        $this->barang = BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'nama_barang' => 'Adaptor Lenovo E440',
            'kategori_id' => $kategori->id,
            'satuan' => 'Buah',
            'stok_aktif' => 10,
            'stok_minimum' => 2,
            'harga_satuan_estimasi' => 150000,
        ]);

        $this->transaksi = TransaksiPersediaan::create([
            'barang_id' => $this->barang->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => 10,
            'saldo_sebelum' => 0,
            'saldo_setelah' => 10,
            'tanggal_transaksi' => '2026-11-23',
            'nomor_dokumen' => 'KW123',
            'sumber_atau_tujuan' => 'CV Maulana Komputer',
            'status_spj' => 'selesai',
            'keterangan' => 'Pengadaan adaptor laptop dinas',
        ]);
    }

    public function test_admin_can_update_barang_masuk_and_sync_stock_upwards(): void
    {
        // Kondisi: Admin salah menduga 10 barang, padahal aslinya 12 barang
        $response = $this->actingAs($this->admin)->put(route('admin.barang-masuk.update', $this->transaksi->id), [
            'jumlah' => 12,
            'nomor_dokumen' => 'KW123-REVISI',
            'tanggal_transaksi' => '2026-11-23',
            'sumber_atau_tujuan' => 'CV Maulana Komputer',
            'status_spj' => 'selesai',
            'keterangan' => 'Koreksi penambahan 2 unit riil faktur',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Verifikasi transaksi diperbarui
        $this->assertDatabaseHas('transaksi_persediaan', [
            'id' => $this->transaksi->id,
            'jumlah' => 12,
            'nomor_dokumen' => 'KW123-REVISI',
            'saldo_setelah' => 12,
        ]);

        // Verifikasi stok aktif barang bertambah menjadi 12 (10 + (12 - 10) = 12)
        $this->assertEquals(12, $this->barang->fresh()->stok_aktif);
    }

    public function test_admin_can_update_barang_masuk_and_sync_stock_downwards(): void
    {
        // Kondisi: Admin salah input 10 barang, padahal aslinya hanya 8 barang
        $response = $this->actingAs($this->admin)->put(route('admin.barang-masuk.update', $this->transaksi->id), [
            'jumlah' => 8,
            'nomor_dokumen' => 'KW123',
            'tanggal_transaksi' => '2026-11-23',
            'sumber_atau_tujuan' => 'CV Maulana Komputer',
            'status_spj' => 'selesai',
            'keterangan' => 'Koreksi pengurangan 2 unit',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transaksi_persediaan', [
            'id' => $this->transaksi->id,
            'jumlah' => 8,
            'saldo_setelah' => 8,
        ]);

        // Stok barang berkurang dari 10 menjadi 8 (10 + (8 - 10) = 8)
        $this->assertEquals(8, $this->barang->fresh()->stok_aktif);
    }

    public function test_admin_cannot_update_barang_masuk_with_invalid_quantity(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.barang-masuk.update', $this->transaksi->id), [
            'jumlah' => 0,
            'tanggal_transaksi' => '2026-11-23',
            'status_spj' => 'selesai',
        ]);

        $response->assertSessionHasErrors('jumlah');
        $this->assertEquals(10, $this->barang->fresh()->stok_aktif);
    }

    public function test_admin_cannot_reduce_barang_masuk_if_stock_already_consumed(): void
    {
        // Barang telah didistribusikan ke ruangan sehingga sisa stok aktif hanya 1 unit
        $this->barang->update(['stok_aktif' => 1]);

        // Coba kurangi penerimaan dari 10 menjadi 5 (pengurangan 5 unit padahal stok sisa hanya 1)
        $response = $this->actingAs($this->admin)->put(route('admin.barang-masuk.update', $this->transaksi->id), [
            'jumlah' => 5,
            'tanggal_transaksi' => '2026-11-23',
            'status_spj' => 'selesai',
        ]);

        $response->assertSessionHas('error');
        // Stok tetap 1, transaksi tidak berubah
        $this->assertEquals(1, $this->barang->fresh()->stok_aktif);
        $this->assertEquals(10, $this->transaksi->fresh()->jumlah);
    }

    public function test_admin_can_delete_barang_masuk_and_revert_stock(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.barang-masuk.destroy', $this->transaksi->id));

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Transaksi terhapus
        $this->assertDatabaseMissing('transaksi_persediaan', [
            'id' => $this->transaksi->id,
        ]);

        // Stok master dikembalikan ke 0 (10 - 10 = 0)
        $this->assertEquals(0, $this->barang->fresh()->stok_aktif);
    }

    public function test_admin_cannot_delete_barang_masuk_if_stock_already_consumed(): void
    {
        // Barang telah didistribusikan sehingga stok fisik tersisa 4 (kurang dari kuantitas transaksi 10)
        $this->barang->update(['stok_aktif' => 4]);

        $response = $this->actingAs($this->admin)->delete(route('admin.barang-masuk.destroy', $this->transaksi->id));

        $response->assertSessionHas('error');

        // Transaksi tidak dihapus
        $this->assertDatabaseHas('transaksi_persediaan', [
            'id' => $this->transaksi->id,
        ]);
        $this->assertEquals(4, $this->barang->fresh()->stok_aktif);
    }

    public function test_non_admin_cannot_update_or_delete_barang_masuk(): void
    {
        $responsePut = $this->actingAs($this->pegawai)->put(route('admin.barang-masuk.update', $this->transaksi->id), [
            'jumlah' => 15,
            'tanggal_transaksi' => '2026-11-23',
            'status_spj' => 'selesai',
        ]);
        $responsePut->assertStatus(403);

        $responseDelete = $this->actingAs($this->pegawai)->delete(route('admin.barang-masuk.destroy', $this->transaksi->id));
        $responseDelete->assertStatus(403);
    }

    public function test_index_contains_edit_and_delete_buttons(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.barang-masuk.index'));

        $response->assertStatus(200);
        $response->assertSee('openEdit(');
        $response->assertSee('admin/barang-masuk/' . $this->transaksi->id);
        $response->assertSee('Koreksi Transaksi Penerimaan');
        $response->assertSee('Batalkan pencatatan barang masuk');
    }
}
