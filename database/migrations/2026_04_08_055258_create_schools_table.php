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
    Schema::create('schools', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: SMAN 1 Surabaya
        $table->string('level'); // Contoh: SMA, SMK
        $table->string('status')->default('Negeri'); // Negeri atau Swasta
        
        // Modul 1: Administrasi (Menyimpan Link GDrive)
        $table->string('ijop_link')->nullable();
        $table->string('gtk_link')->nullable();
        $table->string('pd_link')->nullable();
        $table->string('sarpras_link')->nullable();
        
        // Modul 4: Rapor Pendidikan (Link GDrive)
        $table->string('rapor_link')->nullable();
        $table->text('catatan_pengawas')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
