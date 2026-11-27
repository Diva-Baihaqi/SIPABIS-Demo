<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_persediaan', function (Blueprint $table) {
            $table->dropUnique('barang_persediaan_kode_barang_unique');
            $table->unique(['kode_sakti', 'kode_barang'], 'barang_persediaan_sakti_barang_unique');
        });
    }

    public function down(): void
    {
        Schema::table('barang_persediaan', function (Blueprint $table) {
            $table->dropUnique('barang_persediaan_sakti_barang_unique');
            $table->unique('kode_barang', 'barang_persediaan_kode_barang_unique');
        });
    }
};
