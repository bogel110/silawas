<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('monthly_reports', function (Blueprint $table) {
        // Menambahkan kolom tahun_pelajaran setelah kolom bulan
        $table->string('tahun_pelajaran')->nullable()->after('bulan');
    });
}

public function down()
{
    Schema::table('monthly_reports', function (Blueprint $table) {
        $table->dropColumn('tahun_pelajaran');
    });
}
};
