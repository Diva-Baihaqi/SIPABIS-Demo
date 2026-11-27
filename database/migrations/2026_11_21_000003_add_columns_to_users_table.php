<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->unique()->after('id');
            $table->enum('role', ['admin', 'pegawai', 'pimpinan'])->default('pegawai')->after('password');
            $table->foreignId('ruangan_id')->nullable()->after('role')->constrained('ruangan')->nullOnDelete();
            $table->string('jabatan', 150)->nullable()->after('ruangan_id');
            $table->string('golongan', 50)->nullable()->after('jabatan');
            $table->string('status_pegawai', 50)->default('PNS')->after('golongan');
            $table->string('no_hp', 30)->nullable()->after('status_pegawai');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn(['nip', 'role', 'ruangan_id', 'jabatan', 'golongan', 'status_pegawai', 'no_hp']);
        });
    }
};
