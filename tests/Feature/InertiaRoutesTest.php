<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\Violation;

class InertiaRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_all_inertia_pages_render_successfully(): void
    {
        $this->seed();

        $routes = [
            '/',
            '/login',
            '/absensi',
            '/statistik',
            '/pelanggaran',
            '/verifikasi',
            '/pelanggaran/detail',
            '/pemanggilan',
            '/piket',
            '/panduan',
            '/profil',
            '/admin',
            '/tamu',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $this->assertStringContainsString('data-page', $response->getContent(), "Route {$route} does not contain Inertia data-page attribute.");
        }
    }

    public function test_login_routes_connect_by_role(): void
    {
        // Guru login
        $guruResponse = $this->post('/login', [
            'nip' => '198503152010012014',
            'password' => 'secret123',
            'role' => 'guru',
        ]);
        $guruResponse->assertRedirect('/absensi');

        // Admin login
        $adminResponse = $this->post('/login', [
            'nip' => 'admin_bkprima',
            'password' => 'admin2026!',
            'role' => 'admin',
        ]);
        $adminResponse->assertRedirect('/admin');
    }

    public function test_logout_route_cleans_session(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/login');

        $getLogoutResponse = $this->get('/logout');
        $getLogoutResponse->assertRedirect('/login');
    }

    public function test_attendance_sync_endpoint(): void
    {
        $response = $this->post('/absensi/sync');
        $response->assertStatus(302);
    }

    public function test_profile_update_endpoint(): void
    {
        $response = $this->post('/profil', [
            'name' => 'Dra. Hj. Nurul Hidayati, M.Pd',
            'email' => 'nurul@example.com',
            'phone' => '08123456789',
        ]);
        $response->assertStatus(302);
    }

    public function test_guest_portal_searches_by_nisn(): void
    {
        $this->seed();
        $student = Student::first();

        $response = $this->get("/tamu?nisn={$student->nisn}");
        $response->assertStatus(200);
        $this->assertStringContainsString($student->name, $response->getContent());
    }

    public function test_store_violation_connects_properly(): void
    {
        $this->seed();
        $student = Student::first();

        $response = $this->post('/pelanggaran', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_class' => $student->class_name,
            'category' => 'Kedisiplinan',
            'violation_name' => 'Terlambat Masuk Jam Ke-1',
            'points' => 5,
            'date' => now()->toDateString(),
            'time' => '07:15',
            'reporter' => 'Bpk. Piket',
            'penalty_action' => 'Teguran lisan',
            'notes' => 'Catatan penindakan piket',
        ]);

        $response->assertRedirect('/pelanggaran');
        $this->assertDatabaseHas('violations', [
            'student_name' => $student->name,
            'violation_name' => 'Terlambat Masuk Jam Ke-1',
            'points' => 5,
        ]);
    }

    public function test_admin_can_manage_majors(): void
    {
        $response = $this->post('/admin/jurusan', [
            'code' => 'TKR',
            'name' => 'Teknik Kendaraan Ringan',
            'description' => 'Jurusan otomotif mobil'
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('majors', ['code' => 'TKR']);

        $major = \App\Models\Major::where('code', 'TKR')->first();
        $this->delete("/admin/jurusan/{$major->id}")->assertStatus(302);
        $this->assertDatabaseMissing('majors', ['code' => 'TKR']);
    }

    public function test_admin_can_manage_classroom_and_protect_with_students(): void
    {
        $response = $this->post('/admin/kelas', [
            'name' => 'XI TKKR 2',
            'level' => 'Kelas 11 (XI)',
            'jurusan' => 'TKKR',
            'waliKelas' => 'Bpk. Ahmad Santoso',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('classrooms', ['name' => 'XI TKKR 2']);
    }

    public function test_admin_can_import_students_and_store_to_database(): void
    {
        $studentsPayload = [
            [
                'nisn' => '0089911221',
                'name' => 'Muhammad Bintang Pratama',
                'className' => 'XI TKKR 2',
                'gender' => 'L',
                'parentWa' => '081234567890'
            ]
        ];

        $response = $this->post('/admin/siswa/import', [
            'students' => $studentsPayload,
            'targetClass' => 'XI TKKR 2'
        ]);
        $response->assertStatus(302);

        $this->assertDatabaseHas('students', [
            'nisn' => '0089911221',
            'name' => 'Muhammad Bintang Pratama',
            'class_name' => 'XI TKKR 2',
        ]);
    }

    public function test_violation_with_null_points_and_photo_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->seed();
        $student = Student::first();

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_pelanggaran.jpg', 600, 400);

        $response = $this->post('/pelanggaran', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_class' => $student->class_name,
            'category' => 'Pembinaan Karakter',
            'violation_name' => 'Konseling Keterlambatan',
            'points' => null, // Poin opsional (nullable)
            'foto_bukti' => $file,
        ]);

        $response->assertStatus(302);

        $violation = Violation::where('student_id', $student->id)
            ->where('category', 'Pembinaan Karakter')
            ->first();

        $this->assertNotNull($violation);
        $this->assertNull($violation->points);
        $this->assertNotNull($violation->foto_bukti);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($violation->foto_bukti);

        // Hapus violation dan pastikan file foto fisik ikut terhapus otomatis
        $filePath = $violation->foto_bukti;
        $this->delete("/pelanggaran/{$violation->id}")->assertStatus(302);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($filePath);
    }
}
