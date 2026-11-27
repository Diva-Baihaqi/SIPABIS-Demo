<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\AiSetting;
use App\Models\AiUsageLog;
use App\Services\AiGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AiManagementController extends Controller
{
    protected AiGatewayService $gateway;

    public function __construct(AiGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Halaman utama Manajemen AI (Admin).
     */
    public function index(): View
    {
        $setting = AiSetting::current();
        $providers = AiProvider::orderByDesc('is_active')
            ->orderByDesc('is_fallback')
            ->orderByDesc('id')
            ->get();

        $activeProvider = $providers->firstWhere('is_active', true);
        $presets = $this->gateway->getProviderPresets();
        $hasLegacyKey = $this->gateway->hasLegacyGeminiKey();
        $legacyModel = $this->gateway->resolveLegacyGeminiModel();
        $logs = AiUsageLog::with('provider:id,nama,provider_type')
            ->latest('id')
            ->limit(12)
            ->get();

        $stats = [
            'requests_today' => AiUsageLog::whereDate('created_at', today())->count(),
            'total_tokens' => (int) AiUsageLog::sum('total_tokens'),
            'avg_latency_ms' => (int) round(
                AiUsageLog::where('status', 'success')->whereNotNull('latency_ms')->avg('latency_ms') ?? 0
            ),
            'errors_today' => AiUsageLog::whereDate('created_at', today())->where('status', 'error')->count(),
        ];

        return view('admin.ai.index', compact(
            'setting',
            'providers',
            'activeProvider',
            'presets',
            'hasLegacyKey',
            'legacyModel',
            'logs',
            'stats'
        ));
    }

    /**
     * Quick Connect: Paste API Key, uji koneksi riil, simpan terenkripsi, dan langsung aktifkan.
     */
    public function quickConnect(Request $request): JsonResponse|RedirectResponse
    {
        $presets = $this->gateway->getProviderPresets();

        $validated = $request->validate([
            'nama' => ['nullable', 'string', 'max:120'],
            'provider_type' => ['required', Rule::in(array_keys($presets))],
            'protocol' => ['required', Rule::in(['gemini', 'openai_compatible'])],
            'base_url' => ['nullable', 'url:http,https', 'max:500'],
            'api_key' => ['nullable', 'string', 'max:500'],
            'model' => ['required', 'string', 'max:150'],
            'is_fallback' => ['nullable', 'boolean'],
            'save_even_if_failed' => ['nullable', 'boolean'],
        ], [
            'provider_type.required' => 'Pilih penyedia AI terlebih dahulu.',
            'protocol.required' => 'Protokol API wajib dipilih.',
            'base_url.url' => 'Format Base URL harus diawali http:// atau https://.',
            'model.required' => 'Nama/ID Model AI wajib diisi.',
        ]);

        $preset = $presets[$validated['provider_type']];
        $baseUrl = !empty($validated['base_url']) ? trim($validated['base_url']) : ($preset['base_url'] ?? '');
        $apiKey = isset($validated['api_key']) ? trim($validated['api_key']) : null;
        $nama = !empty($validated['nama'])
            ? trim($validated['nama'])
            : ($preset['label'] . ' (' . now()->format('d/m H:i') . ')');

        $testResult = $this->gateway->testConnection(
            protocol: $validated['protocol'],
            baseUrl: $baseUrl,
            apiKey: $apiKey,
            model: trim($validated['model']),
            providerType: $validated['provider_type']
        );

        if (!$testResult['success'] && !$request->boolean('save_even_if_failed')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $testResult['error'] ?? 'Gagal terhubung ke penyedia AI.',
                    'latency_ms' => $testResult['latency_ms'] ?? 0,
                ], 422);
            }

            return back()->withErrors([
                'connection' => $testResult['error'] ?? 'Gagal terhubung ke penyedia AI.',
            ])->withInput($request->except('api_key'));
        }

        $provider = DB::transaction(function () use ($validated, $baseUrl, $apiKey, $nama, $testResult, $request) {
            AiProvider::where('is_active', true)->update(['is_active' => false]);

            return AiProvider::create([
                'nama' => $nama,
                'provider_type' => $validated['provider_type'],
                'protocol' => $validated['protocol'],
                'base_url' => $baseUrl,
                'api_key' => $apiKey !== '' ? $apiKey : null,
                'model' => trim($validated['model']),
                'is_active' => true,
                'is_fallback' => $request->boolean('is_fallback', false),
                'status_koneksi' => $testResult['success'] ? 'connected' : 'error',
                'last_latency_ms' => $testResult['latency_ms'] ?? null,
                'last_error' => $testResult['success'] ? null : mb_substr((string) ($testResult['error'] ?? ''), 0, 480),
                'last_tested_at' => now(),
                'total_requests' => 1,
                'total_prompt_tokens' => (int) ($testResult['usage']['prompt_tokens'] ?? 0),
                'total_completion_tokens' => (int) ($testResult['usage']['completion_tokens'] ?? 0),
            ]);
        });

        AiUsageLog::create([
            'ai_provider_id' => $provider->id,
            'provider_type' => $provider->provider_type,
            'model' => $provider->model,
            'prompt_tokens' => (int) ($testResult['usage']['prompt_tokens'] ?? 0),
            'completion_tokens' => (int) ($testResult['usage']['completion_tokens'] ?? 0),
            'total_tokens' => (int) ($testResult['usage']['total_tokens'] ?? 0),
            'latency_ms' => $testResult['latency_ms'] ?? null,
            'status' => $testResult['success'] ? 'success' : 'error',
            'error_summary' => $testResult['success'] ? null : mb_substr((string) ($testResult['error'] ?? ''), 0, 480),
            'trigger_source' => 'connection_test',
        ]);

        $msg = $testResult['success']
            ? "Berhasil terhubung & diaktifkan! ({$provider->nama} • {$provider->model} • {$testResult['latency_ms']} ms)"
            : 'Profil disimpan (belum terkoneksi: ' . ($testResult['error'] ?? 'Error') . ')';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'connected' => $testResult['success'],
                'message' => $msg,
                'latency_ms' => $testResult['latency_ms'] ?? null,
                'provider' => $provider,
            ]);
        }

        return redirect()->route('admin.ai.index')->with('success', $msg);
    }

    /**
     * Aktifkan salah satu profil provider tersimpan menjadi AI utama.
     */
    public function activateProvider(Request $request, AiProvider $provider): JsonResponse|RedirectResponse
    {
        DB::transaction(function () use ($provider) {
            AiProvider::where('id', '!=', $provider->id)->update(['is_active' => false]);
            $provider->update(['is_active' => true]);
        });

        $msg = "Koneksi aktif dialihkan ke {$provider->nama} ({$provider->model}).";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.ai.index')->with('success', $msg);
    }

    /**
     * Jadikan atau cabut status cadangan (Auto-Fallback) pada profil provider.
     */
    public function toggleFallback(Request $request, AiProvider $provider): JsonResponse|RedirectResponse
    {
        $provider->update(['is_fallback' => !$provider->is_fallback]);

        $msg = $provider->is_fallback
            ? "{$provider->nama} ditandai sebagai provider cadangan (Auto-Fallback)."
            : "{$provider->nama} dicabut dari daftar cadangan.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'is_fallback' => $provider->is_fallback, 'message' => $msg]);
        }

        return redirect()->route('admin.ai.index')->with('success', $msg);
    }

    /**
     * Uji koneksi ulang pada profil provider tersimpan.
     */
    public function testProvider(Request $request, AiProvider $provider): JsonResponse|RedirectResponse
    {
        $testResult = $this->gateway->testConnection(
            protocol: $provider->protocol,
            baseUrl: $provider->base_url,
            apiKey: $provider->resolveDecryptedApiKey(),
            model: $provider->model,
            providerType: $provider->provider_type
        );

        $provider->increment('total_requests');
        $provider->increment('total_prompt_tokens', (int) ($testResult['usage']['prompt_tokens'] ?? 0));
        $provider->increment('total_completion_tokens', (int) ($testResult['usage']['completion_tokens'] ?? 0));

        $provider->update([
            'status_koneksi' => $testResult['success'] ? 'connected' : 'error',
            'last_latency_ms' => $testResult['latency_ms'] ?? null,
            'last_error' => $testResult['success'] ? null : mb_substr((string) ($testResult['error'] ?? ''), 0, 480),
            'last_tested_at' => now(),
        ]);

        AiUsageLog::create([
            'ai_provider_id' => $provider->id,
            'provider_type' => $provider->provider_type,
            'model' => $provider->model,
            'prompt_tokens' => (int) ($testResult['usage']['prompt_tokens'] ?? 0),
            'completion_tokens' => (int) ($testResult['usage']['completion_tokens'] ?? 0),
            'total_tokens' => (int) ($testResult['usage']['total_tokens'] ?? 0),
            'latency_ms' => $testResult['latency_ms'] ?? null,
            'status' => $testResult['success'] ? 'success' : 'error',
            'error_summary' => $testResult['success'] ? null : mb_substr((string) ($testResult['error'] ?? ''), 0, 480),
            'trigger_source' => 'connection_test',
        ]);

        $msg = $testResult['success']
            ? "Uji koneksi {$provider->nama} berhasil! Latensi: {$testResult['latency_ms']} ms."
            : "Uji koneksi {$provider->nama} gagal: " . ($testResult['error'] ?? 'Error');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $testResult['success'],
                'message' => $msg,
                'latency_ms' => $testResult['latency_ms'] ?? null,
                'provider' => $provider->fresh(),
            ], $testResult['success'] ? 200 : 422);
        }

        return redirect()->route('admin.ai.index')->with(
            $testResult['success'] ? 'success' : 'error',
            $msg
        );
    }

    /**
     * Hapus profil provider tersimpan.
     */
    public function destroyProvider(Request $request, AiProvider $provider): JsonResponse|RedirectResponse
    {
        $name = $provider->nama;
        $provider->delete();

        $msg = "Profil koneksi AI '{$name}' berhasil dihapus.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.ai.index')->with('success', $msg);
    }

    /**
     * Ambil daftar model gratis/aktif secara live langsung dari endpoint provider.
     */
    public function fetchModels(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'protocol' => ['required', Rule::in(['gemini', 'openai_compatible'])],
            'base_url' => ['nullable', 'url:http,https', 'max:500'],
            'api_key' => ['nullable', 'string', 'max:500'],
            'provider_type' => ['required', 'string', 'max:40'],
            'provider_id' => ['nullable', 'integer', 'exists:ai_providers,id'],
        ]);

        $apiKey = isset($validated['api_key']) ? trim($validated['api_key']) : null;
        if (empty($apiKey) && !empty($validated['provider_id'])) {
            $existing = AiProvider::find($validated['provider_id']);
            $apiKey = $existing?->resolveDecryptedApiKey();
        }

        $result = $this->gateway->fetchAvailableModels(
            protocol: $validated['protocol'],
            baseUrl: $validated['base_url'] ?? null,
            apiKey: $apiKey,
            providerType: $validated['provider_type']
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Toggle cepat status aktif/nonaktif Asisten Virtual AI global.
     */
    public function toggleEnabled(Request $request): JsonResponse|RedirectResponse
    {
        $setting = AiSetting::current();
        $setting->update(['is_enabled' => !$setting->is_enabled]);

        $msg = $setting->is_enabled
            ? 'Asisten Virtual AI SIPABIS diaktifkan kembali untuk seluruh pengguna.'
            : 'Asisten Virtual AI SIPABIS dinonaktifkan sementara.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_enabled' => $setting->is_enabled,
                'message' => $msg,
            ]);
        }

        return redirect()->route('admin.ai.index')->with('success', $msg);
    }

    /**
     * Simpan pengaturan efisiensi token, fallback, identitas, serta aturan perilaku AI.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $setting = AiSetting::current();

        if ($request->boolean('reset_defaults')) {
            $defaults = AiSetting::defaultDefaults();
            $setting->update($defaults);

            return redirect()->route('admin.ai.index')->with(
                'success',
                'Pengaturan efisiensi token dan aturan perilaku AI dikembalikan ke standar resmi BPS.'
            );
        }

        $validated = $request->validate([
            'temperature' => ['required', 'numeric', 'min:0', 'max:1'],
            'max_output_tokens' => ['required', 'integer', 'min:50', 'max:2048'],
            'timeout_seconds' => ['required', 'integer', 'min:5', 'max:60'],
            'assistant_name' => ['required', 'string', 'max:120'],
            'assistant_tone' => ['required', 'string', 'max:500'],
            'rules_text' => ['nullable', 'string', 'max:10000'],
        ], [
            'max_output_tokens.min' => 'Batas token minimal 50 agar AI cukup menyusun jawaban stok.',
            'max_output_tokens.max' => 'Batas token maksimal 2048 agar konsumsi tetap efisien.',
        ]);

        $rulesList = $setting->rules_json ?? [];
        if (array_key_exists('rules_text', $validated)) {
            $lines = preg_split("/\r\n|\n|\r/", (string) $validated['rules_text']) ?: [];
            $rulesList = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
        }

        $setting->update([
            'auto_fallback' => $request->boolean('auto_fallback'),
            'disable_thinking' => $request->boolean('disable_thinking'),
            'temperature' => (float) $validated['temperature'],
            'max_output_tokens' => (int) $validated['max_output_tokens'],
            'timeout_seconds' => (int) $validated['timeout_seconds'],
            'assistant_name' => trim($validated['assistant_name']),
            'assistant_tone' => trim($validated['assistant_tone']),
            'rules_json' => $rulesList,
        ]);

        return redirect()->route('admin.ai.index')->with(
            'success',
            'Konfigurasi hemat token dan aturan Asisten AI berhasil disimpan.'
        );
    }

    /**
     * Live Mini Sandbox Chat untuk pengujian langsung oleh Admin beserta metrik token & latensi.
     */
    public function sandboxChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:500'],
        ], [
            'message.required' => 'Pertanyaan uji coba wajib diisi.',
        ]);

        $result = $this->gateway->chat($validated['message'], $request->user(), 'sandbox');

        return response()->json([
            'success' => (bool) ($result['success'] ?? false),
            'reply' => $result['reply'] ?? 'Tidak ada respons dari server AI.',
            'model' => $result['model'] ?? null,
            'provider_type' => $result['provider_type'] ?? null,
            'provider_name' => $result['provider_name'] ?? null,
            'latency_ms' => $result['latency_ms'] ?? null,
            'usage' => $result['usage'] ?? null,
        ], ($result['success'] ?? false) ? 200 : 422);
    }
}
