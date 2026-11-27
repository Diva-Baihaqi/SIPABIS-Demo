<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected Ruangan $ruangan;
    protected User $adminUser;
    protected User $pegawaiUser;
    protected User $pimpinanUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'TU',
            'nama_ruangan' => 'Subbagian Umum',
        ]);

        $this->adminUser = User::create([
            'name' => 'Administrator Testing',
            'email' => 'admin.test@bps.go.id',
            'nip' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pegawaiUser = User::create([
            'name' => 'Pegawai Testing',
            'email' => 'user.test@bps.go.id',
            'nip' => 'user',
            'password' => Hash::make('user'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->pimpinanUser = User::create([
            'name' => 'Kepala BPS Testing',
            'email' => 'pimpinan.test@bps.go.id',
            'nip' => 'pimpinan',
            'password' => Hash::make('pimpinan'),
            'role' => 'pimpinan',
            'ruangan_id' => $this->ruangan->id,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIPABIS');
        $response->assertSee('Nomor Induk Pegawai (NIP)');
        $response->assertDontSee('Email Address');
    }

    public function test_admin_testing_account_can_login_with_admin_admin(): void
    {
        $response = $this->post('/login', [
            'nip' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('dashboard'));

        // Akses dashboard mengarahkan ke dashboard admin
        $dashResponse = $this->actingAs($this->adminUser)->get('/dashboard');
        $dashResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_testing_account_can_login_with_user_user(): void
    {
        $response = $this->post('/login', [
            'nip' => 'user',
            'password' => 'user',
        ]);

        $this->assertAuthenticatedAs($this->pegawaiUser);
        $response->assertRedirect(route('dashboard'));

        // Akses dashboard mengarahkan ke dashboard pegawai
        $dashResponse = $this->actingAs($this->pegawaiUser)->get('/dashboard');
        $dashResponse->assertRedirect(route('pegawai.dashboard'));
    }

    public function test_pimpinan_testing_account_can_login_with_pimpinan_pimpinan(): void
    {
        $response = $this->post('/login', [
            'nip' => 'pimpinan',
            'password' => 'pimpinan',
        ]);

        $this->assertAuthenticatedAs($this->pimpinanUser);
        $response->assertRedirect(route('dashboard'));

        // Akses dashboard mengarahkan ke dashboard pimpinan
        $dashResponse = $this->actingAs($this->pimpinanUser)->get('/dashboard');
        $dashResponse->assertRedirect(route('pimpinan.dashboard'));
    }

    public function test_real_bps_employee_can_login_using_18_digit_nip(): void
    {
        $realEmployee = User::create([
            'name' => 'Raka Pratama',
            'email' => 'raka@demo-sipabis.test',
            'nip' => '199205122020011001',
            'password' => Hash::make('199205122020011001'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $response = $this->post('/login', [
            'nip' => '199205122020011001',
            'password' => '199205122020011001',
        ]);

        $this->assertAuthenticatedAs($realEmployee);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'nip' => 'admin',
            'password' => 'salah-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nip');
    }

    public function test_users_cannot_authenticate_with_unregistered_nip(): void
    {
        $response = $this->post('/login', [
            'nip' => '999999999999999999',
            'password' => 'bps12345',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('nip');
    }

    public function test_role_middleware_enforces_access_control(): void
    {
        // 1. Admin mengakses rute admin -> 200
        $this->actingAs($this->adminUser)->get('/admin/dashboard')->assertStatus(200);

        // 2. Pegawai mengakses rute admin -> 403 (Forbidden)
        $this->actingAs($this->pegawaiUser)->get('/admin/dashboard')->assertStatus(403);

        // 3. Pegawai mengakses rute pegawai -> 200
        $this->actingAs($this->pegawaiUser)->get('/pegawai/dashboard')->assertStatus(200);

        // 4. Pimpinan mengakses rute pimpinan -> 200
        $this->actingAs($this->pimpinanUser)->get('/pimpinan/dashboard')->assertStatus(200);

        // 5. Pimpinan mengakses rute admin -> 403
        $this->actingAs($this->pimpinanUser)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('logout.page'));
    }
}
