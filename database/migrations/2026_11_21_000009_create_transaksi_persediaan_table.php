<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang_persediaan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_transaksi', ['masuk', 'keluar', 'penyesuaian']);
            $table->integer('jumlah'); // positif untuk masuk/tambah, negatif untuk keluar/kurang
            $table->integer('saldo_sebelum');
            $table->integer('saldo_setelah');
            $table->date('tanggal_transaksi');
            $table->string('nomor_dokumen', 100)->nullable(); // No Surat Jalan/Kwitansi/No Permintaan/No BASO
            $table->string('sumber_atau_tujuan', 150)->nullable(); // Vendor atau Nama Ruangan penerima
            $table->enum('status_spj', ['selesai', 'tertunda'])->default('selesai');
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaan')->nullOnDelete();
            $table->foreignId('stok_opname_id')->nullable()->constrained('stok_opname')->nullOnDelete();
            $table->timestamps();

            $table->index(['barang_id', 'tanggal_transaksi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_persediaan');
    }
};
