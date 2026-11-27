<?php

namespace Tests\Feature;

use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected User $pimpinan;
    protected Ruangan $ruangan1;
    protected Ruangan $ruangan2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan1 = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'pj_ruangan' => 'Kasubbag Umum',
        ]);

        $this->ruangan2 = Ruangan::create([
            'kode_ruangan' => 'SOSIAL',
            'nama_ruangan' => 'Statistik Sosial',
            'pj_ruangan' => 'PJ Sosial',
        ]);

        $this->admin = User::create([
            'nip' => 'admin',
            'name' => 'Admin Persediaan',
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan1->id,
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->pegawai = User::create([
            'nip' => '199001012015011001',
            'name' => 'Budi Santoso',
            'email' => 'budi@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan1->id,
            'jabatan' => 'Statistisi Pertama',
            'golongan' => 'III/a',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->pimpinan = User::create([
            'nip' => '197501011998031001',
            'name' => 'Kepala BPS',
            'email' => 'kepala@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan1->id,
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_user_management_index_and_see_stats(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Admin Persediaan');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Kepala BPS');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $responsePimpinan = $this->actingAs($this->pimpinan)->get(route('admin.users.index'));
        $responsePimpinan->assertStatus(403);

        auth()->logout();
        $responseGuest = $this->get(route('admin.users.index'));
        $responseGuest->assertRedirect(route('login'));
    }

    public function test_admin_can_filter_and_search_users(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'q' => 'Budi',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Kepala BPS');
    }

    public function test_admin_can_create_new_user_successfully(): void
    {
        $userData = [
            'nip' => '199505052020122002',
            'name' => 'Siti Rahma, S.Tr.Stat.',
            'email' => 'siti.rahma@bps.go.id',
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan2->id,
            'jabatan' => 'Statistisi Ahli Pertama',
            'golongan' => 'III/a',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'P',
            'no_hp' => '081298765432',
            'password' => '', // Kosongkan agar default NIP
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'nip' => '199505052020122002',
            'name' => 'Siti Rahma, S.Tr.Stat.',
            'email' => 'siti.rahma@bps.go.id',
            'jenis_kelamin' => 'P',
            'is_active' => true,
        ]);

        $newUser = User::where('nip', '199505052020122002')->first();
        $this->assertTrue(Hash::check('199505052020122002', $newUser->password));
    }

    public function test_user_creation_fails_when_nip_duplicate(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'nip' => '199001012015011001', // NIP Budi yang sudah ada
            'name' => 'User Duplikat',
            'email' => 'duplikat@bps.go.id',
            'role' => 'pegawai',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('nip');
    }

    public function test_admin_can_update_user_and_mutate_ruangan(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->pegawai), [
            'nip' => $this->pegawai->nip,
            'name' => 'Budi Santoso, M.Stat.',
            'email' => $this->pegawai->email,
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan2->id, // Mutasi ke Statistik Sosial
            'jabatan' => 'Statistisi Ahli Muda',
            'golongan' => 'III/c',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->pegawai->id,
            'name' => 'Budi Santoso, M.Stat.',
            'ruangan_id' => $this->ruangan2->id,
            'jabatan' => 'Statistisi Ahli Muda',
            'golongan' => 'III/c',
        ]);
    }

    public function test_admin_cannot_demote_self_from_admin(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'nip' => $this->admin->nip,
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => 'pegawai', // Coba ubah diri sendiri jadi pegawai
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $this->assertTrue($this->pegawai->is_active);

        // Deaktivasi
        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->pegawai));
        $response->assertSessionHas('success');
        $this->assertFalse($this->pegawai->fresh()->is_active);

        // Reaktivasi
        $response2 = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->pegawai));
        $response2->assertSessionHas('success');
        $this->assertTrue($this->pegawai->fresh()->is_active);
    }

    public function test_inactive_user_cannot_login(): void
    {
        // Nonaktifkan pegawai
        $this->pegawai->update(['is_active' => false]);

        $response = $this->post(route('login'), [
            'nip' => $this->pegawai->nip,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertGuest();
    }

    public function test_admin_cannot_toggle_own_status(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin));
        $response->assertSessionHasErrors('error');
        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_admin_can_reset_user_password_to_nip(): void
    {
        // Ubah password Budi dulu
        $this->pegawai->update(['password' => Hash::make('newSecret123')]);
        $this->assertFalse(Hash::check($this->pegawai->nip, $this->pegawai->password));

        // Reset password
        $response = $this->actingAs($this->admin)->post(route('admin.users.reset-password', $this->pegawai));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check($this->pegawai->nip, $this->pegawai->fresh()->password));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_cannot_delete_user_with_persediaan_records(): void
    {
        // Buat record permintaan barang untuk Budi
        Permintaan::create([
            'nomor_permintaan' => 'REQ-TEST-001',
            'user_id' => $this->pegawai->id,
            'ruangan_id' => $this->ruangan1->id,
            'status' => 'diajukan',
            'tujuan_penggunaan' => 'Operasional sensus',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->pegawai));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $this->pegawai->id]);
    }

    public function test_admin_can_delete_user_without_records(): void
    {
        $target = User::create([
            'nip' => '199999999999999999',
            'name' => 'Pegawai Baru Tanpa Transaksi',
            'email' => 'tanpa.transaksi@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $target));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_can_export_users_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
