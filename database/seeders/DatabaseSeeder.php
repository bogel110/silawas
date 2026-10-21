<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Data Sekolah Dummy
        $school1 = School::firstOrCreate(
            ['name' => 'SMAN 1 Surabaya'],
            [
                'level' => 'SMA',
                'status' => 'Negeri',
                'ijop_link' => 'https://drive.google.com/file/d/dummy-ijop',
                'gtk_link' => 'https://drive.google.com/file/d/dummy-gtk',
                'pd_link' => 'https://drive.google.com/file/d/dummy-pd',
                'sarpras_link' => 'https://drive.google.com/file/d/dummy-sarpras',
            ]
        );

        $school2 = School::firstOrCreate(
            ['name' => 'SMKN 2 Surabaya'],
            [
                'level' => 'SMK',
                'status' => 'Negeri',
                'ijop_link' => 'https://drive.google.com/file/d/dummy-ijop2',
            ]
        );

        $school3 = School::firstOrCreate(
            ['name' => 'SMA Budi Luhur'],
            [
                'level' => 'SMA',
                'status' => 'Swasta',
            ]
        );

        // 2. Membuat Akun Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@silawas.com'],
            [
                'name' => 'Super Admin SILAWAS',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'school_id' => null,
            ]
        );

        // 3. Membuat Akun Pengawas
        $pengawas = User::updateOrCreate(
            ['email' => 'pengawas@silawas.com'],
            [
                'name' => 'Ir. Pengawas Hebat, M.Pd',
                'password' => Hash::make('password123'),
                'role' => 'pengawas',
                'school_id' => null,
            ]
        );
        $pengawas->supervisedSchools()->syncWithoutDetaching([$school1->id, $school2->id, $school3->id]);

        // 4. Membuat Akun Admin Sekolah
        User::updateOrCreate(
            ['email' => 'admin.sman1@silawas.com'],
            [
                'name' => 'Admin SMAN 1',
                'password' => Hash::make('password123'),
                'role' => 'admin_sekolah',
                'school_id' => $school1->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.smkn2@silawas.com'],
            [
                'name' => 'Admin SMKN 2',
                'password' => Hash::make('password123'),
                'role' => 'admin_sekolah',
                'school_id' => $school2->id,
            ]
        );
    }
}
