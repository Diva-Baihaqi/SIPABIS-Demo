<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasFactory;

    protected $table = 'ai_providers';

    protected $fillable = [
        'nama',
        'provider_type',
        'protocol',
        'base_url',
        'api_key',
        'model',
        'is_active',
        'is_fallback',
        'status_koneksi',
        'last_latency_ms',
        'last_error',
        'last_tested_at',
        'total_requests',
        'total_prompt_tokens',
        'total_completion_tokens',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected $appends = [
        'masked_api_key',
        'total_tokens',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
            'is_fallback' => 'boolean',
            'last_latency_ms' => 'integer',
            'last_tested_at' => 'datetime',
            'total_requests' => 'integer',
            'total_prompt_tokens' => 'integer',
            'total_completion_tokens' => 'integer',
        ];
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class, 'ai_provider_id');
    }

    /**
     * Kembalikan pratinjau API key yang tersamar agar tidak pernah membocorkan rahasia ke UI.
     */
    public function getMaskedApiKeyAttribute(): string
    {
        try {
            $raw = $this->api_key;
        } catch (\Throwable $e) {
            return '(Kunci perlu diperbarui)';
        }

        if (empty($raw)) {
            return '(Tanpa API Key)';
        }

        $len = strlen($raw);
        if ($len <= 8) {
            return '••••' . substr($raw, -2);
        }

        return substr($raw, 0, 4) . '••••••••' . substr($raw, -4);
    }

    /**
     * Ambil nilai raw API key dengan aman dari kegagalan dekripsi.
     */
    public function resolveDecryptedApiKey(): ?string
    {
        try {
            $raw = $this->api_key;
            return is_string($raw) ? trim($raw) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getTotalTokensAttribute(): int
    {
        return (int) $this->total_prompt_tokens + (int) $this->total_completion_tokens;
    }
}
