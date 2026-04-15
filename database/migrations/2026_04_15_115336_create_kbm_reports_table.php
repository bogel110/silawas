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
    Schema::create('kbm_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained()->onDelete('cascade');
        $table->string('tahun_pelajaran');
        $table->text('intra_link')->nullable(); // Intrakurikuler (RPP/Modul Ajar)
        $table->text('ko_link')->nullable();    // Kokurikuler
        $table->text('extra_link')->nullable(); // Ekstrakurikuler
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kbm_reports');
    }
};
