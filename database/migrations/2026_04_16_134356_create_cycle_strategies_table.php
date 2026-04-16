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
        Schema::create('cycle_strategies', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel sekolah
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            // Menyimpan nama strategi
            $table->string('strategy');
            // Keterangan / Catatan pengawas
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_strategies');
    }
};
