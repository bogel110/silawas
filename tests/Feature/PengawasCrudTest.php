<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CycleStrategy;
use App\Models\MentoringCycle;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengawasCrudTest extends TestCase
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

    private function makePengawas(School $school): User
    {
        $pengawas = User::factory()->create([
            'role' => 'pengawas',
            'school_id' => null,
        ]);

        $pengawas->supervisedSchools()->attach($school->id);

        return $pengawas;
    }

    public function test_pengawas_can_manage_allowed_crud_for_assigned_school(): void
    {
        $school = $this->makeSchool('SMAN Binaan CRUD');
        $pengawas = $this->makePengawas($school);

        $this->actingAs($pengawas)
            ->post(route('school.update_catatan', $school->id), [
                'catatan_pengawas' => 'Perlu tindak lanjut dokumen sekolah.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'catatan_pengawas' => 'Perlu tindak lanjut dokumen sekolah.',
        ]);

        $this->actingAs($pengawas)
            ->post(route('strategy.store'), [
                'school_id' => $school->id,
                'strategy' => 'Perubahan Segera (Rapid Change)',
                'keterangan' => 'Dorongan awal',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $strategy = CycleStrategy::where('school_id', $school->id)->firstOrFail();

        $this->actingAs($pengawas)
            ->put(route('strategy.update', $strategy->id), [
                'strategy' => 'Penguatan Perubahan (Reinforcing Change)',
                'keterangan' => 'Perkuat pelaksanaan',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('cycle_strategies', [
            'id' => $strategy->id,
            'strategy' => 'Penguatan Perubahan (Reinforcing Change)',
            'keterangan' => 'Perkuat pelaksanaan',
        ]);

        $this->actingAs($pengawas)
            ->delete(route('strategy.destroy', $strategy->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('cycle_strategies', ['id' => $strategy->id]);

        $this->actingAs($pengawas)
            ->post(route('mentoring.store'), [
                'school_id' => $school->id,
                'siklus' => 'Perencanaan Pendampingan',
                'tanggal' => '2026-05-19',
                'keterangan' => 'Rencana kunjungan',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $cycle = MentoringCycle::where('school_id', $school->id)->firstOrFail();

        $this->actingAs($pengawas)
            ->put(route('mentoring.update', $cycle->id), [
                'siklus' => 'Pelaporan Pendampingan',
                'tanggal' => '2026-05-20',
                'keterangan' => 'Laporan selesai',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('mentoring_cycles', [
            'id' => $cycle->id,
            'siklus' => 'Pelaporan Pendampingan',
            'tanggal' => '2026-05-20',
        ]);

        $this->actingAs($pengawas)
            ->delete(route('mentoring.destroy', $cycle->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('mentoring_cycles', ['id' => $cycle->id]);

        $attendance = Attendance::create([
            'school_id' => $school->id,
            'tanggal' => '2026-05-19',
            'siswa_hadir' => 100,
            'guru_hadir' => 20,
            'kepsek_hadir' => true,
            'tupoksi' => 'Manajerial',
            'keterangan' => 'Data uji',
        ]);

        $this->actingAs($pengawas)
            ->delete(route('attendance.destroy', $attendance->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    public function test_pengawas_cannot_delete_school_master_even_if_assigned(): void
    {
        $school = $this->makeSchool('SMAN Tidak Boleh Dihapus');
        $pengawas = $this->makePengawas($school);

        $this->actingAs($pengawas)
            ->delete(route('school.destroy', $school->id))
            ->assertForbidden();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'SMAN Tidak Boleh Dihapus',
        ]);
    }

    public function test_pengawas_input_must_use_valid_strategy_and_cycle_options(): void
    {
        $school = $this->makeSchool('SMAN Validasi Pengawas');
        $pengawas = $this->makePengawas($school);

        $this->actingAs($pengawas)
            ->post(route('strategy.store'), [
                'school_id' => $school->id,
                'strategy' => 'Pilihan Tidak Terdaftar',
                'keterangan' => 'Tidak boleh masuk',
            ])
            ->assertSessionHasErrors('strategy');

        $this->assertDatabaseMissing('cycle_strategies', [
            'school_id' => $school->id,
            'strategy' => 'Pilihan Tidak Terdaftar',
        ]);

        $this->actingAs($pengawas)
            ->post(route('mentoring.store'), [
                'school_id' => $school->id,
                'siklus' => 'Siklus Tidak Terdaftar',
                'tanggal' => '2026-05-19',
                'keterangan' => 'Tidak boleh masuk',
            ])
            ->assertSessionHasErrors('siklus');

        $this->assertDatabaseMissing('mentoring_cycles', [
            'school_id' => $school->id,
            'siklus' => 'Siklus Tidak Terdaftar',
        ]);
    }
}
