<?php

namespace App\Services;

use App\Models\AiProvider;
use App\Models\AiSetting;
use App\Models\AiUsageLog;
use App\Models\BarangPersediaan;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGatewayService
{
    /**
     * Daftar preset penyedia AI yang difokuskan pada tier Gratis ($0), latensi tercepat, dan konsumsi token rendah.
     */
    public function getProviderPresets(): array
    {
        return [
            'gemini' => [
                'key' => 'gemini',
                'label' => 'Google Gemini (AI Studio)',
                'protocol' => 'gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'requires_api_key' => true,
                'key_prefix_hint' => 'AIza...',
                'is_free_tier' => true,
                'badge' => 'Gratis $0 • Flash-Lite Tercepat',
                'description' => 'Free Tier resmi Google AI Studio tanpa kartu kredit. Seri Flash-Lite paling ringan, cepat, dan hemat token untuk tanya-jawab stok.',
                'default_model' => 'gemini-3.1-flash-lite',
                'recommended_models' => [
                    [
                        'id' => 'gemini-3.1-flash-lite',
                        'name' => 'Gemini 3.1 Flash-Lite',
                        'note' => 'Gratis $0 • Paling cepat & hemat token (Rekomendasi)',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'gemini-3.5-flash-lite',
                        'name' => 'Gemini 3.5 Flash-Lite',
                        'note' => 'Gratis $0 • Flash-Lite generasi terbaru',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'gemini-2.5-flash-lite',
                        'name' => 'Gemini 2.5 Flash-Lite',
                        'note' => 'Gratis $0 • Alternatif ringan',
                        'is_free' => true,
                    ],
                ],
            ],
            'groq' => [
                'key' => 'groq',
                'label' => 'Groq Cloud (LPU Super Cepat)',
                'protocol' => 'openai_compatible',
                'base_url' => 'https://api.groq.com/openai/v1',
                'requires_api_key' => true,
                'key_prefix_hint' => 'gsk_...',
                'is_free_tier' => true,
                'badge' => 'Gratis $0 • Hingga 1.000 tok/s',
                'description' => 'Free Plan Groq berbasis chip LPU dengan kecepatan respons kilat dan batas kuota harian gratis tanpa kartu kredit.',
                'default_model' => 'openai/gpt-oss-20b',
                'recommended_models' => [
                    [
                        'id' => 'openai/gpt-oss-20b',
                        'name' => 'GPT-OSS 20B (Groq)',
                        'note' => 'Gratis $0 • ~1.000 token/detik sangat cepat',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'qwen/qwen3.8-27b',
                        'name' => 'Qwen 3.8 27B (Groq)',
                        'note' => 'Gratis $0 • Ringkas & akurat berbahasa Indonesia',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'openai/gpt-oss-120b',
                        'name' => 'GPT-OSS 120B (Groq)',
                        'note' => 'Gratis $0 • Kapasitas penalaran lebih tinggi',
                        'is_free' => true,
                    ],
                ],
            ],
            'openrouter' => [
                'key' => 'openrouter',
                'label' => 'OpenRouter (:free Gateway)',
                'protocol' => 'openai_compatible',
                'base_url' => 'https://openrouter.ai/api/v1',
                'requires_api_key' => true,
                'key_prefix_hint' => 'sk-or-v1-...',
                'is_free_tier' => true,
                'badge' => 'Gratis $0 • Auto-Router :free',
                'description' => 'Satu API key untuk banyak model $0 (:free). Gunakan openrouter/free agar otomatis diarahkan ke model gratis tercepat yang tersedia.',
                'default_model' => 'openrouter/free',
                'recommended_models' => [
                    [
                        'id' => 'openrouter/free',
                        'name' => 'OpenRouter Free Auto-Router',
                        'note' => 'Gratis $0 • Otomatis pilih model :free tercepat yang aktif',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'google/gemma-3-4b-it:free',
                        'name' => 'Gemma 3 4B (:free)',
                        'note' => 'Gratis $0 • Model mini super ringan',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'nvidia/nemotron-3-nano-30b-a3b:free',
                        'name' => 'NVIDIA Nemotron 3 Nano (:free)',
                        'note' => 'Gratis $0 • Efisien & cepat',
                        'is_free' => true,
                    ],
                ],
            ],
            'mistral' => [
                'key' => 'mistral',
                'label' => 'Mistral AI (La Plateforme)',
                'protocol' => 'openai_compatible',
                'base_url' => 'https://api.mistral.ai/v1',
                'requires_api_key' => true,
                'key_prefix_hint' => 'API Key Mistral...',
                'is_free_tier' => true,
                'badge' => 'Free Experiment / Small',
                'description' => 'Mendukung API key Mistral La Plateforme dengan endpoint OpenAI-compatible standar.',
                'default_model' => 'mistral-small-latest',
                'recommended_models' => [
                    [
                        'id' => 'mistral-small-latest',
                        'name' => 'Mistral Small Latest',
                        'note' => 'Ringan & cepat (cek kuota free akun Mistral)',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'open-mistral-nemo',
                        'name' => 'Open Mistral Nemo',
                        'note' => 'Model open-weight cepat',
                        'is_free' => true,
                    ],
                ],
            ],
            'deepseek' => [
                'key' => 'deepseek',
                'label' => 'DeepSeek API',
                'protocol' => 'openai_compatible',
                'base_url' => 'https://api.deepseek.com/v1',
                'requires_api_key' => true,
                'key_prefix_hint' => 'sk-...',
                'is_free_tier' => false,
                'badge' => 'OpenAI-Compatible • Hemat',
                'description' => 'Mendukung API key DeepSeek (`deepseek-chat` non-thinking lebih cepat dan hemat token).',
                'default_model' => 'deepseek-chat',
                'recommended_models' => [
                    [
                        'id' => 'deepseek-chat',
                        'name' => 'DeepSeek Chat',
                        'note' => 'Cepat & hemat token (non-thinking)',
                        'is_free' => false,
                    ],
                ],
            ],
            'openai' => [
                'key' => 'openai',
                'label' => 'OpenAI (ChatGPT API)',
                'protocol' => 'openai_compatible',
                'base_url' => 'https://api.openai.com/v1',
                'requires_api_key' => true,
                'key_prefix_hint' => 'sk-... / sk-proj-...',
                'is_free_tier' => false,
                'badge' => 'OpenAI Resmi • Mini Series',
                'description' => 'Mendukung API key OpenAI resmi dengan varian mini yang cepat dan rendah token.',
                'default_model' => 'gpt-4o-mini',
                'recommended_models' => [
                    [
                        'id' => 'gpt-4o-mini',
                        'name' => 'GPT-4o Mini',
                        'note' => 'Cepat & ringan di ekosistem OpenAI',
                        'is_free' => false,
                    ],
                ],
            ],
            'custom' => [
                'key' => 'custom',
                'label' => 'Custom Provider / Ollama Lokal ($0)',
                'protocol' => 'openai_compatible',
                'base_url' => 'http://localhost:11434/v1',
                'requires_api_key' => false,
                'key_prefix_hint' => 'Opsional (boleh kosong untuk Ollama lokal)',
                'is_free_tier' => true,
                'badge' => 'Gratis $0 • API Key / Endpoint Bebas',
                'description' => 'Gunakan API key dari gateway atau server AI mana saja (Ollama lokal, LM Studio, vLLM, Cloudflare AI, Together, dll.) yang mendukung OpenAI-compatible atau Gemini.',
                'default_model' => 'llama3.2:3b',
                'recommended_models' => [
                    [
                        'id' => 'llama3.2:3b',
                        'name' => 'Llama 3.2 3B (Lokal)',
                        'note' => 'Gratis $0 tanpa batas jika pakai Ollama lokal',
                        'is_free' => true,
                    ],
                    [
                        'id' => 'qwen2.5:3b',
                        'name' => 'Qwen 2.5 3B (Lokal)',
                        'note' => 'Gratis $0 • Ringan di RAM/CPU',
                        'is_free' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Deteksi otomatis tipe provider dari format awalan API key yang ditempel Admin.
     */
    public function detectProviderFromKey(?string $apiKey): ?string
    {
        $trimmed = trim((string) $apiKey);
        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, 'AIza')) {
            return 'gemini';
        }
        if (str_starts_with($trimmed, 'gsk_')) {
            return 'groq';
        }
        if (str_starts_with($trimmed, 'sk-or-')) {
            return 'openrouter';
        }
        if (str_starts_with($trimmed, 'sk-proj-') || str_starts_with($trimmed, 'sk-')) {
            return 'openai';
        }

        return null;
    }

    /**
     * Uji koneksi ke endpoint AI dengan ping minimal yang sangat hemat token.
     */
    public function testConnection(
        string $protocol,
        ?string $baseUrl,
        ?string $apiKey,
        string $model,
        string $providerType = 'custom',
        ?AiSetting $setting = null
    ): array {
        $setting = $setting ?? AiSetting::current();
        $cleanKey = $apiKey !== null ? trim($apiKey) : null;

        if ($protocol === 'gemini' && empty($cleanKey)) {
            return [
                'success' => false,
                'latency_ms' => 0,
                'error' => 'API Key wajib diisi untuk penyedia Google Gemini.',
                'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
            ];
        }

        $presets = $this->getProviderPresets();
        $presetRequiresKey = $presets[$providerType]['requires_api_key'] ?? false;
        if ($presetRequiresKey && empty($cleanKey)) {
            return [
                'success' => false,
                'latency_ms' => 0,
                'error' => "API Key wajib diisi untuk penyedia {$providerType}.",
                'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
            ];
        }

        $pingSystem = 'Jawab 1 kata saja: OK';
        $pingPrompt = 'Ping';

        return $this->dispatchPrompt(
            protocol: $protocol,
            baseUrl: $baseUrl,
            apiKey: $cleanKey,
            model: $model,
            systemInstruction: $pingSystem,
            userPrompt: $pingPrompt,
            setting: $setting,
            providerType: $providerType,
            maxTokensOverride: 10
        );
    }

    /**
     * Ambil daftar model gratis / ringan yang benar-benar aktif langsung dari API provider.
     */
    public function fetchAvailableModels(
        string $protocol,
        ?string $baseUrl,
        ?string $apiKey,
        string $providerType = 'custom'
    ): array {
        $cleanKey = $apiKey !== null ? trim($apiKey) : null;
        $presets = $this->getProviderPresets();
        $resolvedBase = trim((string) ($baseUrl ?: ($presets[$providerType]['base_url'] ?? '')));

        if ($resolvedBase === '') {
            return [
                'success' => false,
                'models' => [],
                'message' => 'Base URL provider tidak valid.',
            ];
        }

        try {
            if ($protocol === 'gemini') {
                if (empty($cleanKey)) {
                    return [
                        'success' => false,
                        'models' => [],
                        'message' => 'Tempel API Key Gemini terlebih dahulu untuk mengambil daftar model live.',
                    ];
                }

                $url = $this->normalizeGeminiBaseUrl($resolvedBase) . '/models';
                $response = Http::timeout(10)->get($url, ['key' => $cleanKey]);

                if (!$response->successful()) {
                    return [
                        'success' => false,
                        'models' => [],
                        'message' => $this->mapHttpError($response, 'gemini', $providerType, $cleanKey),
                    ];
                }

                $rawModels = $response->json('models', []);
                $models = [];
                foreach ($rawModels as $m) {
                    $methods = $m['supportedGenerationMethods'] ?? [];
                    if (!in_array('generateContent', $methods, true)) {
                        continue;
                    }
                    $id = preg_replace('/^models\//', '', (string) ($m['name'] ?? ''));
                    if ($id === '' || str_contains($id, 'embedding') || str_contains($id, 'tts') || str_contains($id, 'imagen')) {
                        continue;
                    }
                    $isLite = str_contains($id, 'flash-lite') || str_contains($id, 'flash');
                    $models[] = [
                        'id' => $id,
                        'name' => (string) ($m['displayName'] ?? $id),
                        'note' => str_contains($id, 'flash-lite') ? 'Gratis $0 • Flash-Lite Hemat & Cepat' : (str_contains($id, 'flash') ? 'Gratis $0 • Flash Cepat' : 'Model Gemini'),
                        'is_free' => true,
                        'priority' => str_contains($id, 'flash-lite') ? 0 : ($isLite ? 1 : 2),
                    ];
                }

                usort($models, fn ($a, $b) => $a['priority'] <=> $b['priority']);

                return [
                    'success' => true,
                    'models' => array_slice($models, 0, 30),
                    'message' => count($models) . ' model Gemini aktif berhasil dimuat.',
                ];
            }

            // Protokol OpenAI-Compatible (OpenRouter, Groq, Mistral, DeepSeek, Custom/Ollama)
            $modelsUrl = $this->buildOpenAiModelsEndpoint($resolvedBase);
            $headers = ['Accept' => 'application/json'];
            if (!empty($cleanKey)) {
                $headers['Authorization'] = "Bearer {$cleanKey}";
            }

            $response = Http::timeout(12)->withHeaders($headers)->get($modelsUrl);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'models' => [],
                    'message' => $this->mapHttpError($response, 'models', $providerType, $cleanKey),
                ];
            }

            $rawList = $response->json('data', []);
            if (!is_array($rawList) && is_array($response->json('models'))) {
                $rawList = $response->json('models');
            }

            $models = [];
            foreach ($rawList as $m) {
                $id = (string) ($m['id'] ?? $m['name'] ?? '');
                if ($id === '') {
                    continue;
                }

                // Untuk OpenRouter: hanya tampilkan model yang benar-benar GRATIS ($0)
                if ($providerType === 'openrouter' || str_contains($resolvedBase, 'openrouter.ai')) {
                    $promptPrice = (string) ($m['pricing']['prompt'] ?? '');
                    $completionPrice = (string) ($m['pricing']['completion'] ?? '');
                    $isZeroPriced = ($promptPrice === '0' && $completionPrice === '0')
                        || str_ends_with($id, ':free')
                        || $id === 'openrouter/free';

                    if (!$isZeroPriced) {
                        continue;
                    }
                }

                // Hindari model audio-only/guard/whisper jika teks chat
                $lowerId = strtolower($id);
                if (str_contains($lowerId, 'whisper') || str_contains($lowerId, 'tts') || str_contains($lowerId, 'guard')) {
                    continue;
                }

                $isFastOrMini = str_contains($lowerId, 'free')
                    || str_contains($lowerId, 'lite')
                    || str_contains($lowerId, 'flash')
                    || str_contains($lowerId, 'mini')
                    || str_contains($lowerId, 'instant')
                    || str_contains($lowerId, '20b')
                    || str_contains($lowerId, '8b')
                    || str_contains($lowerId, '4b')
                    || str_contains($lowerId, '3b');

                $models[] = [
                    'id' => $id,
                    'name' => (string) ($m['name'] ?? $id),
                    'note' => ($providerType === 'openrouter' || str_ends_with($lowerId, ':free'))
                        ? 'Gratis $0 (Free Tier)'
                        : ($isFastOrMini ? 'Cepat & Hemat Token' : 'Model Aktif'),
                    'is_free' => true,
                    'priority' => $id === 'openrouter/free' ? -1 : ($isFastOrMini ? 0 : 1),
                ];
            }

            usort($models, fn ($a, $b) => $a['priority'] <=> $b['priority']);

            return [
                'success' => true,
                'models' => array_slice($models, 0, 40),
                'message' => count($models) . ' model aktif berhasil dimuat.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'models' => [],
                'message' => 'Gagal mengambil daftar model: ' . $this->sanitizeErrorMessage($e->getMessage(), $cleanKey),
            ];
        }
    }

    /**
     * Proses chat utama Asisten Virtual SIPABIS dengan dukungan Multi-Provider, Auto-Fallback & Tracking Token.
     */
    public function chat(string $userMessage, ?User $user = null, string $triggerSource = 'chatbot'): array
    {
        $setting = $this->resolveCurrentSetting();

        if (!$setting->is_enabled && $triggerSource === 'chatbot') {
            return [
                'success' => false,
                'disabled' => true,
                'reply' => 'Layanan Asisten Virtual SIPABIS sedang dinonaktifkan sementara oleh Administrator.',
            ];
        }

        $systemInstruction = $this->buildSystemInstruction($user, $setting);
        $contextData = $this->extractRelevantContext($userMessage);

        $promptText = $userMessage;
        if ($contextData !== '') {
            $promptText .= "\n\n[Data Stok Terkini]:\n" . $contextData;
        }

        $chain = $this->resolveExecutionChain($setting);

        if (empty($chain)) {
            return [
                'success' => false,
                'reply' => 'Layanan Asisten AI belum dikonfigurasi dengan API key yang valid.',
            ];
        }

        $lastErrorReply = 'Maaf, asisten virtual sedang tidak dapat memproses permintaan Anda saat ini. Silakan coba beberapa saat lagi.';

        foreach ($chain as $candidate) {
            /** @var AiProvider|null $providerModel */
            $providerModel = $candidate['provider_record'];

            $result = $this->dispatchPrompt(
                protocol: $candidate['protocol'],
                baseUrl: $candidate['base_url'],
                apiKey: $candidate['api_key'],
                model: $candidate['model'],
                systemInstruction: $systemInstruction,
                userPrompt: $promptText,
                setting: $setting,
                providerType: $candidate['provider_type']
            );

            $this->recordExecutionMetrics(
                provider: $providerModel,
                providerType: $candidate['provider_type'],
                model: $candidate['model'],
                result: $result,
                triggerSource: $triggerSource
            );

            if ($result['success']) {
                return [
                    'success' => true,
                    'reply' => $result['reply'],
                    'model' => $candidate['model'],
                    'provider_type' => $candidate['provider_type'],
                    'provider_name' => $candidate['provider_name'],
                    'latency_ms' => $result['latency_ms'],
                    'usage' => $result['usage'],
                ];
            }

            if (!empty($result['error'])) {
                $lastErrorReply = $result['error'];
            }
        }

        return [
            'success' => false,
            'reply' => $triggerSource === 'sandbox'
                ? $lastErrorReply
                : 'Maaf, asisten virtual sedang tidak dapat memproses permintaan Anda saat ini. Silakan coba beberapa saat lagi.',
        ];
    }

    /**
     * Eksekusi panggilan HTTP ke protokol Gemini atau OpenAI-Compatible.
     */
    public function dispatchPrompt(
        string $protocol,
        ?string $baseUrl,
        ?string $apiKey,
        string $model,
        string $systemInstruction,
        string $userPrompt,
        AiSetting $setting,
        string $providerType = 'custom',
        ?int $maxTokensOverride = null
    ): array {
        $startedAt = microtime(true);
        $timeout = max(5, min(60, (int) ($setting->timeout_seconds ?: 15)));
        $maxTokens = (int) ($maxTokensOverride ?? $setting->max_output_tokens ?: 250);
        $temperature = (float) ($setting->temperature ?? 0.1);

        try {
            if ($protocol === 'gemini') {
                return $this->sendGeminiRequest(
                    baseUrl: $baseUrl,
                    apiKey: $apiKey,
                    model: $model,
                    systemInstruction: $systemInstruction,
                    userPrompt: $userPrompt,
                    temperature: $temperature,
                    maxTokens: $maxTokens,
                    disableThinking: (bool) $setting->disable_thinking,
                    timeout: $timeout,
                    startedAt: $startedAt
                );
            }

            return $this->sendOpenAiCompatibleRequest(
                baseUrl: $baseUrl,
                apiKey: $apiKey,
                model: $model,
                systemInstruction: $systemInstruction,
                userPrompt: $userPrompt,
                temperature: $temperature,
                maxTokens: $maxTokens,
                timeout: $timeout,
                providerType: $providerType,
                startedAt: $startedAt
            );
        } catch (\Throwable $e) {
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);
            $safeMessage = $this->sanitizeErrorMessage($e->getMessage(), $apiKey);

            Log::error('AI Gateway Connection Exception', [
                'provider' => $providerType,
                'model' => $model,
                'message' => $safeMessage,
            ]);

            return [
                'success' => false,
                'latency_ms' => $latencyMs,
                'error' => 'Kendala jaringan saat menghubungi server AI: ' . $safeMessage,
                'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
            ];
        }
    }

    protected function sendGeminiRequest(
        ?string $baseUrl,
        ?string $apiKey,
        string $model,
        string $systemInstruction,
        string $userPrompt,
        float $temperature,
        int $maxTokens,
        bool $disableThinking,
        int $timeout,
        float $startedAt
    ): array {
        $endpoint = $this->buildGeminiGenerateEndpoint($baseUrl, $model);

        $generationConfig = [
            'temperature' => $temperature,
            'maxOutputTokens' => $maxTokens,
        ];

        // Hemat token pada model Gemini 2.5 / 3.x dengan menonaktifkan thinking tokens internal bila diminta
        if ($disableThinking && (str_contains($model, '2.5') || str_contains($model, '3.'))) {
            $generationConfig['thinkingConfig'] = ['thinkingBudget' => 0];
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userPrompt],
                    ],
                ],
            ],
            'generationConfig' => $generationConfig,
        ];

        $response = Http::timeout($timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$endpoint}?key={$apiKey}", $payload);

        // Jika model menolak parameter thinkingConfig (HTTP 400), ulangi sekali tanpa thinkingConfig
        if ($response->status() === 400 && isset($payload['generationConfig']['thinkingConfig'])) {
            $msg = strtolower((string) $response->json('error.message', ''));
            if (str_contains($msg, 'thinking')) {
                unset($payload['generationConfig']['thinkingConfig']);
                $response = Http::timeout($timeout)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$endpoint}?key={$apiKey}", $payload);
            }
        }

        $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

        if ($response->successful()) {
            $candidates = $response->json('candidates', []);
            $reply = $candidates[0]['content']['parts'][0]['text'] ?? null;
            $usageMeta = $response->json('usageMetadata', []);
            $promptTokens = (int) ($usageMeta['promptTokenCount'] ?? 0);
            $completionTokens = (int) ($usageMeta['candidatesTokenCount'] ?? 0);
            $totalTokens = (int) ($usageMeta['totalTokenCount'] ?? ($promptTokens + $completionTokens));

            if (!empty($reply)) {
                return [
                    'success' => true,
                    'reply' => trim((string) $reply),
                    'latency_ms' => $latencyMs,
                    'usage' => [
                        'prompt_tokens' => $promptTokens,
                        'completion_tokens' => $completionTokens,
                        'total_tokens' => $totalTokens,
                    ],
                ];
            }
        }

        $errorMsg = $this->mapHttpError($response, $model, 'gemini', $apiKey);
        Log::warning('Gemini API Error Response', [
            'status' => $response->status(),
            'model' => $model,
            'error' => $errorMsg,
        ]);

        return [
            'success' => false,
            'latency_ms' => $latencyMs,
            'error' => $errorMsg,
            'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
        ];
    }

    protected function sendOpenAiCompatibleRequest(
        ?string $baseUrl,
        ?string $apiKey,
        string $model,
        string $systemInstruction,
        string $userPrompt,
        float $temperature,
        int $maxTokens,
        int $timeout,
        string $providerType,
        float $startedAt
    ): array {
        $presets = $this->getProviderPresets();
        $resolvedBase = $baseUrl ?: ($presets[$providerType]['base_url'] ?? 'https://api.openai.com/v1');
        $endpoint = $this->buildOpenAiChatEndpoint($resolvedBase);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if (!empty($apiKey)) {
            $headers['Authorization'] = "Bearer {$apiKey}";
        }

        if ($providerType === 'openrouter' || str_contains($resolvedBase, 'openrouter.ai')) {
            $headers['HTTP-Referer'] = (string) config('app.url', 'http://localhost');
            $headers['X-Title'] = 'SIPABIS BPS Kabupaten Cirebon';
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemInstruction],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];

        $response = Http::timeout($timeout)
            ->withHeaders($headers)
            ->post($endpoint, $payload);

        $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

        if ($response->successful()) {
            $reply = $response->json('choices.0.message.content');
            $usageMeta = $response->json('usage', []);
            $promptTokens = (int) ($usageMeta['prompt_tokens'] ?? 0);
            $completionTokens = (int) ($usageMeta['completion_tokens'] ?? 0);
            $totalTokens = (int) ($usageMeta['total_tokens'] ?? ($promptTokens + $completionTokens));

            if (is_string($reply) && trim($reply) !== '') {
                return [
                    'success' => true,
                    'reply' => trim($reply),
                    'latency_ms' => $latencyMs,
                    'usage' => [
                        'prompt_tokens' => $promptTokens,
                        'completion_tokens' => $completionTokens,
                        'total_tokens' => $totalTokens,
                    ],
                ];
            }
        }

        $errorMsg = $this->mapHttpError($response, $model, $providerType, $apiKey);
        Log::warning('OpenAI-Compatible API Error Response', [
            'status' => $response->status(),
            'provider' => $providerType,
            'model' => $model,
            'error' => $errorMsg,
        ]);

        return [
            'success' => false,
            'latency_ms' => $latencyMs,
            'error' => $errorMsg,
            'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
        ];
    }

    /**
     * Tentukan rantai provider eksekusi (DB aktif -> fallback -> atau konfigurasi legacy bila DB kosong).
     */
    protected function resolveExecutionChain(AiSetting $setting): array
    {
        try {
            $totalDbProviders = AiProvider::count();
        } catch (\Throwable $e) {
            $totalDbProviders = 0;
        }

        $chain = [];

        if ($totalDbProviders > 0) {
            $active = AiProvider::where('is_active', true)->first();
            if ($active) {
                $chain[] = $this->providerToCandidate($active);
            }

            if ($setting->auto_fallback) {
                $fallbacks = AiProvider::where('is_fallback', true)
                    ->when($active, fn ($q) => $q->where('id', '!=', $active->id))
                    ->orderBy('id')
                    ->get();

                foreach ($fallbacks as $fb) {
                    $chain[] = $this->providerToCandidate($fb);
                }
            }

            return array_values(array_filter($chain, fn ($c) => $c !== null));
        }

        // Backward Compatibility: jika belum ada profil di database, baca config('services.gemini') atau AI/apikey.txt
        $legacyKey = $this->resolveLegacyGeminiApiKey();
        if (empty($legacyKey)) {
            return [];
        }

        $legacyModel = $this->resolveLegacyGeminiModel();

        return [[
            'provider_record' => null,
            'provider_name' => 'Google Gemini (Konfigurasi Bawaan)',
            'provider_type' => 'gemini',
            'protocol' => 'gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'api_key' => $legacyKey,
            'model' => $legacyModel,
        ]];
    }

    protected function providerToCandidate(AiProvider $provider): ?array
    {
        $presets = $this->getProviderPresets();
        $requiresKey = $presets[$provider->provider_type]['requires_api_key'] ?? false;
        $decryptedKey = $provider->resolveDecryptedApiKey();

        if ($requiresKey && empty($decryptedKey)) {
            return null;
        }

        return [
            'provider_record' => $provider,
            'provider_name' => $provider->nama,
            'provider_type' => $provider->provider_type,
            'protocol' => $provider->protocol,
            'base_url' => $provider->base_url ?: ($presets[$provider->provider_type]['base_url'] ?? ''),
            'api_key' => $decryptedKey,
            'model' => $provider->model,
        ];
    }

    protected function resolveCurrentSetting(): AiSetting
    {
        try {
            return AiSetting::current();
        } catch (\Throwable $e) {
            $defaults = AiSetting::defaultDefaults();
            return new AiSetting(array_merge([
                'is_enabled' => true,
                'auto_fallback' => true,
            ], $defaults));
        }
    }

    /**
     * Dapatkan informasi status konfigurasi bawaan (untuk info di halaman Manajemen AI tanpa membocorkan key).
     */
    public function hasLegacyGeminiKey(): bool
    {
        return !empty($this->resolveLegacyGeminiApiKey());
    }

    public function resolveLegacyGeminiApiKey(): ?string
    {
        $key = config('services.gemini.api_key');
        if (!empty($key)) {
            return trim((string) $key);
        }

        $filePath = base_path('AI/apikey.txt');
        if (file_exists($filePath)) {
            $fileKey = trim((string) file_get_contents($filePath));
            if (!empty($fileKey)) {
                return $fileKey;
            }
        }

        return null;
    }

    public function resolveLegacyGeminiModel(): string
    {
        $filePath = base_path('AI/rule.json');
        if (file_exists($filePath)) {
            $parsed = json_decode((string) file_get_contents($filePath), true);
            if (is_array($parsed) && !empty($parsed['model'])) {
                return (string) $parsed['model'];
            }
        }

        return (string) config('services.gemini.model', 'gemini-3.1-flash-lite');
    }

    protected function recordExecutionMetrics(
        ?AiProvider $provider,
        string $providerType,
        string $model,
        array $result,
        string $triggerSource
    ): void {
        $usage = $result['usage'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0];
        $latencyMs = $result['latency_ms'] ?? null;
        $isSuccess = (bool) ($result['success'] ?? false);

        try {
            if ($provider) {
                $provider->increment('total_requests');
                if ($usage['prompt_tokens'] > 0) {
                    $provider->increment('total_prompt_tokens', (int) $usage['prompt_tokens']);
                }
                if ($usage['completion_tokens'] > 0) {
                    $provider->increment('total_completion_tokens', (int) $usage['completion_tokens']);
                }

                $provider->update([
                    'status_koneksi' => $isSuccess ? 'connected' : 'error',
                    'last_latency_ms' => $latencyMs,
                    'last_error' => $isSuccess ? null : mb_substr((string) ($result['error'] ?? 'Unknown error'), 0, 480),
                    'last_tested_at' => now(),
                ]);
            }

            AiUsageLog::create([
                'ai_provider_id' => $provider?->id,
                'provider_type' => $providerType,
                'model' => $model,
                'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
                'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
                'total_tokens' => (int) ($usage['total_tokens'] ?? 0),
                'latency_ms' => $latencyMs,
                'status' => $isSuccess ? 'success' : 'error',
                'error_summary' => $isSuccess ? null : mb_substr((string) ($result['error'] ?? 'Unknown error'), 0, 480),
                'trigger_source' => $triggerSource,
            ]);
        } catch (\Throwable $e) {
            Log::debug('Failed to record AI metrics: ' . $e->getMessage());
        }
    }

    /**
     * Susun System Instruction ringkas berbasis pengaturan Manajemen AI agar konsumsi token minimum.
     */
    public function buildSystemInstruction(?User $user = null, ?AiSetting $setting = null): string
    {
        $setting = $setting ?? $this->resolveCurrentSetting();

        $userInfo = $user
            ? "Pengguna terautentikasi: {$user->name} ({$user->nip}) pada unit kerja " . ($user->ruangan->nama_ruangan ?? 'BPS Kabupaten Cirebon') . '.'
            : 'Pengguna publik/tamu di portal BPS Kabupaten Cirebon.';

        $identity = $setting->assistant_name ?: 'Asisten SIPABIS AI';
        $tone = $setting->assistant_tone ?: 'Lugas, to the point, hemat token.';

        $rulesList = '';
        if (is_array($setting->rules_json)) {
            foreach (array_values($setting->rules_json) as $idx => $rule) {
                $ruleText = trim((string) $rule);
                if ($ruleText !== '') {
                    $num = $idx + 1;
                    $rulesList .= "{$num}. {$ruleText}\n";
                }
            }
        }

        $securityList = '';
        if (is_array($setting->security_json)) {
            if (!empty($setting->security_json['prompt_injection_prevention'])) {
                $securityList .= '- Keamanan Prompt: ' . implode(' ', (array) $setting->security_json['prompt_injection_prevention']) . "\n";
            }
            if (!empty($setting->security_json['response_filter'])) {
                $securityList .= '- Filter Konten: ' . implode(' ', (array) $setting->security_json['response_filter']) . "\n";
            }
        }

        $securityBlock = $securityList !== '' ? "\nPANDUAN KEAMANAN & INTEGRITAS:\n{$securityList}" : '';

        return <<<TEXT
Identitas: {$identity} (Satker 428646).
{$userInfo}
Gaya Bahasa: {$tone}

ATURAN PERILAKU & RESPONS:
{$rulesList}{$securityBlock}
TEXT;
    }

    /**
     * Ekstraksi konteks stok riil dari database berdasarkan kata kunci pesan pengguna.
     */
    public function extractRelevantContext(string $message): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', mb_strtolower($message));
        $words = array_filter(explode(' ', (string) $clean), function ($word) {
            $stopWords = [
                'apa', 'apakah', 'ada', 'stok', 'sisa', 'berapa', 'tolong', 'cek', 'lihat',
                'barang', 'persediaan', 'di', 'ke', 'dari', 'yang', 'untuk', 'pada', 'dan',
                'saya', 'kami', 'mohon', 'bisa', 'kah', 'gudang', 'kantor', 'bps', 'cirebon',
                'ya', 'halo', 'hai', 'terima', 'kasih', 'bagaimana', 'cara', 'aturan', 'masih',
            ];
            return mb_strlen($word) >= 2 && !in_array($word, $stopWords, true);
        });

        if (empty($words)) {
            return '';
        }

        $query = BarangPersediaan::with('kategori');
        $query->where(function ($q) use ($words) {
            foreach ($words as $w) {
                $q->orWhere('nama_barang', 'LIKE', "%{$w}%")
                    ->orWhere('kode_barang', 'LIKE', "%{$w}%")
                    ->orWhere('kode_sakti', 'LIKE', "%{$w}%");
            }
        });

        $items = $query->orderBy('stok_aktif', 'desc')->limit(8)->get();

        if ($items->isEmpty()) {
            return '';
        }

        $lines = [];
        foreach ($items as $item) {
            $rak = $item->lokasi_rak ? " (Lokasi: {$item->lokasi_rak})" : '';
            $lines[] = "• {$item->nama_barang}: {$item->stok_aktif} {$item->satuan}{$rak}";
        }

        return implode("\n", $lines);
    }

    protected function normalizeGeminiBaseUrl(?string $baseUrl): string
    {
        $url = rtrim(trim((string) $baseUrl), '/');
        if ($url === '') {
            return 'https://generativelanguage.googleapis.com/v1beta';
        }
        return preg_replace('/\/models(\/.*)?$/', '', $url) ?: $url;
    }

    protected function buildGeminiGenerateEndpoint(?string $baseUrl, string $model): string
    {
        $raw = rtrim(trim((string) $baseUrl), '/');
        if ($raw !== '' && str_contains($raw, ':generateContent')) {
            return $raw;
        }
        $normalizedBase = $this->normalizeGeminiBaseUrl($baseUrl);
        $cleanModel = preg_replace('/^models\//', '', trim($model));

        return "{$normalizedBase}/models/{$cleanModel}:generateContent";
    }

    protected function buildOpenAiChatEndpoint(string $baseUrl): string
    {
        $url = rtrim(trim($baseUrl), '/');
        if (str_ends_with($url, '/chat/completions')) {
            return $url;
        }
        return "{$url}/chat/completions";
    }

    protected function buildOpenAiModelsEndpoint(string $baseUrl): string
    {
        $url = rtrim(trim($baseUrl), '/');
        if (str_ends_with($url, '/chat/completions')) {
            $url = substr($url, 0, -strlen('/chat/completions'));
        }
        if (str_ends_with($url, '/models')) {
            return $url;
        }
        return "{$url}/models";
    }

    protected function mapHttpError(Response $response, string $model, string $providerType, ?string $apiKey): string
    {
        $status = $response->status();
        $rawDetail = $response->json('error.message')
            ?? $response->json('message')
            ?? '';
        $detail = $this->sanitizeErrorMessage((string) $rawDetail, $apiKey);
        $shortDetail = $detail !== '' ? ' (' . mb_substr($detail, 0, 180) . ')' : '';

        return match (true) {
            $status === 401 || $status === 403 => "Autentikasi ditolak (HTTP {$status}): API Key tidak valid atau belum memiliki akses.{$shortDetail}",
            $status === 402 => "Saldo/kuota kredit tidak mencukupi (HTTP 402). Pilih model bertanda Gratis ($0) atau ganti API Key.{$shortDetail}",
            $status === 404 => "Model '{$model}' atau endpoint {$providerType} tidak ditemukan (HTTP 404). Coba klik 'Cek Model Live'.{$shortDetail}",
            $status === 429 => "Batas permintaan/kuota harian tercapai (HTTP 429). Gunakan API Key cadangan atau aktifkan Auto-Fallback.{$shortDetail}",
            $status >= 500 => "Server penyedia AI ({$providerType}) sedang gangguan sementara (HTTP {$status}).{$shortDetail}",
            default => "Permintaan gagal (HTTP {$status}).{$shortDetail}",
        };
    }

    /**
     * Hapus jejak API key agar tidak pernah muncul pada pesan error UI maupun file log.
     */
    protected function sanitizeErrorMessage(string $message, ?string $apiKey = null): string
    {
        $cleaned = preg_replace('/([?&]key=)[^&\s]+/i', '$1[REDACTED]', $message) ?? $message;
        if (!empty($apiKey) && strlen($apiKey) >= 6) {
            $cleaned = str_replace($apiKey, '[REDACTED_KEY]', $cleaned);
        }
        return trim($cleaned);
    }
}
