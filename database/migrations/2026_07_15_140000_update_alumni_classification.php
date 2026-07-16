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
        Schema::table('alumnis', function (Blueprint $table) {
            // Ubah jenis_studi dari nullable menjadi dapat KEDINASAN
            $table->dropColumn('jenis_studi');
            $table->enum('jenis_studi', ['PTN', 'PTS', 'KEDINASAN'])->nullable()->after('status');
            
            // Tambah jalur penerimaan (untuk studi)
            $table->enum('jalur_penerimaan', ['SNBP', 'SNBT', 'MANDIRI', 'KEDINASAN'])->nullable()->after('jenis_studi');
            
            // Tambah jenis pekerjaan (untuk bekerja)
            $table->enum('jenis_pekerjaan', ['ASN', 'TNI', 'POLRI', 'SWASTA'])->nullable()->after('jalur_penerimaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn('jenis_studi');
            $table->dropColumn('jalur_penerimaan');
            $table->dropColumn('jenis_pekerjaan');
            
            // Restore jenis_studi ke versi lama
            $table->enum('jenis_studi', ['PTN', 'PTS'])->nullable();
        });
    }
};
