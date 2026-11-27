<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class LogoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_visiting_logout_page_sees_animated_logged_out_view(): void
    {
        $response = $this->get(route('logout.page'));

        $response->assertStatus(200);
        $response->assertSee('Sesi Anda Telah Diamankan', false);
        $response->assertSee('logoutCountdown', false);
        $response->assertSee('logout-check-path', false);
        $response->assertSee('Masuk Kembali ke SIPABIS', false);
        $response->assertSee('Ke Beranda Publik', false);
    }

    public function test_authenticated_user_visiting_logout_page_directly_sees_safe_confirmation_without_auto_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Pegawai BPS Uji',
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($user)->get(route('logout.page'));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
        $response->assertSee('Konfirmasi Keluar Sistem', false);
        $response->assertSee('Pegawai BPS Uji', false);
        $response->assertSee('Ya, Keluar Sekarang', false);
        $response->assertSee('Kembali ke Dashboard', false);
    }

    public function test_post_logout_logs_out_and_redirects_to_animated_logout_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('logout.page'));

        $follow = $this->get(route('logout.page'));
        $follow->assertStatus(200);
        $follow->assertSee('Sesi Anda Telah Diamankan', false);
    }

    public function test_post_logout_with_expired_csrf_token_recovers_gracefully_without_419_screen(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $request = Request::create('/logout', 'POST');
        $request->setLaravelSession(app('session.store'));

        $handler = app(ExceptionHandler::class);
        $response = $handler->render($request, new TokenMismatchException('CSRF token mismatch.'));

        $this->assertGuest();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('logout.page'), $response->headers->get('Location'));
    }

    public function test_other_routes_with_419_render_custom_bps_error_page_instead_of_dark_default(): void
    {
        $request = Request::create('/login', 'POST');
        $request->setLaravelSession(app('session.store'));

        $handler = app(ExceptionHandler::class);
        $response = $handler->render($request, new TokenMismatchException('CSRF token mismatch.'));

        $this->assertEquals(419, $response->getStatusCode());
        $this->assertStringContainsString('Sesi Halaman Kedaluwarsa', $response->getContent());
        $this->assertStringContainsString('Muat Ulang Halaman', $response->getContent());
        $this->assertStringContainsString('Masuk ke SIPABIS', $response->getContent());
    }
}
