<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::table('users')->where('role', 'super_admin')->exists()) {
            return;
        }

        $existingUser = DB::table('users')->where('email', 'superadmin@silawas.com')->first();

        if ($existingUser) {
            DB::table('users')
                ->where('id', $existingUser->id)
                ->update([
                    'role' => 'super_admin',
                    'school_id' => null,
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('users')->insert([
            'name' => 'Super Admin SILAWAS',
            'email' => 'superadmin@silawas.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'school_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
