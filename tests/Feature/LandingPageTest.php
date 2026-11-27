<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_successfully_with_catalog_data(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHasAll([
            'totalBarang',
            'totalKategori',
            'totalRuangan',
            'kategoriList',
            'topKategori',
            'barangContoh',
        ]);

        $response->assertSee('Eksplorasi Barang Persediaan');
        $response->assertSee('Semua Kategori');
        $response->assertSee('Alat Tulis Kantor');
        $response->assertSee('KATALOG LENGKAP', false);
    }

    public function test_landing_page_contains_interactive_realtime_filter_attributes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('x-data="{', false);
        $response->assertSee('searchQuery', false);
        $response->assertSee('allFiltered', false);
        $response->assertSee('displayLimit', false);
        $response->assertSee('@filter-kategori.window', false);
    }

    public function test_landing_page_navigation_anchors_match_target_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verify top bar links
        $response->assertSee('/#beranda', false);
        $response->assertSee('/#kategori', false);
        $response->assertSee('/#alur', false);
        $response->assertSee('/#layanan', false);
        $response->assertSee('/#katalog', false);
        $response->assertSee('/#tentang', false);

        // Verify destination section and footer IDs
        $response->assertSee('id="beranda"', false);
        $response->assertSee('id="kategori"', false);
        $response->assertSee('id="alur"', false);
        $response->assertSee('id="layanan"', false);
        $response->assertSee('id="katalog"', false);
        $response->assertSee('id="tentang"', false);
    }
}
