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
        // Menambahkan kolom setelah sarpras_link
        $table->string('rpp_link')->nullable()->after('sarpras_link');
        $table->string('ekskul_link')->nullable()->after('rpp_link');
    });
}

public function down(): void
{
    Schema::table('schools', function (Blueprint $table) {
        $table->dropColumn(['rpp_link', 'ekskul_link']);
    });
}

    /**
     * Reverse the migrations.
     */

};
