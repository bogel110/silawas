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
        $table->string('ksp_link')->nullable()->after('ijop_link');
        $table->string('akreditasi_link')->nullable()->after('ksp_link');
    });
    }

    public function down(): void
    {
    Schema::table('schools', function (Blueprint $table) {
        $table->dropColumn(['ksp_link', 'akreditasi_link']);
    });
    }
};
