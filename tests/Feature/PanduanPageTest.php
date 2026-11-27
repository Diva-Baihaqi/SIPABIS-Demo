<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanduanPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contains_panduan_navigation_and_hero_button(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('panduan.index'), false);
        $response->assertSee('Panduan', false);
        $response->assertSee('Buku Panduan', false);
    }

    public function test_panduan_page_can_be_accessed_publicly_and_contains_three_role_tabs(): void
    {
        $response = $this->get(route('panduan.index'));

        $response->assertStatus(200);

        // Judul, pencarian live, cetak, dan 3 tab sesuai urutan (1. Pegawai, 2. Pimpinan, 3. Admin)
        $response->assertSee('Buku Panduan Penggunaan SIPABIS', false);
        $response->assertSee('Panduan Pegawai', false);
        $response->assertSee('Panduan Pimpinan', false);
        $response->assertSee('Panduan Admin', false);
        $response->assertSee('searchQuery', false);
        $response->assertSee('window.print()', false);

        // Konten komprehensif Tab 1: Pegawai
        $response->assertSee('Ajukan ATK Baru', false);
        $response->assertSee('PSD-133', false);
        $response->assertSee('Usulan Barang Baru', false);
        $response->assertSee('Buku Kendali Ruangan', false);
        $response->assertSee('Asisten AI', false);

        // Konten komprehensif Tab 2: Pimpinan
        $response->assertSee('Dashboard Eksekutif', false);
        $response->assertSee('Audit 7 Unit Kerja', false);
        $response->assertSee('Pengesahan Stock Opname (BASO)', false);
        $response->assertSee('Neraca SAKTI', false);

        // Konten komprehensif Tab 3: Admin
        $response->assertSee('Verifikasi &amp; Serah Terima', false);
        $response->assertSee('Penerimaan Barang', false);
        $response->assertSee('Manajemen Pengguna', false);
        $response->assertSee('Manajemen Asisten AI', false);
    }

    public function test_panduan_page_respects_tab_query_parameter_with_safe_fallback(): void
    {
        $pimpinanRes = $this->get(route('panduan.index', ['tab' => 'pimpinan']));
        $pimpinanRes->assertStatus(200);
        $pimpinanRes->assertSee("activeTab: 'pimpinan'", false);

        $adminRes = $this->get(route('panduan.index', ['tab' => 'admin']));
        $adminRes->assertStatus(200);
        $adminRes->assertSee("activeTab: 'admin'", false);

        $invalidRes = $this->get(route('panduan.index', ['tab' => 'hacked']));
        $invalidRes->assertStatus(200);
        $invalidRes->assertSee("activeTab: 'pegawai'", false);
    }

    public function test_authenticated_user_can_view_panduan_and_still_sees_dashboard_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('panduan.index'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard (Admin)', false);
    }

    public function test_panduan_page_uses_clean_focused_handbook_layout_without_stacked_sticky_bars(): void
    {
        $response = $this->get(route('panduan.index'));

        $response->assertStatus(200);

        // Mode fokus per bab (Interactive Digital Handbook) + toggle lihat semua
        $response->assertSee('activeSection', false);
        $response->assertSee('viewMode', false);
        $response->assertSee('Bab Sebelumnya', false);
        $response->assertSee('Bab Selanjutnya', false);
        $response->assertSee('Tampilkan Semua Bab', false);
        $response->assertSee('Pilih Bab Panduan', false);

        // Tidak ada lagi double-sticky tab bar yang menumpuk di bawah navbar mengambang
        $response->assertDontSee('sticky top-20 sm:top-24', false);
    }

    public function test_panduan_page_displays_official_book_download_and_preview_actions(): void
    {
        $publicBookPath = public_path('doc/BUKU_SIPABIS.pdf');
        $this->assertFileExists($publicBookPath);

        $expectedSizeLabel = number_format(filesize($publicBookPath) / (1024 * 1024), 1, ',', '.').' MB';

        $response = $this->get(route('panduan.index'));

        $response->assertStatus(200);
        $response->assertSee(route('panduan.buku.download'), false);
        $response->assertSee(route('panduan.buku.preview'), false);
        $response->assertSee('Unduh Buku', false);
        $response->assertSee('Pratinjau di Browser', false);
        $response->assertSee('69 Halaman', false);
        $response->assertSee('PDF B5', false);
        $response->assertSee($expectedSizeLabel, false);
        $response->assertSee('cover-sipabis-thumb.webp', false);
    }

    public function test_panduan_book_download_route_serves_pdf_attachment(): void
    {
        $response = $this->get(route('panduan.buku.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $disposition = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('BUKU-SIPABIS-BPS-Kabupaten-Cirebon.pdf', $disposition);
        $this->assertSame(
            realpath(public_path('doc/BUKU_SIPABIS.pdf')),
            $response->baseResponse->getFile()->getRealPath()
        );
    }

    public function test_panduan_book_preview_route_serves_inline_pdf(): void
    {
        $response = $this->get(route('panduan.buku.preview'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $disposition = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $disposition);
        $this->assertStringContainsString('BUKU-SIPABIS-BPS-Kabupaten-Cirebon.pdf', $disposition);
        $this->assertSame(
            realpath(public_path('doc/BUKU_SIPABIS.pdf')),
            $response->baseResponse->getFile()->getRealPath()
        );
    }
}
