<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminUserImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengawas_cannot_access_administrator_users_page(): void
    {
        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);

        $this->actingAs($pengawas)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_import_admin_sekolah(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'admin-sekolah.csv',
            "nama_admin;email;password;nama_sekolah;level_sekolah;status_sekolah\r\n"
            . "Admin Contoh;admin.contoh@silawas.test;Silawas2026!;SMAN Import Test;SMA;Negeri\r\n"
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.users.import'), [
            'import_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'admin.contoh@silawas.test',
            'role' => 'admin_sekolah',
        ]);

        $this->assertDatabaseHas('schools', [
            'name' => 'SMAN Import Test',
            'level' => 'SMA',
            'status' => 'Negeri',
        ]);
    }
}
