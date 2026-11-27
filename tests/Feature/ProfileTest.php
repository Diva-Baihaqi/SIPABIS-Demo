<?php

namespace Tests\Feature;

use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\StokOpname;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected Ruangan $ruangan;
    protected User $pegawaiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruangan = Ruangan::create([
            'kode_ruangan' => 'SOS',
            'nama_ruangan' => 'Fungsi Statistik Sosial',
            'penanggung_jawab' => "Dewi Lestari, S.ST., M.Si.",
            'nip_penanggung_jawab' => '198204102006042001',
        ]);

        $this->pegawaiUser = User::create([
            'name' => 'Raka Pratama',
            'email' => 'raka.pratama@demo-sipabis.test',
            'nip' => '199205122020011001',
            'password' => Hash::make('199205122020011001'),
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
            'jabatan' => 'Pranata Komputer Ahli Pertama',
            'golongan' => 'III/a',
            'status_pegawai' => 'PNS',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->get(route('profil.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->get(route('profil.index'));

        $response->assertStatus(200);
        $response->assertSee('Profil Pegawai BPS');
        $response->assertSee('Raka Pratama');
        $response->assertSee('199205122020011001');
        $response->assertSee('Fungsi Statistik Sosial');
        $response->assertSee("Dewi Lestari, S.ST., M.Si.");
        $response->assertSee('428646');
    }

    public function test_user_can_update_password_successfully(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->put(route('profil.update-password'), [
            'current_password' => '199205122020011001',
            'password' => 'bpsCirebon2026',
            'password_confirmation' => 'bpsCirebon2026',
        ]);

        $response->assertRedirect(route('profil.index'));
        $response->assertSessionHas('success');

        // Verifikasi password baru cocok dan bisa digunakan login
        $this->pegawaiUser->refresh();
        $this->assertTrue(Hash::check('bpsCirebon2026', $this->pegawaiUser->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->put(route('profil.update-password'), [
            'current_password' => 'password-salah',
            'password' => 'bpsCirebon2026',
            'password_confirmation' => 'bpsCirebon2026',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->pegawaiUser->refresh();
        $this->assertTrue(Hash::check('199205122020011001', $this->pegawaiUser->password));
    }

    public function test_user_cannot_update_password_with_mismatched_confirmation(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->put(route('profil.update-password'), [
            'current_password' => '199205122020011001',
            'password' => 'bpsCirebon2026',
            'password_confirmation' => 'bpsBerbeda2026',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_update_profile_details(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->put(route('profil.update'), [
            'name' => 'Raka Pratama, S.Tr.Stat.',
            'nip' => '199205122020011001',
            'email' => 'raka.updated@demo-sipabis.test',
            'jabatan' => 'Pranata Komputer Ahli Muda',
            'golongan' => 'III/b',
            'status_pegawai' => 'PNS',
            'ruangan_id' => $this->ruangan->id,
            'no_hp' => '089912345678',
        ]);

        $response->assertRedirect(route('profil.index'));
        $response->assertSessionHas('success');

        $this->pegawaiUser->refresh();
        $this->assertEquals('Raka Pratama, S.Tr.Stat.', $this->pegawaiUser->name);
        $this->assertEquals('raka.updated@demo-sipabis.test', $this->pegawaiUser->email);
        $this->assertEquals('Pranata Komputer Ahli Muda', $this->pegawaiUser->jabatan);
        $this->assertEquals('III/b', $this->pegawaiUser->golongan);
        $this->assertEquals('089912345678', $this->pegawaiUser->no_hp);
    }

    public function test_user_can_update_profile_via_ajax_realtime(): void
    {
        $response = $this->actingAs($this->pegawaiUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->putJson(route('profil.update'), [
                'name' => 'Raka Pratama, M.Kom.',
                'nip' => '199205122020011001',
                'email' => 'raka.realtime@demo-sipabis.test',
                'jabatan' => 'Koordinator TI Persediaan',
                'golongan' => 'III/c',
                'status_pegawai' => 'PNS',
                'ruangan_id' => $this->ruangan->id,
                'no_hp' => '087700112233',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'name' => 'Raka Pratama, M.Kom.',
                'nip' => '199205122020011001',
                'email' => 'raka.realtime@demo-sipabis.test',
                'jabatan' => 'Koordinator TI Persediaan',
                'golongan' => 'III/c',
                'ruangan_nama' => 'Fungsi Statistik Sosial',
                'ruangan_kode' => 'SOS',
                'no_hp' => '087700112233',
            ],
        ]);

        $this->pegawaiUser->refresh();
        $this->assertEquals('Raka Pratama, M.Kom.', $this->pegawaiUser->name);
        $this->assertEquals('raka.realtime@demo-sipabis.test', $this->pegawaiUser->email);
    }

    public function test_validation_errors_when_updating_profile_with_invalid_data(): void
    {
        // Buat user lain untuk uji keunikan NIP dan Email
        User::create([
            'name' => 'User Lain BPS',
            'email' => 'user.lain@demo-sipabis.test',
            'nip' => '198501012010011001',
            'password' => Hash::make('secret'),
            'role' => 'pegawai',
        ]);

        // Uji NIP duplikat via AJAX
        $response = $this->actingAs($this->pegawaiUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->putJson(route('profil.update'), [
                'name' => 'Raka Pratama',
                'nip' => '198501012010011001', // Sudah milik user lain
                'email' => 'raka.pratama@demo-sipabis.test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nip']);

        // Uji Email duplikat via AJAX
        $response2 = $this->actingAs($this->pegawaiUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->putJson(route('profil.update'), [
                'name' => 'Raka Pratama',
                'nip' => '199205122020011001',
                'email' => 'user.lain@demo-sipabis.test', // Sudah milik user lain
            ]);

        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['email']);
    }

    public function test_male_and_female_users_have_appropriate_avatar_url(): void
    {
        // 1. Pegawai Laki-laki (Raka Pratama, NIP digit 15 = 1)
        $this->assertFalse($this->pegawaiUser->isPerempuan());
        $this->assertStringContainsString('images/man.png', $this->pegawaiUser->foto_url);

        // 2. Pegawai Perempuan (NIP digit 15 = 2)
        $femaleUser = User::create([
            'name' => "Dewi Lestari, S.ST., M.Si.",
            'email' => 'dewi.lestari@demo-sipabis.test',
            'nip' => '198204102006042001', // Digit ke-15 = 2 (Perempuan)
            'password' => Hash::make('secret'),
            'role' => 'pegawai',
            'jenis_kelamin' => 'P',
            'ruangan_id' => $this->ruangan->id,
        ]);

        $this->assertTrue($femaleUser->isPerempuan());
        $this->assertStringContainsString('images/girl.png', $femaleUser->foto_url);

        // Uji render foto di view profil
        $response = $this->actingAs($femaleUser)->get(route('profil.index'));
        $response->assertStatus(200);
        $response->assertSee('images/girl.png');
    }

    public function test_updating_gender_updates_avatar_url_in_realtime(): void
    {
        $response = $this->actingAs($this->pegawaiUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->putJson(route('profil.update'), [
                'name' => 'Raka Pratama',
                'nip' => '199205122020011001',
                'email' => 'raka.pratama@demo-sipabis.test',
                'jenis_kelamin' => 'P', // Ganti ke perempuan
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'jenis_kelamin' => 'P',
                'jenis_kelamin_label' => 'Perempuan',
            ],
        ]);
        $responseJson = $response->json();
        $this->assertStringContainsString('images/girl.png', $responseJson['user']['foto_url']);

        $this->pegawaiUser->refresh();
        $this->assertEquals('P', $this->pegawaiUser->jenis_kelamin);
        $this->assertStringContainsString('images/girl.png', $this->pegawaiUser->foto_url);
    }

    public function test_pegawai_profile_stats_endpoint_returns_personal_realtime_counts(): void
    {
        $otherPegawai = User::factory()->create([
            'role' => 'pegawai',
            'ruangan_id' => $this->ruangan->id,
        ]);

        // 1 tiket aktif + 1 tiket selesai milik pegawaiUser
        $tiketAktif = Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0001',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
        ]);

        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0002',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'selesai',
        ]);

        // 1 tiket milik pegawai lain (tidak boleh masuk hitungan pribadi pegawaiUser)
        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0003',
            'user_id' => $otherPegawai->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
        ]);

        $page = $this->actingAs($this->pegawaiUser)->get(route('profil.index'));
        $page->assertStatus(200);
        $page->assertSee('Total Permintaan Saya', false);
        $page->assertSee(route('profil.stats'), false);

        $json = $this->actingAs($this->pegawaiUser)->getJson(route('profil.stats'));
        $json->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('cards.0.label', 'Total Permintaan Saya')
            ->assertJsonPath('cards.0.value', 2)
            ->assertJsonPath('cards.1.value', 1)
            ->assertJsonPath('cards.2.value', 1);

        // Simulasikan tiket aktif diserahterimakan -> endpoint realtime langsung mencerminkan perubahan
        $tiketAktif->update(['status' => 'selesai']);

        $jsonAfter = $this->actingAs($this->pegawaiUser)->getJson(route('profil.stats'));
        $jsonAfter->assertStatus(200)
            ->assertJsonPath('cards.0.value', 2)
            ->assertJsonPath('cards.1.value', 0)
            ->assertJsonPath('cards.2.value', 2);
    }

    public function test_admin_profile_stats_shows_office_wide_permintaan_counts_instead_of_zeros(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'ruangan_id' => $this->ruangan->id,
        ]);

        // Tiket dibuat oleh pegawai, bukan admin
        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0101',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
        ]);
        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0102',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'disetujui',
        ]);
        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0103',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'selesai',
        ]);

        $page = $this->actingAs($admin)->get(route('profil.index'));
        $page->assertStatus(200);
        $page->assertSee('Total Permintaan Kantor', false);

        $json = $this->actingAs($admin)->getJson(route('profil.stats'));
        $json->assertStatus(200)
            ->assertJsonPath('cards.0.label', 'Total Permintaan Kantor')
            ->assertJsonPath('cards.0.value', 3)
            ->assertJsonPath('cards.1.value', 2)
            ->assertJsonPath('cards.2.value', 1);
    }

    public function test_pimpinan_profile_stats_shows_permintaan_and_baso_endorsement_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'ruangan_id' => $this->ruangan->id]);
        $pimpinan = User::factory()->create(['role' => 'pimpinan', 'ruangan_id' => $this->ruangan->id]);

        Permintaan::create([
            'nomor_permintaan' => 'REQ/BPS/2026/11/0201',
            'user_id' => $this->pegawaiUser->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal_pengajuan' => now(),
            'status' => 'selesai',
        ]);

        StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS/2026/11/0001',
            'tanggal_opname' => now(),
            'periode' => 'November 2026',
            'status' => 'final',
            'user_id' => $admin->id,
        ]);

        StokOpname::create([
            'nomor_opname' => 'BA-SO/BPS/2026/10/0001',
            'tanggal_opname' => now()->subMonth(),
            'periode' => 'Oktober 2026',
            'status' => 'disahkan',
            'user_id' => $admin->id,
            'pimpinan_id' => $pimpinan->id,
        ]);

        $json = $this->actingAs($pimpinan)->getJson(route('profil.stats'));
        $json->assertStatus(200)
            ->assertJsonPath('cards.0.label', 'Total Permintaan Kantor')
            ->assertJsonPath('cards.0.value', 1)
            ->assertJsonPath('cards.1.label', 'BASO Menunggu Pengesahan')
            ->assertJsonPath('cards.1.value', 1)
            ->assertJsonPath('cards.2.label', 'BASO Telah Disahkan')
            ->assertJsonPath('cards.2.value', 1);
    }

    public function test_guest_cannot_access_profile_stats_endpoint(): void
    {
        $this->getJson(route('profil.stats'))->assertStatus(401);
    }
}
