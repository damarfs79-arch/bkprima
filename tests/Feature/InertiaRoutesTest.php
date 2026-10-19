<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\Violation;

class InertiaRoutesTest extends TestCase
{
    use RefreshDatabase;

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
}
