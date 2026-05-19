<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchool(string $name): School
    {
        return School::create([
            'name' => $name,
            'level' => 'SMA',
            'status' => 'Negeri',
        ]);
    }

    private function makeSuperAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);
    }

    public function test_super_admin_can_manage_user_crud_and_pengawas_assignments(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $schoolA = $this->makeSchool('SMAN Super A');
        $schoolB = $this->makeSchool('SMAN Super B');

        $this->actingAs($superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Pengawas Baru',
                'email' => 'pengawas.baru@silawas.test',
                'password' => 'Silawas2026!',
                'role' => 'pengawas',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $pengawas = User::where('email', 'pengawas.baru@silawas.test')->firstOrFail();
        $this->assertSame('pengawas', $pengawas->role);
        $this->assertNull($pengawas->school_id);

        $this->actingAs($superAdmin)
            ->put(route('super-admin.pengawas-binaan.update', $pengawas->id), [
                'school_ids' => [$schoolA->id, $schoolB->id],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$schoolA->id, $schoolB->id],
            $pengawas->fresh()->supervisedSchools()->pluck('schools.id')->all()
        );

        $this->actingAs($superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Admin Sekolah Baru',
                'email' => 'admin.baru@silawas.test',
                'password' => 'Silawas2026!',
                'role' => 'admin_sekolah',
                'school_name' => 'SMAN Admin Baru',
                'school_level' => 'SMA',
                'school_status' => 'Swasta',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $adminSekolah = User::where('email', 'admin.baru@silawas.test')->firstOrFail();
        $this->assertSame('admin_sekolah', $adminSekolah->role);
        $this->assertNotNull($adminSekolah->school_id);

        $this->actingAs($superAdmin)
            ->put(route('admin.users.update', $adminSekolah->id), [
                'name' => 'Admin Sekolah Update',
                'email' => 'admin.update@silawas.test',
                'role' => 'admin_sekolah',
                'school_id' => $schoolB->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $adminSekolah->id,
            'name' => 'Admin Sekolah Update',
            'email' => 'admin.update@silawas.test',
            'role' => 'admin_sekolah',
            'school_id' => $schoolB->id,
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.users.reset_password', $adminSekolah->id), [
                'password' => 'PasswordBaru2026!',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue(Hash::check('PasswordBaru2026!', $adminSekolah->fresh()->password));

        $this->actingAs($superAdmin)
            ->delete(route('admin.users.destroy', $adminSekolah->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $adminSekolah->id]);
    }

    public function test_super_admin_cannot_demote_or_delete_current_account(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        $this->actingAs($superAdmin)
            ->put(route('admin.users.update', $superAdmin->id), [
                'name' => 'Super Admin Diri Sendiri',
                'email' => $superAdmin->email,
                'role' => 'pengawas',
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame('super_admin', $superAdmin->fresh()->role);

        $this->actingAs($superAdmin)
            ->delete(route('admin.users.destroy', $superAdmin->id))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', [
            'id' => $superAdmin->id,
            'role' => 'super_admin',
        ]);
    }

    public function test_non_super_admin_cannot_mutate_super_admin_crud_routes(): void
    {
        $school = $this->makeSchool('SMAN Terbatas');
        $targetUser = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $school->id,
        ]);
        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);

        $this->actingAs($pengawas)
            ->post(route('admin.users.store'), [
                'name' => 'Coba Tambah',
                'email' => 'coba@silawas.test',
                'password' => 'Silawas2026!',
                'role' => 'pengawas',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('admin.users.update', $targetUser->id), [
                'name' => 'Coba Ubah',
                'email' => 'ubah@silawas.test',
                'role' => 'pengawas',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('admin.users.reset_password', $targetUser->id), [
                'password' => 'PasswordBaru2026!',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->delete(route('admin.users.destroy', $targetUser->id))
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('super-admin.pengawas-binaan.update', $pengawas->id), [
                'school_ids' => [$school->id],
            ])
            ->assertForbidden();
    }

    public function test_pengawas_binaan_assignment_validates_target_and_school_ids(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $school = $this->makeSchool('SMAN Validasi Binaan');
        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);
        $adminSekolah = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $school->id,
        ]);

        $this->actingAs($superAdmin)
            ->put(route('super-admin.pengawas-binaan.update', $adminSekolah->id), [
                'school_ids' => [$school->id],
            ])
            ->assertNotFound();

        $this->actingAs($superAdmin)
            ->put(route('super-admin.pengawas-binaan.update', $pengawas->id), [
                'school_ids' => [$school->id, $school->id],
            ])
            ->assertSessionHasErrors();

        $this->actingAs($superAdmin)
            ->put(route('super-admin.pengawas-binaan.update', $pengawas->id), [
                'school_ids' => [999999],
            ])
            ->assertSessionHasErrors();

        $this->assertSame(0, $pengawas->fresh()->supervisedSchools()->count());
    }
}
