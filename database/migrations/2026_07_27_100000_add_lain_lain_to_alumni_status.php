<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            DB::statement("ALTER TABLE alumnis MODIFY COLUMN status ENUM('Melanjutkan Studi','Bekerja','Lain-Lain') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            DB::statement("ALTER TABLE alumnis MODIFY COLUMN status ENUM('Melanjutkan Studi','Bekerja') NOT NULL");
        });
    }
};