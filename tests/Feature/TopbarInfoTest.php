<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopbarInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_topbar_displays_realtime_wib_clock_and_default_unit_kerja_card(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 25, 11, 30, 45, 'Asia/Jakarta'));

        $admin = User::factory()->create([
            'role' => 'admin',
            'ruangan_id' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Waktu realtime WIB (tanggal Indonesia + jam + komponen Alpine topbarClock)
        $response->assertSee('topbarClock(', false);
        $response->assertSee('Rabu, 25 November 2026', false);
        $response->assertSee('11:30:45', false);
        $response->assertSee('WIB', false);
        $response->assertSee('tabular-nums', false);

        // Kartu Unit Kerja dengan default Subbag Umum & styling resmi BPS
        $response->assertSee('Unit Kerja', false);
        $response->assertSee('Subbag Umum', false);
        $response->assertSee('bg-bps-navy-600', false);
        $response->assertSee('bg-bps-blue-50', false);

        Carbon::setTestNow();
    }

    public function test_pegawai_topbar_displays_assigned_ruangan_and_realtime_clock(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 25, 8, 5, 9, 'Asia/Jakarta'));

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-SOS',
            'nama_ruangan' => 'Fungsi Statistik Sosial',
            'penanggung_jawab' => 'Koordinator Sosial',
        ]);

        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
        ]);

        $response = $this->actingAs($pegawai)->get(route('pegawai.dashboard'));

        $response->assertStatus(200);

        $response->assertSee('topbarClock(', false);
        $response->assertSee('08:05:09', false);
        $response->assertSee('Fungsi Statistik Sosial', false);

        Carbon::setTestNow();
    }
}
