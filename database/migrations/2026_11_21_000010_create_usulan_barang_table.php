<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->string('nama_barang_usulan', 200);
            $table->string('perkiraan_satuan', 50)->nullable();
            $table->integer('estimasi_kebutuhan')->default(1);
            $table->text('alasan_kebutuhan');
            $table->enum('status', ['diajukan', 'dipertimbangkan', 'ditolak', 'direalisasikan'])->default('diajukan');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_barang');
    }
};
