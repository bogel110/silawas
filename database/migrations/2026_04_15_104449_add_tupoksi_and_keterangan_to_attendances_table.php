<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Menambahkan 2 kolom baru setelah kolom kepsek_hadir
            $table->string('tupoksi')->nullable()->after('kepsek_hadir');
            $table->text('keterangan')->nullable()->after('tupoksi');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Menghapus kolom jika sewaktu-waktu di-rollback
            $table->dropColumn(['tupoksi', 'keterangan']);
        });
    }
};