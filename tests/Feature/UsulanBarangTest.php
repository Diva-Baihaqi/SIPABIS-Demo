<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\UsulanBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsulanBarangTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai1;
    protected User $pegawai2;
    protected Ruangan $ruangan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Rina Kusuma Wardani',
        ]);

        $ruanganLain = Ruangan::create([
            'kode_ruangan' => 'SOSIAL',
            'nama_ruangan' => 'Tim Statistik Sosial',
            'pj_ruangan' => 'PJ Sosial',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Admin Persediaan',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai1 = User::create([
            'nip' => '199001012015011001',
            'name' => 'Pegawai Umum',
            'email' => 'pegawai1@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawai2 = User::create([
            'nip' => '199202022016022002',
            'name' => 'Pegawai Sosial',
            'email' => 'pegawai2@bps.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'ruangan_id' => $ruanganLain->id,
        ]);
    }

    public function test_pegawai_can_view_usulan_list_and_create_page(): void
    {
        $response = $this->actingAs($this->pegawai1)->get(route('usulan-barang.index'));
        $response->assertStatus(200);
        $response->assertSee('Usulan Pengadaan Barang Baru', false);

        $responseCreate = $this->actingAs($this->pegawai1)->get(route('usulan-barang.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Formulir Usulan Pengadaan Barang', false);
    }

    public function test_pegawai_can_submit_usulan_barang(): void
    {
        $payload = [
            'nama_barang_usulan' => 'Flashdisk Kingston 64GB USB 3.0',
            'perkiraan_satuan' => 'buah',
            'estimasi_kebutuhan' => 5,
            'alasan_kebutuhan' => 'Dibutuhkan untuk backup data sensus lapangan BPS 2026',
        ];

        $response = $this->actingAs($this->pegawai1)->post(route('usulan-barang.store'), $payload);

        $response->assertRedirect(route('usulan-barang.index'));
        $this->assertDatabaseHas('usulan_barang', [
            'user_id' => $this->pegawai1->id,
            'nama_barang_usulan' => 'Flashdisk Kingston 64GB USB 3.0',
            'perkiraan_satuan' => 'buah',
            'estimasi_kebutuhan' => 5,
            'status' => 'diajukan',
        ]);
    }

    public function test_admin_can_update_status_usulan(): void
    {
        $usulan = UsulanBarang::create([
            'user_id' => $this->pegawai1->id,
            'ruangan_id' => $this->ruangan->id,
            'nama_barang_usulan' => 'Tinta Printer Epson L3110 003 Black',
            'perkiraan_satuan' => 'botol',
            'estimasi_kebutuhan' => 10,
            'alasan_kebutuhan' => 'Stok habis mendesak untuk cetak kuesioner BPS',
            'status' => 'diajukan',
        ]);

        // Pegawai tidak boleh update status
        $resForbidden = $this->actingAs($this->pegawai1)->patch(route('admin.usulan-barang.update-status', $usulan->id), [
            'status' => 'dipertimbangkan',
            'catatan_tindak_lanjut' => 'Direncanakan masuk anggaran triwulan II',
        ]);
        $resForbidden->assertStatus(403);

        // Admin berhasil update status
        $resAdmin = $this->actingAs($this->admin)->patch(route('admin.usulan-barang.update-status', $usulan->id), [
            'status' => 'dipertimbangkan',
            'catatan_tindak_lanjut' => 'Direncanakan masuk anggaran triwulan II',
        ]);
        $resAdmin->assertRedirect();
        $this->assertDatabaseHas('usulan_barang', [
            'id' => $usulan->id,
            'status' => 'dipertimbangkan',
            'catatan_tindak_lanjut' => 'Direncanakan masuk anggaran triwulan II',
        ]);
    }

    public function test_pegawai_can_cancel_own_pending_usulan(): void
    {
        $usulan = UsulanBarang::create([
            'user_id' => $this->pegawai1->id,
            'ruangan_id' => $this->ruangan->id,
            'nama_barang_usulan' => 'Kabel HDMI 5 Meter',
            'perkiraan_satuan' => 'buah',
            'estimasi_kebutuhan' => 2,
            'alasan_kebutuhan' => 'Untuk ruang rapat',
            'status' => 'diajukan',
        ]);

        $res = $this->actingAs($this->pegawai1)->delete(route('usulan-barang.destroy', $usulan->id));
        $res->assertRedirect(route('usulan-barang.index'));
        $this->assertDatabaseMissing('usulan_barang', ['id' => $usulan->id]);
    }

    public function test_pegawai_cannot_cancel_others_usulan(): void
    {
        $usulan = UsulanBarang::create([
            'user_id' => $this->pegawai1->id,
            'ruangan_id' => $this->ruangan->id,
            'nama_barang_usulan' => 'Stapler Besar Max HD-12N',
            'perkiraan_satuan' => 'buah',
            'estimasi_kebutuhan' => 1,
            'alasan_kebutuhan' => 'Untuk jilid dokumen publikasi BPS',
            'status' => 'diajukan',
        ]);

        $res = $this->actingAs($this->pegawai2)->delete(route('usulan-barang.destroy', $usulan->id));
        $res->assertStatus(403);
        $this->assertDatabaseHas('usulan_barang', ['id' => $usulan->id]);
    }
}
