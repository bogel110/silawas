<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CycleStrategy;
use App\Models\MentoringCycle;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMenuAccessTest extends TestCase
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

    public function test_super_admin_can_open_all_supervision_and_admin_menus(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);

        $this->makeSchool('SMAN Super Admin Test');

        $routes = [
            route('dashboard'),
            route('reports.index'),
            route('jurnal.index'),
            route('kbm.index'),
            route('achievement.pengawas'),
            route('strategy.index'),
            route('mentoring.index'),
            route('super-admin.pengawas-binaan.index'),
            route('admin.users.index'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($superAdmin)->get($url)->assertOk();
        }
    }

    public function test_pengawas_only_accesses_assigned_school_and_cannot_open_super_admin_menus(): void
    {
        $assignedSchool = $this->makeSchool('SMAN Binaan Pengawas');
        $otherSchool = $this->makeSchool('SMAN Bukan Binaan');

        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);
        $pengawas->supervisedSchools()->attach($assignedSchool->id);

        $this->actingAs($pengawas)->get(route('dashboard'))->assertOk();
        $this->actingAs($pengawas)->get(route('school.show', $assignedSchool->id))->assertOk();
        $this->actingAs($pengawas)->get(route('school.show', $otherSchool->id))->assertForbidden();

        $restrictedUrls = [
            route('reports.index', ['school_id' => $otherSchool->id]),
            route('jurnal.index', ['school_id' => $otherSchool->id]),
            route('school.export_attendance', $otherSchool->id),
            route('kbm.index', ['school_id' => $otherSchool->id]),
            route('achievement.pengawas', ['school_id' => $otherSchool->id]),
            route('achievement.export.pengawas', ['school_id' => $otherSchool->id]),
            route('strategy.index', ['school_id' => $otherSchool->id]),
            route('strategy.export', ['school_id' => $otherSchool->id]),
            route('mentoring.index', ['school_id' => $otherSchool->id]),
            route('mentoring.export', ['school_id' => $otherSchool->id]),
            route('admin.users.index'),
            route('super-admin.pengawas-binaan.index'),
        ];

        foreach ($restrictedUrls as $url) {
            $response = $this->actingAs($pengawas)->get($url);
            $this->assertSame(403, $response->getStatusCode(), $url);
        }
    }

    public function test_pengawas_cannot_mutate_unassigned_school_records(): void
    {
        $assignedSchool = $this->makeSchool('SMAN Binaan Mutasi');
        $otherSchool = $this->makeSchool('SMAN Bukan Binaan Mutasi');

        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);
        $pengawas->supervisedSchools()->attach($assignedSchool->id);

        $strategy = CycleStrategy::create([
            'school_id' => $otherSchool->id,
            'strategy' => 'Perubahan Segera (Rapid Change)',
            'keterangan' => 'Tidak boleh diubah',
        ]);

        $cycle = MentoringCycle::create([
            'school_id' => $otherSchool->id,
            'siklus' => 'Perencanaan Pendampingan',
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Tidak boleh diubah',
        ]);

        $attendance = Attendance::create([
            'school_id' => $otherSchool->id,
            'tanggal' => now()->toDateString(),
            'siswa_hadir' => 10,
            'guru_hadir' => 2,
            'kepsek_hadir' => true,
            'tupoksi' => 'Manajerial',
            'keterangan' => 'Tidak boleh dihapus',
        ]);

        $this->actingAs($pengawas)
            ->post(route('strategy.store'), [
                'school_id' => $otherSchool->id,
                'strategy' => 'Perubahan Segera (Rapid Change)',
                'keterangan' => 'Coba tambah',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('strategy.update', $strategy->id), [
                'strategy' => 'Perubahan Berangsur (Gradual Change)',
                'keterangan' => 'Coba ubah',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->post(route('mentoring.store'), [
                'school_id' => $otherSchool->id,
                'siklus' => 'Perencanaan Pendampingan',
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Coba tambah',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->put(route('mentoring.update', $cycle->id), [
                'siklus' => 'Pelaporan Pendampingan',
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Coba ubah',
            ])
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->delete(route('attendance.destroy', $attendance->id))
            ->assertForbidden();
    }

    public function test_admin_sekolah_only_accesses_own_school_menus(): void
    {
        $ownSchool = $this->makeSchool('SMAN Admin Sendiri');
        $otherSchool = $this->makeSchool('SMAN Admin Lain');

        $adminSekolah = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $ownSchool->id,
        ]);

        $this->actingAs($adminSekolah)->get(route('dashboard'))->assertRedirect(route('school.show', $ownSchool->id));
        $this->actingAs($adminSekolah)->get(route('school.show', $ownSchool->id))->assertOk();
        $this->actingAs($adminSekolah)->get(route('school.show', $otherSchool->id))->assertForbidden();

        $allowedUrls = [
            route('reports.index'),
            route('jurnal.index'),
            route('kbm.index'),
            route('achievement.admin'),
        ];

        foreach ($allowedUrls as $url) {
            $this->actingAs($adminSekolah)->get($url)->assertOk();
        }

        $restrictedUrls = [
            route('achievement.pengawas'),
            route('strategy.index'),
            route('mentoring.index'),
            route('admin.users.index'),
            route('super-admin.pengawas-binaan.index'),
        ];

        foreach ($restrictedUrls as $url) {
            $response = $this->actingAs($adminSekolah)->get($url);
            $this->assertSame(403, $response->getStatusCode(), $url);
        }
    }
}
