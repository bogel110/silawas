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
        $school1 = School::create([
            'name' => 'SMAN 1 Surabaya',
            'level' => 'SMA',
            'status' => 'Negeri',
            // Kita isi link GDrive untuk mensimulasikan sekolah yang tertib administrasi
            'ijop_link' => 'https://drive.google.com/file/d/dummy-ijop',
            'gtk_link' => 'https://drive.google.com/file/d/dummy-gtk',
            'pd_link' => 'https://drive.google.com/file/d/dummy-pd',
            'sarpras_link' => 'https://drive.google.com/file/d/dummy-sarpras',
        ]);

        $school2 = School::create([
            'name' => 'SMKN 2 Surabaya',
            'level' => 'SMK',
            'status' => 'Negeri',
            // Sekolah ini kita buat belum lengkap datanya
            'ijop_link' => 'https://drive.google.com/file/d/dummy-ijop2',
        ]);

        $school3 = School::create([
            'name' => 'SMA Budi Luhur',
            'level' => 'SMA',
            'status' => 'Swasta',
        ]);

        // 2. Membuat Akun Super Admin
        User::create([
            'name' => 'Super Admin SILAWAS',
            'email' => 'superadmin@silawas.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'school_id' => null,
        ]);

        // 3. Membuat Akun Pengawas
        $pengawas = User::create([
            'name' => 'Ir. Pengawas Hebat, M.Pd',
            'email' => 'pengawas@silawas.com',
            'password' => Hash::make('password123'), // Password default
            'role' => 'pengawas',
            'school_id' => null, // Pengawas tidak terikat 1 sekolah
        ]);
        $pengawas->supervisedSchools()->attach([$school1->id, $school2->id, $school3->id]);

        // 4. Membuat Akun Admin Sekolah
        User::create([
            'name' => 'Admin SMAN 1',
            'email' => 'admin.sman1@silawas.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_sekolah',
            'school_id' => $school1->id, // Terhubung ke SMAN 1
        ]);

        User::create([
            'name' => 'Admin SMKN 2',
            'email' => 'admin.smkn2@silawas.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_sekolah',
            'school_id' => $school2->id, // Terhubung ke SMKN 2
        ]);
    }
}
