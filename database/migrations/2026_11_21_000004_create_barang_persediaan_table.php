<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_persediaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('kode_sakti', 50)->nullable()->index();
            $table->foreignId('kategori_id')->constrained('kategori_barang')->cascadeOnDelete();
            $table->string('nama_barang', 200);
            $table->string('satuan', 50); // Batang, Rim, Pcs, Dus, Kotak, Botol
            $table->integer('stok_minimum')->default(5);
            $table->integer('stok_aktif')->default(0);
            $table->decimal('harga_satuan_estimasi', 15, 2)->default(0);
            $table->string('lokasi_rak', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_persediaan');
    }
};
