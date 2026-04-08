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
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
        $table->date('tanggal'); // Menyimpan tanggal absensi
        
        // Data Kehadiran
        $table->integer('guru_hadir')->default(0);
        $table->integer('siswa_hadir')->default(0);
        $table->boolean('kepsek_hadir')->default(false); // true/false
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
