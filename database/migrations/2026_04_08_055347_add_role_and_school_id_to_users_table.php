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
            // UBAH BARIS INI: Gunakan string agar bebas menerima teks (Kepala Sekolah, dll)
            $table->string('role')->nullable();
            
            // Baris school_id ini sudah sangat benar, biarkan saja
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
