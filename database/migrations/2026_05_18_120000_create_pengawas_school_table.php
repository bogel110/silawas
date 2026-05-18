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
        Schema::create('pengawas_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'school_id']);
        });

        $now = now();
        $pengawasIds = DB::table('users')->where('role', 'pengawas')->pluck('id');
        $schoolIds = DB::table('schools')->pluck('id');
        $rows = [];

        foreach ($pengawasIds as $userId) {
            foreach ($schoolIds as $schoolId) {
                $rows[] = [
                    'user_id' => $userId,
                    'school_id' => $schoolId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('pengawas_school')->insert($rows);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawas_school');
    }
};
