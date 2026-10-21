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
        Schema::table('cycle_strategies', function (Blueprint $table) {
            $table->string('tahap_pendampingan')->nullable()->after('strategy');
            $table->text('fokus_sasaran')->nullable()->after('tahap_pendampingan');
            $table->string('metode_pendampingan')->nullable()->after('fokus_sasaran');
            $table->text('langkah_rencana_aksi')->nullable()->after('metode_pendampingan');
            $table->text('output_indikator')->nullable()->after('langkah_rencana_aksi');
            $table->date('jadwal_pendampingan')->nullable()->after('output_indikator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cycle_strategies', function (Blueprint $table) {
            $table->dropColumn([
                'tahap_pendampingan',
                'fokus_sasaran',
                'metode_pendampingan',
                'langkah_rencana_aksi',
                'output_indikator',
                'jadwal_pendampingan',
            ]);
        });
    }
};
