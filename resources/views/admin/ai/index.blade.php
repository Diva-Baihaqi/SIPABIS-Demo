<x-app-layout>
    <x-slot:title>Manajemen AI - SIPABIS</x-slot:title>
    <x-slot:header>Manajemen AI</x-slot:header>

    <div class="space-y-6" x-data="aiManagementPanel()">

        <!-- Top Header Banner & Global Status Toggle -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-brand-lime shadow-sm shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Manajemen & Konfigurasi AI</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Mode Gratis ($0) & Hemat Token
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Kelola API Key dari berbagai penyedia AI (Gemini, Groq, OpenRouter, Ollama/Custom), uji koneksi 1-klik, atur cadangan otomatis (Auto-Fallback), serta kendalikan konsumsi token tanpa mengedit kode.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @if(!$setting->is_enabled)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Asisten Nonaktif</span>
                    </span>
                @elseif($activeProvider && $activeProvider->status_koneksi === 'connected')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Terhubung • {{ $activeProvider->nama }}</span>
                    </span>
                @elseif($activeProvider)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>{{ $activeProvider->nama }} (Perlu Uji)</span>
                    </span>
                @elseif($hasLegacyKey)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>Aktif via Kunci Bawaan</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Belum Dikonfigurasi</span>
                    </span>
                @endif

                <form method="POST" action="{{ route('admin.ai.toggle-enabled') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer {{ $setting->is_enabled ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-brand-lime hover:bg-brand-lime-hover text-slate-950' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/></svg>
                        <span>{{ $setting->is_enabled ? 'Matikan Asisten AI' : 'Aktifkan Asisten AI' }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-sm">
                <p class="font-bold mb-1">Periksa Input Konfigurasi:</p>
                <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- KPI Ringkasan AI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Model & Engine Aktif</span>
                <div class="mt-2">
                    <p class="text-sm font-black text-slate-900 truncate">
                        {{ $activeProvider?->model ?? ($hasLegacyKey ? $legacyModel : 'Belum disetel') }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                        {{ $activeProvider ? strtoupper($activeProvider->provider_type) . ' • ' . $activeProvider->nama : ($hasLegacyKey ? 'Fallback AI/apikey.txt' : 'Silakan tempel API key di bawah') }}
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600">Kecepatan Rata-rata</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-teal-700">
                        {{ $stats['avg_latency_ms'] > 0 ? number_format($stats['avg_latency_ms']) : ($activeProvider?->last_latency_ms ?? 0) }}
                        <span class="text-xs font-bold text-slate-400">ms</span>
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                        {{ $setting->disable_thinking ? 'Instant Mode' : 'Standard' }}
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Total Konsumsi Token</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-indigo-700">{{ number_format($stats['total_tokens']) }}</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Maks {{ $setting->max_output_tokens }} / balas
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Aktivitas Hari Ini</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['requests_today']) }} <span class="text-xs font-bold text-slate-400">req</span></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $stats['errors_today'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        {{ $stats['errors_today'] }} Error
                    </span>
                </div>
            </div>
        </div>

        <!-- PANEL 1: KONEKSI CEPAT (PASTE API KEY & KLIK KONEK) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-900 via-bps-navy-900 to-slate-900 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-brand-lime text-slate-950 text-[10px] font-black uppercase tracking-wider">Quick Setup</span>
                        <h2 class="text-base font-extrabold tracking-tight">Koneksi Cepat AI (Paste API Key &amp; Konek)</h2>
                    </div>
                    <p class="text-xs text-slate-300 mt-1">
                        Pilih penyedia gratis ($0) atau tempel API Key dari mana saja — sistem otomatis mendeteksi provider, menguji koneksi riil, lalu mengaktifkannya.
                    </p>
                </div>
                <div class="text-[11px] text-slate-300 bg-white/10 border border-white/15 px-3 py-1.5 rounded-xl shrink-0">
                    🔒 API Key disimpan terenkripsi (AES-256)
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Pilihan Preset Provider (Fokus Free $0 di Depan) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        1. Pilih Penyedia AI (Rekomendasi Gratis $0 &amp; Tercepat)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <template x-for="key in ['gemini', 'groq', 'openrouter', 'custom']" :key="key">
                            <button type="button"
                                    @click="selectPreset(key)"
                                    :class="form.provider_type === key
                                        ? 'border-teal-600 bg-teal-50/70 ring-2 ring-teal-500/30 shadow-sm'
                                        : 'border-slate-200 bg-slate-50/60 hover:bg-white hover:border-slate-300'"
                                    class="text-left p-3.5 rounded-2xl border transition cursor-pointer flex flex-col justify-between gap-2">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-extrabold text-xs text-slate-900" x-text="presets[key].label"></span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                          :class="form.provider_type === key ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-300 bg-white'">
                                        <svg x-show="form.provider_type === key" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                <span class="inline-flex w-fit items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100/80 text-emerald-800"
                                      x-text="presets[key].badge"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Pilihan Provider Tambahan -->
                    <div class="mt-2.5 flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="text-slate-500 font-semibold">Penyedia Lainnya:</span>
                        <template x-for="extraKey in ['mistral', 'deepseek', 'openai']" :key="extraKey">
                            <button type="button"
                                    @click="selectPreset(extraKey)"
                                    :class="form.provider_type === extraKey
                                        ? 'bg-slate-900 text-white border-slate-900'
                                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-2.5 py-1 rounded-lg border font-bold transition cursor-pointer"
                                    x-text="presets[extraKey].label"></button>
                        </template>
                    </div>

                    <p class="mt-2 text-[11px] text-slate-500 bg-slate-50 border border-slate-200/70 rounded-xl px-3 py-2"
                       x-text="presets[form.provider_type]?.description"></p>
                </div>

                <!-- Form Input Paste API Key + Model Gratis + Base URL -->
                <form @submit.prevent="submitQuickConnect(false)" class="space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <!-- Input Paste API Key -->
                        <div class="lg:col-span-6 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    2. Tempel (Paste) API Key
                                </label>
                                <span x-show="detectedProviderLabel"
                                      x-text="'✨ Terdeteksi: ' + detectedProviderLabel"
                                      class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200"
                                      style="display: none;"></span>
                            </div>
                            <div class="relative">
                                <input :type="showRawKey ? 'text' : 'password'"
                                       x-model="form.api_key"
                                       @input="onApiKeyChanged()"
                                       @paste="setTimeout(() => onApiKeyChanged(), 10)"
                                       :placeholder="'Tempel API Key di sini (' + (presets[form.provider_type]?.key_prefix_hint || 'API Key') + ')'"
                                       autocomplete="new-password"
                                       class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                                <button type="button"
                                        @click="showRawKey = !showRawKey"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 cursor-pointer"
                                        title="Tampilkan / Sembunyikan API Key">
                                    <svg x-show="!showRawKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showRawKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Nama Label Profil -->
                        <div class="lg:col-span-3 space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Label Koneksi (Opsional)</label>
                            <input type="text"
                                   x-model="form.nama"
                                   placeholder="Misal: Gemini Free Utama"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                        </div>

                        <!-- Protokol API -->
                        <div class="lg:col-span-3 space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Protokol API</label>
                            <select x-model="form.protocol"
                                    class="w-full px-3 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-teal-500 focus:outline-none transition">
                                <option value="gemini">Google Gemini REST</option>
                                <option value="openai_compatible">OpenAI-Compatible (Umum)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pemilihan Model Khusus Gratis & Tercepat -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                        <div class="lg:col-span-6 space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    3. Model Gratis ($0), Tercepat &amp; Hemat Token
                                </label>
                                <button type="button"
                                        @click="fetchLiveModels()"
                                        :disabled="isFetchingModels"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[11px] font-bold transition cursor-pointer disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" :class="isFetchingModels ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span x-text="isFetchingModels ? 'Memuat...' : 'Cek Model Gratis Live'"></span>
                                </button>
                            </div>

                            <input type="text"
                                   x-model="form.model"
                                   required
                                   placeholder="Contoh: gemini-3.1-flash-lite atau openrouter/free"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-teal-500 focus:outline-none transition">

                            <!-- Rekomendasi Preset Model Gratis -->
                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                <template x-for="m in activeRecommendedModels" :key="m.id">
                                    <button type="button"
                                            @click="form.model = m.id"
                                            :title="m.note"
                                            :class="form.model === m.id
                                                ? 'bg-emerald-600 text-white border-emerald-600'
                                                : 'bg-emerald-50/70 text-emerald-800 border-emerald-200 hover:bg-emerald-100'"
                                            class="px-2.5 py-1 rounded-lg border text-[11px] font-bold transition cursor-pointer flex items-center gap-1">
                                        <span x-text="m.id"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Hasil Live Fetch Model dari Provider -->
                            <div x-show="liveModelFeedback"
                                 class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2"
                                 style="display: none;">
                                <p class="text-[11px] font-semibold"
                                   :class="liveModels.length > 0 ? 'text-teal-700' : 'text-rose-600'"
                                   x-text="liveModelFeedback"></p>
                                <div x-show="liveModels.length > 0" class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                                    <template x-for="lm in liveModels" :key="'live-' + lm.id">
                                        <button type="button"
                                                @click="form.model = lm.id"
                                                :class="form.model === lm.id
                                                    ? 'bg-slate-900 text-white border-slate-900'
                                                    : 'bg-white text-slate-700 border-slate-200 hover:border-teal-400'"
                                                class="px-2 py-1 rounded-md border text-[10px] font-mono font-semibold transition cursor-pointer"
                                                :title="lm.note"
                                                x-text="lm.id"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Base URL Endpoint -->
                        <div class="lg:col-span-6 space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Base URL Endpoint (Bisa Custom Gateway Mana Saja)
                            </label>
                            <input type="url"
                                   x-model="form.base_url"
                                   placeholder="https://generativelanguage.googleapis.com/v1beta"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:outline-none transition">

                            <label class="inline-flex items-center gap-2 pt-1 cursor-pointer select-none">
                                <input type="checkbox"
                                       x-model="form.is_fallback"
                                       class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                <span class="text-xs text-slate-600 font-medium">Jadikan juga profil cadangan (Auto-Fallback)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Feedback Koneksi Real-time -->
                    <div x-show="connectResult"
                         :class="connectResult?.success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'"
                         class="p-4 rounded-2xl border text-xs space-y-2"
                         style="display: none;">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-bold" x-text="connectResult?.message"></p>
                            <button type="button"
                                    x-show="!connectResult?.success"
                                    @click="submitQuickConnect(true)"
                                    class="px-3 py-1 rounded-lg bg-rose-700 hover:bg-rose-800 text-white text-[11px] font-bold shrink-0 cursor-pointer">
                                Tetap Simpan Profil Ini
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Konek & Aktifkan -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                        <p class="text-[11px] text-slate-500">
                            Tombol di kanan akan langsung menguji koneksi ke server AI sebelum menggantikan AI aktif saat ini.
                        </p>
                        <button type="submit"
                                :disabled="isConnecting"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-black text-xs shadow-sm hover:shadow transition transform hover:scale-[1.01] active:scale-[0.99] cursor-pointer disabled:opacity-60">
                            <svg x-show="isConnecting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <svg x-show="!isConnecting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span x-text="isConnecting ? 'Menguji & Menghubungkan AI...' : 'Konek & Aktifkan Sekarang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PANEL 2: DAFTAR PROFIL KONEKSI & CADANGAN MULTI-PROVIDER -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Daftar Koneksi AI Tersimpan &amp; Cadangan (Auto-Fallback)</h2>
                    <p class="text-[11px] text-slate-500">Beralih antar API Key atau penyedia kapan saja dalam 1 klik jika penyedia utama sedang limit atau gangguan.</p>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 w-fit">
                    {{ $providers->count() }} Profil Tersimpan
                </span>
            </div>

            @if($providers->isEmpty())
                <div class="p-6 text-center space-y-2">
                    <p class="text-xs font-bold text-slate-700">
                        Belum ada profil AI yang disimpan di database.
                    </p>
                    <p class="text-[11px] text-slate-500">
                        @if($hasLegacyKey)
                            Saat ini sistem menggunakan konfigurasi bawaan (<code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded">AI/apikey.txt</code> / <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded">.env</code> • Model: <span class="font-semibold">{{ $legacyModel }}</span>). Anda dapat menggantinya kapan saja melalui panel Koneksi Cepat di atas.
                        @else
                            Gunakan panel <strong>Koneksi Cepat AI</strong> di atas untuk menempel API Key dan menghubungkan layanan AI gratis.
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                                <th class="py-3 px-5">Profil &amp; Penyedia</th>
                                <th class="py-3 px-4">Model &amp; API Key</th>
                                <th class="py-3 px-4">Status &amp; Latensi</th>
                                <th class="py-3 px-4 text-center">Pemakaian</th>
                                <th class="py-3 px-5 text-right">Aksi 1-Klik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($providers as $prov)
                                <tr class="{{ $prov->is_active ? 'bg-emerald-50/40' : 'hover:bg-slate-50/70' }} transition">
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-slate-900">{{ $prov->nama }}</span>
                                            @if($prov->is_active)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-600 text-white">Utama Aktif</span>
                                            @endif
                                            @if($prov->is_fallback)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-indigo-100 text-indigo-800">Cadangan</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            {{ strtoupper($prov->provider_type) }} • <span class="font-mono">{{ $prov->protocol }}</span>
                                        </p>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="font-mono font-bold text-slate-800 text-[11px]">{{ $prov->model }}</p>
                                        <p class="font-mono text-[10px] text-slate-400 mt-0.5">{{ $prov->masked_api_key }}</p>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($prov->status_koneksi === 'connected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">
                                                ● Terhubung {{ $prov->last_latency_ms ? "({$prov->last_latency_ms} ms)" : '' }}
                                            </span>
                                        @elseif($prov->status_koneksi === 'error')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px]" title="{{ $prov->last_error }}">
                                                ● Gangguan
                                            </span>
                                            @if($prov->last_error)
                                                <p class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $prov->last_error }}">{{ $prov->last_error }}</p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[10px]">
                                                ○ Belum Diuji
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <p class="font-bold text-slate-800">{{ number_format($prov->total_requests) }} req</p>
                                        <p class="text-[10px] text-slate-500">{{ number_format($prov->total_tokens) }} tok</p>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            @if(!$prov->is_active)
                                                <form method="POST" action="{{ route('admin.ai.providers.activate', $prov) }}">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition cursor-pointer">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('admin.ai.providers.test', $prov) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition cursor-pointer">
                                                    Uji
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.ai.providers.toggle-fallback', $prov) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border font-bold text-[11px] transition cursor-pointer {{ $prov->is_fallback ? 'bg-indigo-50 border-indigo-200 text-indigo-700 hover:bg-indigo-100' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                                                    {{ $prov->is_fallback ? 'Cabut Cadangan' : 'Jadi Cadangan' }}
                                                </button>
                                            </form>

                                            <form method="POST"
                                                  action="{{ route('admin.ai.providers.destroy', $prov) }}"
                                                  data-confirm="Hapus profil koneksi AI '{{ $prov->nama }}'?"
                                                  data-confirm-title="Hapus Profil AI"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer" title="Hapus Profil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- PANEL 3 & 4: PENGATURAN HEMAT TOKEN + SANDBOX CHAT & LOG -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <!-- Kiri: Konfigurasi Hemat Token & Aturan Perilaku -->
            <div class="xl:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Pengaturan Hemat Token &amp; Aturan Perilaku AI</h2>
                        <p class="text-[11px] text-slate-500">Kontrol batas token, mode respons instan, auto-fallback, dan instruksi sistem BPS.</p>
                    </div>
                    <form method="POST"
                          action="{{ route('admin.ai.settings.update') }}"
                          data-confirm="Kembalikan pengaturan efisiensi token dan aturan perilaku AI ke standar resmi BPS?"
                          data-confirm-title="Reset Standar BPS"
                          data-confirm-type="warning"
                          data-confirm-btn="Ya, Reset">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="reset_defaults" value="1">
                        <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition cursor-pointer">
                            Reset Default BPS
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.ai.settings.update') }}" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                            <input type="checkbox" name="disable_thinking" value="1" {{ $setting->disable_thinking ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Matikan Thinking Tokens</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Konsumsi token paling rendah &amp; respons paling cepat (thinkingBudget = 0).</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                            <input type="checkbox" name="auto_fallback" value="1" {{ $setting->auto_fallback ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Auto-Fallback Cadangan</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Otomatis pindah ke provider cadangan jika AI utama limit (429) / mati.</span>
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Maks Output Token</label>
                            <input type="number" name="max_output_tokens" min="50" max="2048"
                                   value="{{ old('max_output_tokens', $setting->max_output_tokens) }}"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500 focus:bg-white">
                            <p class="text-[10px] text-slate-400">Rekomendasi hemat: 150 – 300</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Temperature (0.0 – 1.0)</label>
                            <input type="number" step="0.05" min="0" max="1" name="temperature"
                                   value="{{ old('temperature', $setting->temperature) }}"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500 focus:bg-white">
                            <p class="text-[10px] text-slate-400">Rekomendasi akurat: 0.1</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Timeout (Detik)</label>
                            <input type="number" min="5" max="60" name="timeout_seconds"
                                   value="{{ old('timeout_seconds', $setting->timeout_seconds) }}"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500 focus:bg-white">
                            <p class="text-[10px] text-slate-400">Standar: 15 detik</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Identitas Asisten</label>
                            <input type="text" name="assistant_name"
                                   value="{{ old('assistant_name', $setting->assistant_name) }}"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Gaya Bahasa (Tone)</label>
                            <input type="text" name="assistant_tone"
                                   value="{{ old('assistant_tone', $setting->assistant_tone) }}"
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Daftar Aturan Perilaku AI (1 Aturan per Baris)</label>
                        <textarea name="rules_text" rows="6"
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs leading-relaxed focus:ring-2 focus:ring-teal-500 focus:bg-white">{{ old('rules_text', implode("\n", $setting->rules_json ?? [])) }}</textarea>
                        <p class="text-[10px] text-slate-400">Aturan singkat menghasilkan instruksi sistem yang lebih hemat token setiap kali pegawai bertanya.</p>
                    </div>

                    <div class="flex justify-end pt-2 border-t border-slate-100">
                        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition cursor-pointer">
                            Simpan Konfigurasi AI
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kanan: Live Mini Sandbox Chat + Log Pemakaian Terakhir -->
            <div class="xl:col-span-5 space-y-6">
                <!-- Sandbox Chat -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Live Sandbox Uji Coba AI</h2>
                            <p class="text-[11px] text-slate-500">Tes respons, latensi &amp; token secara langsung sebelum dipakai pegawai.</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">Admin Lab</span>
                    </div>

                    <div class="p-5 space-y-3">
                        <form @submit.prevent="sendSandbox()" class="flex items-center gap-2">
                            <input type="text"
                                   x-model="sandboxMessage"
                                   placeholder="Misal: Cek stok kertas HVS A4..."
                                   class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white focus:outline-none">
                            <button type="submit"
                                    :disabled="isSandboxLoading || !sandboxMessage.trim()"
                                    class="px-4 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs transition cursor-pointer disabled:opacity-50 shrink-0">
                                <span x-text="isSandboxLoading ? 'Menguji...' : 'Uji Kirim'"></span>
                            </button>
                        </form>

                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="sandboxMessage = 'Halo, siapa kamu?'; sendSandbox()" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold cursor-pointer">Halo, siapa kamu?</button>
                            <button type="button" @click="sandboxMessage = 'Apakah ada stok kertas HVS?'; sendSandbox()" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold cursor-pointer">Cek stok kertas HVS</button>
                        </div>

                        <div x-show="sandboxResult"
                             :class="sandboxResult?.success ? 'bg-slate-50 border-slate-200' : 'bg-rose-50 border-rose-200'"
                             class="p-4 rounded-2xl border space-y-2 text-xs"
                             style="display: none;">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[10px] font-bold">
                                <span :class="sandboxResult?.success ? 'text-emerald-700' : 'text-rose-700'"
                                      x-text="sandboxResult?.success ? ('✓ ' + (sandboxResult?.model || 'AI')) : '✗ Gagal Terhubung'"></span>
                                <span x-show="sandboxResult?.latency_ms"
                                      class="text-slate-500 font-mono"
                                      x-text="(sandboxResult?.latency_ms || 0) + ' ms • ' + ((sandboxResult?.usage?.total_tokens) || 0) + ' tok'"></span>
                            </div>
                            <p class="text-slate-800 whitespace-pre-line leading-relaxed" x-text="sandboxResult?.reply"></p>
                        </div>
                    </div>
                </div>

                <!-- Log Aktivitas & Diagnosa Error Terakhir -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-slate-900">Log Aktivitas &amp; Diagnosa Terakhir</h2>
                        <span class="text-[10px] font-semibold text-slate-400">12 Terkini</span>
                    </div>

                    @if($logs->isEmpty())
                        <div class="p-6 text-center text-xs text-slate-400">
                            Belum ada log pemakaian AI tercatat.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @foreach($logs as $log)
                                <div class="px-5 py-3 text-[11px] flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $log->status === 'success' ? 'OK' : 'ERR' }}
                                            </span>
                                            <span class="font-mono font-bold text-slate-800 truncate">{{ $log->model }}</span>
                                            <span class="text-[10px] text-slate-400">({{ $log->trigger_source }})</span>
                                        </div>
                                        @if($log->error_summary)
                                            <p class="text-[10px] text-rose-600 mt-1 break-words">{{ $log->error_summary }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0 font-mono text-[10px] text-slate-500">
                                        <div>{{ $log->latency_ms ? $log->latency_ms . ' ms' : '-' }} • {{ $log->total_tokens }} tok</div>
                                        <div class="text-slate-400">{{ $log->created_at?->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function aiManagementPanel() {
            const presets = @json($presets);

            return {
                presets,
                showRawKey: false,
                detectedProviderLabel: '',
                isConnecting: false,
                isFetchingModels: false,
                liveModels: [],
                liveModelFeedback: '',
                connectResult: null,
                sandboxMessage: '',
                isSandboxLoading: false,
                sandboxResult: null,

                form: {
                    nama: '',
                    provider_type: 'gemini',
                    protocol: presets.gemini.protocol,
                    base_url: presets.gemini.base_url,
                    api_key: '',
                    model: presets.gemini.default_model,
                    is_fallback: false,
                },

                get activeRecommendedModels() {
                    return (this.presets[this.form.provider_type] && this.presets[this.form.provider_type].recommended_models) || [];
                },

                selectPreset(key) {
                    const p = this.presets[key];
                    if (!p) return;
                    this.form.provider_type = key;
                    this.form.protocol = p.protocol;
                    this.form.base_url = p.base_url;
                    this.form.model = p.default_model;
                    this.liveModels = [];
                    this.liveModelFeedback = '';
                    this.connectResult = null;
                },

                onApiKeyChanged() {
                    const val = (this.form.api_key || '').trim();
                    let detected = null;

                    if (val.startsWith('AIza')) {
                        detected = 'gemini';
                    } else if (val.startsWith('gsk_')) {
                        detected = 'groq';
                    } else if (val.startsWith('sk-or-')) {
                        detected = 'openrouter';
                    } else if (val.startsWith('sk-proj-')) {
                        detected = 'openai';
                    }

                    if (detected && this.presets[detected]) {
                        this.detectedProviderLabel = this.presets[detected].label;
                        if (this.form.provider_type !== detected) {
                            this.selectPreset(detected);
                        }
                    } else {
                        this.detectedProviderLabel = '';
                    }
                },

                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                },

                async fetchLiveModels() {
                    this.isFetchingModels = true;
                    this.liveModelFeedback = '';
                    this.liveModels = [];

                    try {
                        const res = await fetch('{{ route("admin.ai.fetch-models") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            body: JSON.stringify({
                                provider_type: this.form.provider_type,
                                protocol: this.form.protocol,
                                base_url: this.form.base_url,
                                api_key: this.form.api_key,
                            }),
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.liveModels = data.models || [];
                            this.liveModelFeedback = data.message || 'Model berhasil dimuat.';
                        } else {
                            this.liveModelFeedback = data.message || 'Gagal mengambil daftar model live.';
                        }
                    } catch (err) {
                        this.liveModelFeedback = 'Gagal menghubungi server untuk cek model live.';
                    } finally {
                        this.isFetchingModels = false;
                    }
                },

                async submitQuickConnect(saveEvenIfFailed = false) {
                    this.isConnecting = true;
                    this.connectResult = null;

                    try {
                        const res = await fetch('{{ route("admin.ai.quick-connect") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            body: JSON.stringify({
                                ...this.form,
                                save_even_if_failed: saveEvenIfFailed,
                            }),
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.connectResult = {
                                success: true,
                                message: data.message,
                            };
                            setTimeout(() => window.location.reload(), 900);
                        } else {
                            this.connectResult = {
                                success: false,
                                message: data.message || 'Koneksi ke server AI gagal. Periksa kembali API key dan model.',
                            };
                        }
                    } catch (err) {
                        this.connectResult = {
                            success: false,
                            message: 'Terjadi kesalahan jaringan saat menghubungi server.',
                        };
                    } finally {
                        this.isConnecting = false;
                    }
                },

                async sendSandbox() {
                    const text = this.sandboxMessage.trim();
                    if (!text || this.isSandboxLoading) return;

                    this.isSandboxLoading = true;
                    this.sandboxResult = null;

                    try {
                        const res = await fetch('{{ route("admin.ai.sandbox-chat") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            body: JSON.stringify({ message: text }),
                        });

                        const data = await res.json();
                        this.sandboxResult = {
                            success: !!(res.ok && data.success),
                            reply: data.reply || data.message || 'Tidak ada respon.',
                            model: data.model,
                            latency_ms: data.latency_ms,
                            usage: data.usage,
                        };
                    } catch (err) {
                        this.sandboxResult = {
                            success: false,
                            reply: 'Gagal mengirim permintaan uji coba.',
                        };
                    } finally {
                        this.isSandboxLoading = false;
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
