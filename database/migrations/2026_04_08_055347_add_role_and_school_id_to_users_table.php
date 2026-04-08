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
    Schema::table('users', function (Blueprint $table) {
        // Menentukan role pengguna
        $table->enum('role', ['pengawas', 'admin_sekolah'])->default('admin_sekolah');
        
        // Menghubungkan user dengan tabel schools (Pengawas tidak punya sekolah, jadi boleh kosong/nullable)
        $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['school_id']);
        $table->dropColumn(['role', 'school_id']);
    });
}
};
