<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permintaan', 80)->unique(); // REQ/BPS/YYYY/MM/XXXX
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'selesai', 'dibatalkan'])->default('diajukan');
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('tanggal_verifikasi')->nullable();
            $table->datetime('tanggal_serah_terima')->nullable();
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('file_bukti_serah_terima', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan');
    }
};
