<?php

namespace Tests\Feature;

use App\Models\AiProvider;
use App\Models\AiSetting;
use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pegawai;
    protected User $pimpinan;

    protected function setUp(): void
    {
        parent::setUp();

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'UMUM',
            'nama_ruangan' => 'Subbagian Umum',
            'penanggung_jawab' => 'Kasubbag Umum',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Persediaan',
            'nip' => 'admin_test_ai',
            'role' => 'admin',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->pegawai = User::factory()->create([
            'name' => 'Pegawai Biasa',
            'nip' => 'pegawai_test_ai',
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
        ]);

        $this->pimpinan = User::factory()->create([
            'name' => 'Pimpinan Satker',
            'nip' => 'pimpinan_test_ai',
            'role' => 'pimpinan',
            'ruangan_id' => $ruangan->id,
        ]);
    }

    public function test_admin_can_access_manajemen_ai_and_sidebar_shows_link_under_administrasi(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ai.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen & Konfigurasi AI', false);
        $response->assertSee('Manajemen AI');
        $response->assertSee('Koneksi Cepat AI (Paste API Key &amp; Konek)', false);
        $response->assertSee('Administrasi', false);
    }

    public function test_non_admin_cannot_access_manajemen_ai(): void
    {
        $this->actingAs($this->pegawai)->get(route('admin.ai.index'))->assertStatus(403);
        $this->actingAs($this->pimpinan)->get(route('admin.ai.index'))->assertStatus(403);

        $this->actingAs($this->pegawai)->postJson(route('admin.ai.quick-connect'), [
            'provider_type' => 'groq',
            'protocol' => 'openai_compatible',
            'model' => 'openai/gpt-oss-20b',
            'api_key' => 'gsk_test_blocked',
        ])->assertStatus(403);

        // Pegawai tidak melihat link Manajemen AI di sidebar
        $dash = $this->actingAs($this->pegawai)->get(route('pegawai.dashboard'));
        $dash->assertStatus(200);
        $dash->assertDontSee(route('admin.ai.index'));
    }

    public function test_admin_can_quick_connect_with_any_provider_api_key_and_activate_immediately(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'OK']]],
                'usage' => ['prompt_tokens' => 8, 'completion_tokens' => 2, 'total_tokens' => 10],
            ], 200),
        ]);

        $rawKey = 'gsk_live_quick_connect_123456';

        $response = $this->actingAs($this->admin)->postJson(route('admin.ai.quick-connect'), [
            'nama' => 'Groq LPU Gratis',
            'provider_type' => 'groq',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://api.groq.com/openai/v1',
            'api_key' => $rawKey,
            'model' => 'openai/gpt-oss-20b',
            'is_fallback' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'connected' => true,
            ]);

        // Pastikan API key asli TIDAK pernah muncul di response JSON
        $this->assertStringNotContainsString($rawKey, $response->getContent());
        $response->assertJsonPath('provider.masked_api_key', 'gsk_••••••••3456');

        $this->assertDatabaseHas('ai_providers', [
            'nama' => 'Groq LPU Gratis',
            'provider_type' => 'groq',
            'model' => 'openai/gpt-oss-20b',
            'is_active' => true,
            'is_fallback' => true,
            'status_koneksi' => 'connected',
        ]);
    }

    public function test_quick_connect_rejects_invalid_api_key_and_does_not_overwrite_active_provider(): void
    {
        $existing = AiProvider::create([
            'nama' => 'Gemini Aktif Lama',
            'provider_type' => 'gemini',
            'protocol' => 'gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'api_key' => 'AIzaOldValidKey0001',
            'model' => 'gemini-3.1-flash-lite',
            'is_active' => true,
            'status_koneksi' => 'connected',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Invalid API key provided'],
            ], 401),
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.ai.quick-connect'), [
            'provider_type' => 'openrouter',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://openrouter.ai/api/v1',
            'api_key' => 'sk-or-v1-wrong-key',
            'model' => 'openrouter/free',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        // Provider lama harus tetap aktif
        $this->assertTrue($existing->fresh()->is_active);
        $this->assertDatabaseCount('ai_providers', 1);
    }

    public function test_admin_can_switch_active_provider_toggle_fallback_retest_and_delete(): void
    {
        $provA = AiProvider::create([
            'nama' => 'Provider A',
            'provider_type' => 'gemini',
            'protocol' => 'gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'api_key' => 'AIzaKeyA1234',
            'model' => 'gemini-3.1-flash-lite',
            'is_active' => true,
            'is_fallback' => false,
        ]);

        $provB = AiProvider::create([
            'nama' => 'Provider B',
            'provider_type' => 'openrouter',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://openrouter.ai/api/v1',
            'api_key' => 'sk-or-v1-KeyB5678',
            'model' => 'openrouter/free',
            'is_active' => false,
            'is_fallback' => false,
        ]);

        // Aktifkan Provider B
        $this->actingAs($this->admin)
            ->post(route('admin.ai.providers.activate', $provB))
            ->assertRedirect(route('admin.ai.index'));

        $this->assertFalse($provA->fresh()->is_active);
        $this->assertTrue($provB->fresh()->is_active);

        // Jadikan Provider A cadangan
        $this->actingAs($this->admin)
            ->post(route('admin.ai.providers.toggle-fallback', $provA))
            ->assertRedirect(route('admin.ai.index'));

        $this->assertTrue($provA->fresh()->is_fallback);

        // Uji koneksi ulang Provider B
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'OK']]],
                'usage' => ['prompt_tokens' => 5, 'completion_tokens' => 1, 'total_tokens' => 6],
            ], 200),
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.ai.providers.test', $provB))
            ->assertRedirect(route('admin.ai.index'));

        $this->assertSame('connected', $provB->fresh()->status_koneksi);

        // Hapus Provider A
        $this->actingAs($this->admin)
            ->delete(route('admin.ai.providers.destroy', $provA))
            ->assertRedirect(route('admin.ai.index'));

        $this->assertDatabaseMissing('ai_providers', ['id' => $provA->id]);
    }

    public function test_admin_can_fetch_live_free_models_and_run_sandbox_chat(): void
    {
        AiProvider::create([
            'nama' => 'Custom Endpoint Free',
            'provider_type' => 'custom',
            'protocol' => 'openai_compatible',
            'base_url' => 'http://localhost:11434/v1',
            'api_key' => null,
            'model' => 'llama3.2:3b',
            'is_active' => true,
        ]);

        $kategori = KategoriBarang::create(['kode_kategori' => 'ATK', 'nama_kategori' => 'Alat Tulis Kantor']);
        BarangPersediaan::create([
            'kode_barang' => '000010',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kertas HVS A4',
            'satuan' => 'Rim',
            'stok_minimum' => 5,
            'stok_aktif' => 42,
            'lokasi_rak' => 'Rak B-2',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    ['id' => 'openrouter/free', 'name' => 'Free Auto', 'pricing' => ['prompt' => '0', 'completion' => '0']],
                    ['id' => 'paid/model-x', 'name' => 'Paid X', 'pricing' => ['prompt' => '0.01', 'completion' => '0.02']],
                ],
            ], 200),
            'http://localhost:11434/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => '• Kertas HVS A4: 42 Rim (Lokasi: Rak B-2).']]],
                'usage' => ['prompt_tokens' => 40, 'completion_tokens' => 15, 'total_tokens' => 55],
            ], 200),
        ]);

        $fetchRes = $this->actingAs($this->admin)->postJson(route('admin.ai.fetch-models'), [
            'provider_type' => 'openrouter',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://openrouter.ai/api/v1',
            'api_key' => 'sk-or-v1-test',
        ]);

        $fetchRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonFragment(['id' => 'openrouter/free'])
            ->assertJsonMissing(['id' => 'paid/model-x']);

        $sandboxRes = $this->actingAs($this->admin)->postJson(route('admin.ai.sandbox-chat'), [
            'message' => 'Cek stok kertas HVS',
        ]);

        $sandboxRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'model' => 'llama3.2:3b',
                'usage' => ['total_tokens' => 55],
            ]);
    }

    public function test_admin_can_update_token_settings_rules_and_toggle_global_chatbot(): void
    {
        $this->actingAs($this->admin)->put(route('admin.ai.settings.update'), [
            'max_output_tokens' => 180,
            'temperature' => 0.05,
            'timeout_seconds' => 20,
            'disable_thinking' => 1,
            'auto_fallback' => 1,
            'assistant_name' => 'Asisten Hemat SIPABIS',
            'assistant_tone' => 'Sangat singkat dan padat.',
            'rules_text' => "Aturan 1 hemat token\nAturan 2 wajib faktual",
        ])->assertRedirect(route('admin.ai.index'));

        $setting = AiSetting::current()->fresh();
        $this->assertSame(180, $setting->max_output_tokens);
        $this->assertTrue($setting->disable_thinking);
        $this->assertSame(['Aturan 1 hemat token', 'Aturan 2 wajib faktual'], $setting->rules_json);

        // Matikan Asisten AI secara global
        $this->actingAs($this->admin)
            ->post(route('admin.ai.toggle-enabled'))
            ->assertRedirect(route('admin.ai.index'));

        $this->assertFalse(AiSetting::current()->fresh()->is_enabled);

        // Widget chatbot tersembunyi di halaman publik & pegawai, dan /api/chat menolak permintaan
        $this->get('/')->assertDontSee('function sipabisChatbot()', false);
        $this->postJson(route('api.chat'), ['message' => 'Halo?'])
            ->assertStatus(503)
            ->assertJson(['success' => false]);

        // Reset settings ke default BPS
        $this->actingAs($this->admin)->put(route('admin.ai.settings.update'), [
            'reset_defaults' => 1,
        ])->assertRedirect(route('admin.ai.index'));

        $this->assertSame('Asisten SIPABIS AI', AiSetting::current()->fresh()->assistant_name);
    }
}
