<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_opname', 80)->unique(); // BA-SO/BPS/YYYY/MM/XXXX
            $table->date('tanggal_opname');
            $table->string('periode', 50); // e.g., 'November 2026'
            $table->enum('status', ['draft', 'final', 'disahkan'])->default('draft');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pimpinan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_kesimpulan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
