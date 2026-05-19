<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\KbmReport;
use App\Models\MonthlyReport;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSekolahCrudTest extends TestCase
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

    private function makeAdmin(School $school): User
    {
        return User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $school->id,
        ]);
    }

    public function test_admin_sekolah_can_manage_crud_for_own_school(): void
    {
        $school = $this->makeSchool('SMAN CRUD Admin');
        $admin = $this->makeAdmin($school);

        $this->actingAs($admin)
            ->post(route('school.update_links', $school->id), [
                'ijop_link' => 'https://drive.google.com/ijop',
                'ksp_link' => 'https://drive.google.com/ksp',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'ijop_link' => 'https://drive.google.com/ijop',
            'ksp_link' => 'https://drive.google.com/ksp',
        ]);

        $this->actingAs($admin)
            ->post(route('school.store_attendance', $school->id), [
                'siswa_hadir' => 320,
                'guru_hadir' => 42,
                'kepsek_hadir' => true,
                'tupoksi' => 'Manajerial',
                'keterangan' => 'Rapat koordinasi',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'school_id' => $school->id,
            'tanggal' => now()->toDateString(),
            'siswa_hadir' => 320,
            'guru_hadir' => 42,
            'tupoksi' => 'Manajerial',
        ]);

        $monthlyPayload = [
            'bulan' => 5,
            'tahun_pelajaran' => '2025/2026',
            'semester' => 'Genap',
            'kurikulum_link' => 'https://drive.google.com/kurikulum',
            'kesiswaan_link' => null,
            'sarpras_link' => null,
            'humas_link' => null,
        ];

        $this->actingAs($admin)
            ->post(route('school.store_monthly_report', $school->id), $monthlyPayload)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('school.store_monthly_report', $school->id), array_merge($monthlyPayload, [
                'kesiswaan_link' => 'https://drive.google.com/kesiswaan',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(1, MonthlyReport::where([
            'school_id' => $school->id,
            'bulan' => 5,
            'tahun_pelajaran' => '2025/2026',
            'semester' => 'Genap',
        ])->count());

        $monthlyReport = MonthlyReport::where('school_id', $school->id)->firstOrFail();
        $this->assertSame('https://drive.google.com/kesiswaan', $monthlyReport->kesiswaan_link);

        $this->actingAs($admin)
            ->put(route('school.update_monthly_report', $monthlyReport->id), [
                'tahun_pelajaran' => '2026/2027',
                'semester' => 'Ganjil',
                'kurikulum_link' => 'https://drive.google.com/kurikulum-baru',
                'kesiswaan_link' => null,
                'sarpras_link' => null,
                'humas_link' => null,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('monthly_reports', [
            'id' => $monthlyReport->id,
            'tahun_pelajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'kurikulum_link' => 'https://drive.google.com/kurikulum-baru',
        ]);

        $this->actingAs($admin)
            ->delete(route('school.destroy_monthly_report', $monthlyReport->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('monthly_reports', ['id' => $monthlyReport->id]);

        $this->actingAs($admin)
            ->post(route('school.store_kbm', $school->id), [
                'tahun_pelajaran' => '2025/2026',
                'intra_link' => 'https://drive.google.com/intra',
                'ko_link' => null,
                'extra_link' => null,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $kbm = KbmReport::where('school_id', $school->id)->firstOrFail();

        $this->actingAs($admin)
            ->put(route('school.update_kbm', $kbm->id), [
                'tahun_pelajaran' => '2026/2027',
                'intra_link' => 'https://drive.google.com/intra-baru',
                'ko_link' => 'https://drive.google.com/ko',
                'extra_link' => null,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('kbm_reports', [
            'id' => $kbm->id,
            'tahun_pelajaran' => '2026/2027',
            'ko_link' => 'https://drive.google.com/ko',
        ]);

        $this->actingAs($admin)
            ->delete(route('school.destroy_kbm', $kbm->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('kbm_reports', ['id' => $kbm->id]);

        $achievementPayload = [
            'tanggal' => '2026-05-01',
            'peringkat' => 'Juara 1',
            'tingkat' => 'Kabupaten',
            'kategori' => 'Individu',
            'tipe_peserta' => 'Siswa',
            'nama_peserta' => 'Siswa Berprestasi',
            'keterangan' => 'Lomba sains',
        ];

        $this->actingAs($admin)
            ->post(route('achievement.store'), $achievementPayload)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $achievement = Achievement::where('school_id', $school->id)->firstOrFail();

        $this->actingAs($admin)
            ->put(route('achievement.update', $achievement->id), array_merge($achievementPayload, [
                'peringkat' => 'Juara 2',
                'nama_peserta' => 'Siswa Teladan',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('achievements', [
            'id' => $achievement->id,
            'school_id' => $school->id,
            'peringkat' => 'Juara 2',
            'nama_peserta' => 'Siswa Teladan',
        ]);

        $this->actingAs($admin)
            ->delete(route('achievement.destroy', $achievement->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }

    public function test_admin_sekolah_cannot_manage_other_school_data_by_direct_url(): void
    {
        $ownSchool = $this->makeSchool('SMAN Admin Sendiri');
        $otherSchool = $this->makeSchool('SMAN Sekolah Lain');
        $admin = $this->makeAdmin($ownSchool);

        $monthlyReport = MonthlyReport::create([
            'school_id' => $otherSchool->id,
            'bulan' => 5,
            'tahun' => 2026,
            'tahun_pelajaran' => '2025/2026',
            'semester' => 'Genap',
        ]);

        $kbm = KbmReport::create([
            'school_id' => $otherSchool->id,
            'tahun_pelajaran' => '2025/2026',
        ]);

        $achievement = Achievement::create([
            'school_id' => $otherSchool->id,
            'tanggal' => '2026-05-01',
            'peringkat' => 'Juara 1',
            'tingkat' => 'Kabupaten',
            'kategori' => 'Individu',
            'tipe_peserta' => 'Siswa',
            'nama_peserta' => 'Peserta Lain',
            'keterangan' => 'Tidak boleh diubah',
        ]);

        $this->actingAs($admin)
            ->post(route('school.update_links', $otherSchool->id), ['ijop_link' => 'https://drive.google.com/coba'])
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('school.store_attendance', $otherSchool->id), [
                'siswa_hadir' => 1,
                'guru_hadir' => 1,
                'kepsek_hadir' => true,
                'tupoksi' => 'Manajerial',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('school.store_monthly_report', $otherSchool->id), [
                'bulan' => 6,
                'tahun_pelajaran' => '2025/2026',
                'semester' => 'Genap',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('school.update_monthly_report', $monthlyReport->id), [
                'tahun_pelajaran' => '2026/2027',
                'semester' => 'Ganjil',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('school.destroy_monthly_report', $monthlyReport->id))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('school.store_kbm', $otherSchool->id), [
                'tahun_pelajaran' => '2025/2026',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('school.update_kbm', $kbm->id), [
                'tahun_pelajaran' => '2026/2027',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('school.destroy_kbm', $kbm->id))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('achievement.update', $achievement->id), [
                'tanggal' => '2026-05-02',
                'peringkat' => 'Juara 2',
                'tingkat' => 'Kabupaten',
                'kategori' => 'Individu',
                'tipe_peserta' => 'Siswa',
                'nama_peserta' => 'Coba Ubah',
                'keterangan' => 'Coba ubah',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('achievement.destroy', $achievement->id))
            ->assertForbidden();

        $this->assertDatabaseMissing('attendances', [
            'school_id' => $otherSchool->id,
            'siswa_hadir' => 1,
        ]);
    }

    public function test_admin_sekolah_without_school_cannot_open_or_submit_crud_menus(): void
    {
        $school = $this->makeSchool('SMAN Tujuan');
        $admin = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => null,
        ]);

        $urls = [
            route('dashboard'),
            route('reports.index'),
            route('jurnal.index'),
            route('kbm.index'),
            route('achievement.admin'),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertForbidden();
        }

        $this->actingAs($admin)
            ->post(route('school.store_kbm', $school->id), [
                'tahun_pelajaran' => '2025/2026',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('achievement.store'), [
                'tanggal' => '2026-05-01',
                'peringkat' => 'Juara 1',
                'tingkat' => 'Kabupaten',
                'kategori' => 'Individu',
                'tipe_peserta' => 'Siswa',
                'nama_peserta' => 'Tanpa Sekolah',
                'keterangan' => 'Tidak boleh tersimpan',
            ])
            ->assertForbidden();
    }
}
