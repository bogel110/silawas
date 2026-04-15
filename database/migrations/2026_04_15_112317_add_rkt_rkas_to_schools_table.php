<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Menambahkan kolom baru setelah rapor_link
            $table->string('rkt_link')->nullable()->after('rapor_link');
            $table->string('rkas_link')->nullable()->after('rkt_link');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['rkt_link', 'rkas_link']);
        });
    }
};