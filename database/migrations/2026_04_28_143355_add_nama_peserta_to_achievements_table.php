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
        Schema::table('achievements', function (Blueprint $table) {
            // Menambah kolom nama_peserta setelah kolom tipe_peserta
            $table->string('nama_peserta')->nullable()->after('tipe_peserta');
        });
    }

    public function down()
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn('nama_peserta');
        });
    }
};
