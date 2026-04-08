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
    Schema::create('monthly_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
        $table->integer('bulan'); // Contoh: 1 (Januari), 2 (Februari), dst
        $table->year('tahun'); // Contoh: 2026
        
        // Link GDrive Laporan Wakasek
        $table->string('kurikulum_link')->nullable();
        $table->string('kesiswaan_link')->nullable();
        $table->string('sarpras_link')->nullable();
        $table->string('humas_link')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
