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
        Schema::table('schools', function (Blueprint $table) {
            // Menghapus kolom dari database
            $table->dropColumn(['rpp_link', 'ekskul_link']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Menambahkan kembali jika sewaktu-waktu di-rollback
            $table->string('rpp_link')->nullable();
            $table->string('ekskul_link')->nullable();
        });
    }
};