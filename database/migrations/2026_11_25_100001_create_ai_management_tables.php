<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('provider_type', 40);
            $table->string('protocol', 30)->default('openai_compatible');
            $table->string('base_url', 500)->nullable();
            $table->text('api_key')->nullable();
            $table->string('model', 150);
            $table->boolean('is_active')->default(false)->index();
            $table->boolean('is_fallback')->default(false)->index();
            $table->string('status_koneksi', 30)->default('untested');
            $table->unsignedInteger('last_latency_ms')->nullable();
            $table->string('last_error', 500)->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->unsignedBigInteger('total_requests')->default(0);
            $table->unsignedBigInteger('total_prompt_tokens')->default(0);
            $table->unsignedBigInteger('total_completion_tokens')->default(0);
            $table->timestamps();
        });

        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('auto_fallback')->default(true);
            $table->decimal('temperature', 3, 2)->default(0.10);
            $table->unsignedSmallInteger('max_output_tokens')->default(250);
            $table->boolean('disable_thinking')->default(true);
            $table->unsignedSmallInteger('timeout_seconds')->default(15);
            $table->string('assistant_name', 120)->default('Asisten SIPABIS AI');
            $table->string('assistant_tone', 500)->default('Lugas, ramah proporsional, to the point untuk data stok, hemat token, jangan bertele-tele.');
            $table->json('rules_json')->nullable();
            $table->json('security_json')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->string('provider_type', 40);
            $table->string('model', 150);
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->unsignedInteger('latency_ms')->nullable();
            $table->string('status', 20)->default('success');
            $table->string('error_summary', 500)->nullable();
            $table->string('trigger_source', 30)->default('chatbot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_settings');
        Schema::dropIfExists('ai_providers');
    }
};
