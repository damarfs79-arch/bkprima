<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Major;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Violation;
use App\Models\ParentSummon;
use App\Models\Rule;
use App\Models\PointDeduction;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComprehensiveCrudAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $guru;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->seed();

        $this->admin = User::where('email', 'admin@bkprima.sch.id')->first();
        $this->guru = User::where('email', 'damar@bkprima.sch.id')->first();
    }

    public function test_complete_jurusan_crud_and_protection()
    {
        $this->actingAs($this->admin);

        // 1. Create Jurusan
        $response = $this->post('/admin/jurusan', [
            'code' => 'ANIMASI',
            'name' => 'Teknik Animasi 3D',
            'description' => 'Jurusan animasi digital dan motion graphic',
        ]);
        $response->assertStatus(302);

        $major = Major::where('code', 'ANIMASI')->first();
        $this->assertNotNull($major);
        $this->assertEquals('Teknik Animasi 3D', $major->name);

        // 2. Update Jurusan
        $response = $this->put("/admin/jurusan/{$major->id}", [
            'code' => 'ANIMASI',
            'name' => 'Teknik Animasi 3D & VFX',
            'description' => 'Jurusan animasi dan visual effects',
        ]);
        $response->assertStatus(302);
        $major->refresh();
        $this->assertEquals('Teknik Animasi 3D & VFX', $major->name);

        // 3. Test protection when classroom exists
        $classroom = Classroom::create([
            'name' => 'X ANIMASI 1',
            'level' => 'Kelas 10 (X)',
            'major' => 'ANIMASI',
            'jurusan_id' => $major->id,
            'wali_kelas' => 'Wali Kelas Animasi',
        ]);

        $response = $this->delete("/admin/jurusan/{$major->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('majors', ['id' => $major->id]);

        // 4. Delete Classroom first, then delete Jurusan
        $classroom->delete();
        $response = $this->delete("/admin/jurusan/{$major->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('majors', ['id' => $major->id]);
    }

    public function test_complete_guru_bk_crud()
    {
        $this->actingAs($this->admin);

        // 1. Create Guru BK
        $email = 'counselor.test@bkprima.sch.id';
        $response = $this->post('/admin/guru', [
            'name' => 'Ibu Rahmawati, S.Pd',
            'email' => $email,
            'password' => 'secret123',
            'jabatan' => 'Koordinator BK',
            'wa' => '081234567890',
            'rombelText' => 'X RPL 1, XI RPL 1',
        ]);
        $response->assertStatus(302);

        $guruUser = User::where('email', $email)->first();
        $this->assertNotNull($guruUser);
        $this->assertEquals('guru', $guruUser->role);
        $this->assertEquals('Ibu Rahmawati, S.Pd', $guruUser->name);

        // Verify classrooms assigned
        $xRpl = Classroom::where('name', 'X RPL 1')->first();
        if ($xRpl) {
            $this->assertEquals($guruUser->id, $xRpl->guru_bk_id);
        }

        // 2. Update Guru BK
        $response = $this->put("/admin/guru/{$guruUser->id}", [
            'name' => 'Ibu Rahmawati, M.Psi',
            'email' => $email,
            'jabatan' => 'Konselor Ahli Muda',
            'wa' => '081234567899',
            'rombelText' => 'X RPL 1',
        ]);
        $response->assertStatus(302);
        $guruUser->refresh();
        $this->assertEquals('Ibu Rahmawati, M.Psi', $guruUser->name);

        // 3. Delete Guru BK
        $response = $this->delete("/admin/guru/{$guruUser->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('users', ['id' => $guruUser->id]);

        // Classrooms should have guru_bk_id cleared
        if ($xRpl) {
            $xRpl->refresh();
            $this->assertNull($xRpl->guru_bk_id);
        }
    }

    public function test_complete_classroom_crud_and_student_relation()
    {
        $this->actingAs($this->admin);

        $major = Major::firstOrCreate(['code' => 'TKKR'], ['name' => 'Teknik Kendaraan Ringan']);

        // 1. Create Classroom
        $response = $this->post('/admin/kelas', [
            'name' => 'X TKKR 99',
            'level' => 'Kelas 10 (X)',
            'jurusan_id' => $major->id,
            'waliKelas' => 'Bpk. Budi Rahardjo, S.Pd',
            'guruBkId' => $this->guru->id,
        ]);
        $response->assertStatus(302);

        $classroom = Classroom::where('name', 'X TKKR 99')->first();
        $this->assertNotNull($classroom);
        $this->assertEquals($major->id, $classroom->jurusan_id);
        $this->assertEquals($this->guru->id, $classroom->guru_bk_id);

        // 2. Add Student to this classroom
        $student = Student::create([
            'classroom_id' => $classroom->id,
            'nis' => '9988776655',
            'name' => 'Siswa Uji Coba Relasi',
            'class_name' => $classroom->name,
            'gender' => 'L',
            'level' => '10',
            'major' => 'TKKR',
            'status' => 'aktif',
        ]);

        // Verify students count in relation
        $classroomWithCount = Classroom::withCount('students')->find($classroom->id);
        $this->assertGreaterThan(0, $classroomWithCount->students_count);

        // 3. Update Classroom
        $response = $this->put("/admin/kelas/{$classroom->id}", [
            'name' => 'X TKKR 98',
            'level' => 'Kelas 10 (X)',
            'jurusan_id' => $major->id,
            'waliKelas' => 'Bpk. Budi Rahardjo, M.Pd',
            'guruBkId' => $this->guru->id,
        ]);
        $response->assertStatus(302);
        $classroom->refresh();
        $this->assertEquals('X TKKR 98', $classroom->name);

        // Verify student class_name synchronized
        $student->refresh();
        $this->assertEquals('X TKKR 98', $student->class_name);
        $this->assertEquals($classroom->id, $student->classroom_id);

        // 4. Attempt Delete with active student -> must be protected
        $response = $this->delete("/admin/kelas/{$classroom->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);

        // 5. Delete Student, then delete Classroom
        $student->delete();
        $response = $this->delete("/admin/kelas/{$classroom->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('classrooms', ['id' => $classroom->id]);
    }

    public function test_complete_student_crud_and_import()
    {
        $this->actingAs($this->admin);

        // 1. Create Student
        $response = $this->post('/admin/siswa', [
            'nis' => '0099881122',
            'name' => 'Indra Wijaya',
            'className' => 'X RPL 1',
            'gender' => 'L',
            'parentWa' => '081234567800',
        ]);
        $response->assertStatus(302);

        $student = Student::where('nis', '0099881122')->first();
        $this->assertNotNull($student);
        $this->assertEquals('X RPL 1', $student->class_name);
        $this->assertNotNull($student->classroom_id);
        $this->assertEquals('Indra Wijaya', $student->name);

        // 2. Update Student
        $response = $this->put("/admin/siswa/{$student->id}", [
            'nis' => '0099881122',
            'name' => 'Indra Wijaya Saputra',
            'className' => 'X RPL 1',
            'gender' => 'L',
            'parentWa' => '081234567899',
            'poin' => 5,
        ]);
        $response->assertStatus(302);
        $student->refresh();
        $this->assertEquals('Indra Wijaya Saputra', $student->name);
        $this->assertEquals(5, $student->total_violation_points);
        $this->assertNotNull($student->classroom_id);

        // 3. Import Students Massal
        $importPayload = [
            'students' => [
                [
                    'nis' => '0099881123',
                    'name' => 'Dwi Handayani',
                    'className' => 'X RPL 1',
                    'gender' => 'P',
                    'parentWa' => '081234567801',
                ],
                [
                    'nis' => '0099881124',
                    'name' => 'Gilang Ramadhan',
                    'className' => 'X RPL 1',
                    'gender' => 'L',
                    'parentWa' => '081234567802',
                ],
            ],
            'targetClass' => 'X RPL 1',
        ];

        $response = $this->post('/admin/siswa/import', $importPayload);
        $response->assertStatus(302);

        $imported1 = Student::where('nis', '0099881123')->first();
        $this->assertNotNull($imported1);
        $this->assertNotNull($imported1->classroom_id);

        $imported2 = Student::where('nis', '0099881124')->first();
        $this->assertNotNull($imported2);
        $this->assertNotNull($imported2->classroom_id);

        // 4. Delete Student
        $response = $this->delete("/admin/siswa/{$student->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_attendance_status_and_stats()
    {
        $this->actingAs($this->guru);

        $student = Student::first();
        $this->assertNotNull($student);

        // Update status presensi
        $response = $this->post('/absensi/status', [
            'student_id' => $student->id,
            'status' => 'Terlambat',
            'check_in_time' => '07:15 WIB',
            'notes' => 'Terlambat 15 menit',
        ]);
        $response->assertStatus(302);

        $attendance = Attendance::where('student_id', $student->id)->where('date', now()->toDateString())->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('Terlambat', $attendance->status);
    }

    public function test_violation_and_parent_summons_crud()
    {
        $this->actingAs($this->guru);

        $student = Student::first();
        $this->assertNotNull($student);

        // 1. Store Violation
        $response = $this->post('/pelanggaran', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_class' => $student->class_name,
            'category' => 'Kedisiplinan',
            'violation_name' => 'Terlambat masuk sekolah tanpa izin',
            'points' => 10,
            'date' => now()->toDateString(),
            'reporter' => 'Damar',
            'penalty_action' => 'Peringatan lisan',
            'status' => 'Terverifikasi',
        ]);
        $response->assertStatus(302);

        $violation = Violation::where('student_id', $student->id)->latest('id')->first();
        $this->assertNotNull($violation);
        $this->assertEquals(10, $violation->points);

        // 2. Update Violation
        $response = $this->put("/pelanggaran/{$violation->id}", [
            'category' => 'Kedisiplinan',
            'violation_name' => 'Terlambat masuk sekolah berulang kali',
            'points' => 15,
            'date' => now()->toDateString(),
            'reporter' => 'Damar',
            'penalty_action' => 'Surat Peringatan 1',
            'status' => 'Terverifikasi',
        ]);
        $response->assertStatus(302);
        $violation->refresh();
        $this->assertEquals(15, $violation->points);

        // 3. Store Parent Summon
        $response = $this->post('/pelanggaran/panggilan', [
            'student_id' => $student->id,
            'stage' => 'SP-1',
            'case_title' => 'Akumulasi Poin Pelanggaran',
            'points' => 15,
            'meeting_date' => now()->addDays(2)->format('d M Y'),
            'meeting_time' => '09:00 WIB',
            'room' => 'Ruang BK',
        ]);
        $response->assertStatus(302);

        $summon = ParentSummon::where('student_id', $student->id)->first();
        $this->assertNotNull($summon);

        // 4. Update Summon
        $response = $this->patch("/pelanggaran/panggilan/{$summon->id}", [
            'status' => 'Hadir',
            'mediation_notes' => 'Orang tua hadir dan menandatangani surat perjanjian.',
        ]);
        $response->assertStatus(302);
        $summon->refresh();
        $this->assertEquals('Hadir', $summon->status);

        // 5. Delete Summon & Violation
        $this->delete("/pelanggaran/panggilan/{$summon->id}")->assertStatus(302);
        $this->assertDatabaseMissing('parent_summons', ['id' => $summon->id]);

        $this->delete("/pelanggaran/{$violation->id}")->assertStatus(302);
        $this->assertDatabaseMissing('violations', ['id' => $violation->id]);
    }

    public function test_rules_and_point_deductions_crud()
    {
        $this->actingAs($this->guru);

        // 1. Store Rule
        $response = $this->post('/panduan/aturan', [
            'type' => 'pelanggaran',
            'category' => 'Kerapian',
            'name' => 'Atribut seragam tidak lengkap',
            'points' => 5,
            'sanction' => 'Peringatan',
        ]);
        $response->assertStatus(302);

        $rule = Rule::where('name', 'Atribut seragam tidak lengkap')->first();
        $this->assertNotNull($rule);

        // 2. Update Rule
        $response = $this->put("/panduan/aturan/{$rule->id}", [
            'type' => 'pelanggaran',
            'category' => 'Kerapian',
            'name' => 'Atribut seragam tidak lengkap (revisi)',
            'points' => 10,
            'sanction' => 'Pembinaan',
        ]);
        $response->assertStatus(302);
        $rule->refresh();
        $this->assertEquals(10, $rule->points);

        // 3. Store Point Deduction
        $student = Student::first();
        $response = $this->post('/panduan/pemutihan', [
            'student_id' => $student->id,
            'title' => 'Kerja Bakti Perpustakaan',
            'type' => 'Tugas Edukatif',
            'points_deducted' => 10,
            'date' => now()->toDateString(),
            'notes' => 'Menjalankan sanksi edukatif dengan baik',
        ]);
        $response->assertStatus(302);

        $deduction = PointDeduction::where('student_id', $student->id)->first();
        $this->assertNotNull($deduction);
        $this->assertEquals(10, $deduction->points_deducted);

        // 4. Delete Deduction and Rule
        $this->delete("/panduan/pemutihan/{$deduction->id}")->assertStatus(302);
        $this->assertDatabaseMissing('point_deductions', ['id' => $deduction->id]);

        $this->delete("/panduan/aturan/{$rule->id}")->assertStatus(302);
        $this->assertDatabaseMissing('rules', ['id' => $rule->id]);
    }

    public function test_school_settings_update()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/pengaturan', [
            'kepala_sekolah' => 'Saiful Anwar, S.Pd.',
            'nip_kepala_sekolah' => '19780707 200902 1 002',
            'semester' => 'Semester Genap 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'school_name' => 'SMK PGRI 05 JEMBER',
            'school_address' => 'Jl. Krakatau No. 54 Tanggul, Jember',
        ]);
        $response->assertStatus(302);

        $this->assertEquals('Saiful Anwar, S.Pd.', Setting::get('kepala_sekolah'));
        $this->assertEquals('2026/2027', Setting::get('tahun_ajaran'));
    }
}
