<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;

class PromotionAndGraduationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_arsip_alumni_page_renders_successfully(): void
    {
        $this->seed();

        // Buat dummy alumni
        Student::create([
            'nisn' => '0099999001',
            'name' => 'Alumni Sukses Pratama',
            'class_name' => 'XII RPL 1',
            'rombel' => '1',
            'level' => '12',
            'major' => 'RPL',
            'gender' => 'L',
            'status' => 'lulus',
            'graduation_year' => 2025,
            'last_class_name' => 'XII RPL 1',
            'total_violation_points' => 0,
        ]);

        $response = $this->get('/arsip-lulus');
        $response->assertStatus(200);
        $this->assertStringContainsString('data-page', $response->getContent());
    }

    public function test_promotion_summary_endpoint(): void
    {
        $this->seed();

        $admin = User::firstOrCreate(
            ['email' => 'admin@bkprima.sch.id'],
            ['name' => 'Master Admin', 'role' => 'masteradmin', 'password' => bcrypt('password')]
        );
        $this->actingAs($admin);

        $response = $this->getJson('/master/kenaikan-kelas/summary');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'count_xii',
            'count_xi',
            'count_x',
            'current_year',
            'default_academic_year',
            'students',
        ]);
    }

    public function test_batch_promotion_and_graduation_workflow(): void
    {
        // 1. Setup sample students for grade 10, 11, and 12
        $studentX = Student::create([
            'nisn' => '1000000001',
            'name' => 'Siswa Kelas Sepuluh',
            'class_name' => 'X RPL 1',
            'rombel' => '1',
            'level' => '10',
            'major' => 'RPL',
            'status' => 'aktif',
            'total_violation_points' => 15,
        ]);

        $studentXI = Student::create([
            'nisn' => '1100000001',
            'name' => 'Siswa Kelas Sebelas',
            'class_name' => 'XI TSM 1',
            'rombel' => '1',
            'level' => '11',
            'major' => 'TSM',
            'status' => 'aktif',
            'total_violation_points' => 20,
        ]);

        $studentXII = Student::create([
            'nisn' => '1200000001',
            'name' => 'Siswa Calon Lulusan',
            'class_name' => 'XII DKV 1',
            'rombel' => '1',
            'level' => '12',
            'major' => 'DKV',
            'status' => 'aktif',
            'total_violation_points' => 10,
        ]);

        $studentRetained = Student::create([
            'nisn' => '1000000002',
            'name' => 'Siswa Tinggal Kelas X',
            'class_name' => 'X RPL 2',
            'rombel' => '2',
            'level' => '10',
            'major' => 'RPL',
            'status' => 'aktif',
            'total_violation_points' => 50,
        ]);

        // Buat rekaman pelanggaran historis
        $violation = Violation::create([
            'student_id' => $studentX->id,
            'student_name' => $studentX->name,
            'student_class' => $studentX->class_name,
            'class_snapshot' => 'X RPL 1',
            'category' => 'Ketertiban',
            'violation_name' => 'Terlambat Masuk',
            'points' => 15,
            'date' => now()->toDateString(),
            'status' => 'Tercatat',
        ]);

        // 2. Eksekusi Batch Kenaikan Kelas & Kelulusan
        $admin = User::firstOrCreate(
            ['email' => 'admin@bkprima.sch.id'],
            ['name' => 'Master Admin', 'role' => 'masteradmin', 'password' => bcrypt('password')]
        );
        $this->actingAs($admin);

        $response = $this->postJson('/master/kenaikan-kelas', [
            'graduation_year' => 2026,
            'new_academic_year' => '2026/2027',
            'retained_student_ids' => [$studentRetained->id],
            'reset_points' => true,
            'confirmation' => 'PROSES',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'graduated' => 1,
                'promoted_to_xii' => 1,
                'promoted_to_xi' => 1,
                'retained' => 1,
            ],
        ]);

        // 3. Verifikasi Status Siswa Kelas XII (Lulus)
        $studentXII->refresh();
        $this->assertEquals('lulus', $studentXII->status);
        $this->assertEquals(2026, $studentXII->graduation_year);
        $this->assertEquals('XII DKV 1', $studentXII->last_class_name);

        // 4. Verifikasi Promosi XI -> XII
        $studentXI->refresh();
        $this->assertEquals('aktif', $studentXI->status);
        $this->assertEquals('12', $studentXI->level);
        $this->assertEquals('XII TSM 1', $studentXI->class_name);
        $this->assertEquals(0, $studentXI->total_violation_points); // Reset points
        $this->assertEquals('2026/2027', $studentXI->academic_year);

        // 5. Verifikasi Promosi X -> XI
        $studentX->refresh();
        $this->assertEquals('aktif', $studentX->status);
        $this->assertEquals('11', $studentX->level);
        $this->assertEquals('XI RPL 1', $studentX->class_name);
        $this->assertEquals(0, $studentX->total_violation_points); // Reset points
        $this->assertEquals('2026/2027', $studentX->academic_year);

        // 6. Verifikasi Siswa Tinggal Kelas (Tidak Naik)
        $studentRetained->refresh();
        $this->assertEquals('aktif', $studentRetained->status);
        $this->assertEquals('10', $studentRetained->level);
        $this->assertEquals('X RPL 2', $studentRetained->class_name);

        // 7. Verifikasi Riwayat Pelanggaran Historis Tetap Utuh dan Snapshot Kelas Tidak Berubah
        $violation->refresh();
        $this->assertEquals('X RPL 1', $violation->class_snapshot);
        $this->assertDatabaseHas('violations', ['id' => $violation->id]);

        // 8. Verifikasi Active Scope: Siswa Lulus Tidak Muncul di Operational Query Siswa Aktif
        $activeIds = Student::active()->pluck('id')->toArray();
        $this->assertNotContains($studentXII->id, $activeIds);
        $this->assertContains($studentX->id, $activeIds);
        $this->assertContains($studentXI->id, $activeIds);
    }

    public function test_alumni_timeline_endpoint(): void
    {
        $alumni = Student::create([
            'nisn' => '1200000999',
            'name' => 'Alumni Hebat',
            'class_name' => 'XII RPL 1',
            'level' => '12',
            'major' => 'RPL',
            'status' => 'lulus',
            'graduation_year' => 2026,
            'last_class_name' => 'XII RPL 1',
            'total_violation_points' => 0,
        ]);

        $response = $this->getJson("/arsip-lulus/{$alumni->id}/timeline");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'student' => [
                'id',
                'name',
                'nisn',
                'last_class_name',
                'graduation_year',
                'major',
                'total_points',
                'status',
            ],
            'timeline',
        ]);
    }
}
