<?php

namespace Tests\Unit;

use App\Models\AiProvider;
use App\Models\AiSetting;
use App\Models\AiUsageLog;
use App\Services\AiGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiGatewayServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AiGatewayService $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = app(AiGatewayService::class);
    }

    public function test_api_key_is_encrypted_at_rest_and_masked_in_serialization(): void
    {
        $rawKey = 'gsk_super_secret_test_key_9988';

        $provider = AiProvider::create([
            'nama' => 'Groq LPU Free',
            'provider_type' => 'groq',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://api.groq.com/openai/v1',
            'api_key' => $rawKey,
            'model' => 'openai/gpt-oss-20b',
            'is_active' => true,
        ]);

        // Nilai raw terenkripsi di database dan berbeda dari plain text
        $dbRaw = $provider->getRawOriginal('api_key');
        $this->assertNotSame($rawKey, $dbRaw);
        $this->assertSame($rawKey, $provider->resolveDecryptedApiKey());

        // Serialization tidak pernah membocorkan API key asli
        $serialized = $provider->toJson();
        $this->assertStringNotContainsString($rawKey, $serialized);
        $this->assertSame('gsk_••••••••9988', $provider->masked_api_key);
    }

    public function test_detects_provider_from_api_key_prefixes(): void
    {
        $this->assertSame('gemini', $this->gateway->detectProviderFromKey('AIzaSyTest123456'));
        $this->assertSame('groq', $this->gateway->detectProviderFromKey('gsk_abc123456'));
        $this->assertSame('openrouter', $this->gateway->detectProviderFromKey('sk-or-v1-abc123'));
        $this->assertSame('openai', $this->gateway->detectProviderFromKey('sk-proj-abc123'));
        $this->assertNull($this->gateway->detectProviderFromKey('custom-local-token'));
    }

    public function test_chat_uses_active_openai_compatible_provider_and_logs_tokens(): void
    {
        $provider = AiProvider::create([
            'nama' => 'OpenRouter Gratis',
            'provider_type' => 'openrouter',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://openrouter.ai/api/v1',
            'api_key' => 'sk-or-v1-test-key-0001',
            'model' => 'openrouter/free',
            'is_active' => true,
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '• Kertas HVS A4: 25 Rim.',
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 45,
                    'completion_tokens' => 12,
                    'total_tokens' => 57,
                ],
            ], 200),
        ]);

        $result = $this->gateway->chat('Cek kertas HVS');

        $this->assertTrue($result['success']);
        $this->assertSame('• Kertas HVS A4: 25 Rim.', $result['reply']);
        $this->assertSame('openrouter/free', $result['model']);

        $provider->refresh();
        $this->assertSame(1, $provider->total_requests);
        $this->assertSame(45, $provider->total_prompt_tokens);
        $this->assertSame(12, $provider->total_completion_tokens);
        $this->assertSame('connected', $provider->status_koneksi);
        $this->assertDatabaseHas('ai_usage_logs', [
            'ai_provider_id' => $provider->id,
            'status' => 'success',
            'total_tokens' => 57,
        ]);
    }

    public function test_chat_auto_fallbacks_when_primary_provider_hits_rate_limit(): void
    {
        $primary = AiProvider::create([
            'nama' => 'Gemini Utama',
            'provider_type' => 'gemini',
            'protocol' => 'gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'api_key' => 'AIzaPrimaryLimitedKey1234',
            'model' => 'gemini-3.1-flash-lite',
            'is_active' => true,
            'is_fallback' => false,
        ]);

        $fallback = AiProvider::create([
            'nama' => 'Groq Cadangan',
            'provider_type' => 'groq',
            'protocol' => 'openai_compatible',
            'base_url' => 'https://api.groq.com/openai/v1',
            'api_key' => 'gsk_fallback_working_5678',
            'model' => 'openai/gpt-oss-20b',
            'is_active' => false,
            'is_fallback' => true,
        ]);

        AiSetting::current()->update(['auto_fallback' => true]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'Quota exceeded for AIzaPrimaryLimitedKey1234'],
            ], 429),
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Respon sukses dari cadangan Groq!']],
                ],
                'usage' => ['prompt_tokens' => 30, 'completion_tokens' => 8, 'total_tokens' => 38],
            ], 200),
        ]);

        $result = $this->gateway->chat('Halo asisten');

        $this->assertTrue($result['success']);
        $this->assertSame('Respon sukses dari cadangan Groq!', $result['reply']);
        $this->assertSame('groq', $result['provider_type']);

        $primary->refresh();
        $this->assertSame('error', $primary->status_koneksi);
        // Pastikan key tidak bocor ke error message di DB!
        $this->assertStringNotContainsString('AIzaPrimaryLimitedKey1234', (string) $primary->last_error);

        $fallback->refresh();
        $this->assertSame('connected', $fallback->status_koneksi);
        $this->assertSame(1, $fallback->total_requests);
    }

    public function test_fetch_openrouter_models_filters_only_zero_cost_free_models(): void
    {
        Http::fake([
            'https://openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    [
                        'id' => 'openai/gpt-4o',
                        'name' => 'GPT-4o (Paid)',
                        'pricing' => ['prompt' => '0.000005', 'completion' => '0.000015'],
                    ],
                    [
                        'id' => 'openrouter/free',
                        'name' => 'Free Models Router',
                        'pricing' => ['prompt' => '0', 'completion' => '0'],
                    ],
                    [
                        'id' => 'google/gemma-3-4b-it:free',
                        'name' => 'Gemma 3 4B (free)',
                        'pricing' => ['prompt' => '0', 'completion' => '0'],
                    ],
                ],
            ], 200),
        ]);

        $res = $this->gateway->fetchAvailableModels(
            protocol: 'openai_compatible',
            baseUrl: 'https://openrouter.ai/api/v1',
            apiKey: 'sk-or-v1-test',
            providerType: 'openrouter'
        );

        $this->assertTrue($res['success']);
        $ids = array_column($res['models'], 'id');
        $this->assertContains('openrouter/free', $ids);
        $this->assertContains('google/gemma-3-4b-it:free', $ids);
        $this->assertNotContains('openai/gpt-4o', $ids);
    }
}
