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
            '/arsip-lulus',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $this->assertStringContainsString('data-page', $response->getContent(), "Route {$route} does not contain Inertia data-page attribute.");
        }
    }

    public function test_login_routes_connect_by_role(): void
    {
        $this->seed();

        // 1. Guru login dengan Email resmi
        $guruResponse = $this->post('/login', [
            'email' => 'nurul.hidayati@guru.smk.belajar.id',
            'password' => 'secret123',
        ]);
        $guruResponse->assertRedirect('/absensi');

        // 2. Admin login dengan Email
        $adminResponse = $this->post('/login', [
            'email' => 'admin@bkprima.sch.id',
            'password' => 'admin2026!',
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
            'nis' => '0089911221',
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

    public function test_store_manual_violation_with_string_null_and_manual_input(): void
    {
        $this->seed();

        $response = $this->post('/pelanggaran', [
            'student_id' => 'null', // string 'null' dari FormData browser
            'student_name' => 'Siswa Luar Belum Terdaftar',
            'student_class' => 'X RPL 1',
            'category' => 'Terlambat Masuk',
            'points' => 'null', // string 'null'
            'poin' => 'null',
            'foto_bukti' => 'null',
            'notes' => 'Siswa titipan luar',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('violations', [
            'student_name' => 'Siswa Luar Belum Terdaftar',
            'category' => 'Terlambat Masuk',
            'student_class' => 'X RPL 1',
        ]);
    }

    public function test_student_crud_with_nis_and_clear_class_bulk_delete(): void
    {
        $this->seed();

        // 1. Tambah siswa dengan NIS
        $response = $this->post('/siswa', [
            'nis' => '998877',
            'name' => 'Budi Bulk Test',
            'class_name' => 'X TSM 9',
            'gender' => 'L',
            'parent_name' => 'Wali Budi',
            'parent_phone' => '081234567890',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('students', [
            'nis' => '998877',
            'name' => 'Budi Bulk Test',
            'class_name' => 'X TSM 9',
        ]);

        $student = \App\Models\Student::where('nis', '998877')->first();
        $this->assertNotNull($student);
        // Backward-compatible nisn accessor
        $this->assertEquals('998877', $student->nisn);

        // 2. Kosongkan kelas X TSM 9
        $clearResponse = $this->post('/absensi/kelas/clear', [
            'class_name' => 'X TSM 9',
        ]);
        $clearResponse->assertStatus(302);

        // Pastikan siswa di kelas tersebut sudah terhapus
        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
            'nis' => '998877',
        ]);
    }

    public function test_admin_can_update_school_settings_and_headmaster_name(): void
    {
        $this->seed();

        $response = $this->post('/admin/pengaturan', [
            'kepala_sekolah' => 'Dra. Hj. Wahyuni, M.Pd',
            'nip_kepala_sekolah' => '19750820 200501 2 004',
            'semester' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'school_name' => 'SMK PGRI 05 JEMBER',
            'school_address' => 'Jl. Krakatau No. 54 Tanggul',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('settings', [
            'key' => 'kepala_sekolah',
            'value' => 'Dra. Hj. Wahyuni, M.Pd',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'semester',
            'value' => 'Semester Ganjil 2026/2027',
        ]);

        // Verifikasi pada halaman rekap bulanan data kepala sekolah baru tercermin
        $monthlyResponse = $this->get('/absensi/bulanan');
        $monthlyResponse->assertStatus(200);
        // dd($monthlyResponse->original->getData()['page']['props']);
        $monthlyResponse->assertInertia(fn ($page) => $page
            ->where('schoolInfo.kepalaSekolah', 'Dra. Hj. Wahyuni, M.Pd')
            ->where('schoolInfo.nipKepalaSekolah', '19750820 200501 2 004')
        );
        $this->assertEquals('Semester Ganjil 2026/2027', \App\Models\Setting::get('semester'));
    }

    public function test_parent_summons_creation_and_relations(): void
    {
        $this->seed();

        $student = \App\Models\Student::first();
        $this->assertNotNull($student);

        // 1. Simpan surat panggilan orang tua dengan inputan tahap bebas (misal: "Panggilan Khusus 1")
        $response = $this->post('/pelanggaran/panggilan', [
            'student_id' => $student->id,
            'stage' => 'Panggilan Khusus 1',
            'case_title' => 'Membawa rokok dan korek di kelas',
            'case_description' => 'Akumulasi poin 35 poin, membutuhkan klarifikasi orang tua.',
            'meeting_date' => 'Senin, 28 Okt 2026',
            'meeting_time' => '08.30 WIB',
            'room' => 'Ruang BK Gedung A',
            'parent_name' => 'Bapak Sudirman',
            'parent_phone' => '081234567899',
            'counselor' => 'Dra. Hj. Nurul Hidayati, M.Pd',
        ]);

        $response->assertStatus(302);

        $summon = \App\Models\ParentSummon::where('student_id', $student->id)->latest()->first();
        $this->assertNotNull($summon);
        $this->assertEquals('Panggilan Khusus 1', $summon->stage);
        $this->assertEquals($student->id, $summon->student->id);
        $this->assertEquals('Menunggu Kehadiran', $summon->status);

        // 2. Update status mediasi dan catatan
        $patchResponse = $this->patch('/pelanggaran/panggilan/' . $summon->id, [
            'status' => 'Mediasi Selesai',
            'mediation_notes' => 'Orang tua telah hadir dan menandatangani surat komitmen bersama.',
        ]);

        $patchResponse->assertStatus(302);
        $summon->refresh();
        $this->assertEquals('Mediasi Selesai', $summon->status);
        $this->assertEquals('Orang tua telah hadir dan menandatangani surat komitmen bersama.', $summon->mediation_notes);
    }

    public function test_dynamic_rombel_list_and_import_with_classroom_relation()
    {
        $this->seed();

        $admin = \App\Models\User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // 1. Pastikan GET /absensi mengirimkan rombelList dinamis dari master Classroom
        $response = $this->get('/absensi');
        $response->assertStatus(200);
        $pageProps = $response->viewData('page')['props'] ?? [];
        $this->assertArrayHasKey('rombelList', $pageProps);
        $this->assertNotEmpty($pageProps['rombelList']);
        $this->assertArrayHasKey('id', $pageProps['rombelList'][0]);
        $this->assertArrayHasKey('nama_rombel', $pageProps['rombelList'][0]);

        // 2. Buat rombel baru di tabel classrooms
        $customRombel = \App\Models\Classroom::create([
            'name' => 'XII RPL 3',
            'level' => 'Kelas 12 (XII)',
            'major' => 'RPL',
            'wali_kelas' => 'Bpk. Hendra Gunawan, S.Kom',
        ]);

        // 3. Test import dengan mode spesifik rombel_id
        $importSpecific = $this->post('/absensi/import', [
            'target_rombel_id' => $customRombel->id,
            'students' => [
                [
                    'nis' => '99881122',
                    'name' => 'Ahmad Fauzi Spesifik',
                    'gender' => 'L',
                    'parentWa' => '081234567890',
                ]
            ]
        ]);
        $importSpecific->assertStatus(302);

        $studentSpecific = \App\Models\Student::where('nis', '99881122')->first();
        $this->assertNotNull($studentSpecific);
        $this->assertEquals($customRombel->id, $studentSpecific->classroom_id);
        $this->assertEquals('XII RPL 3', $studentSpecific->class_name);
        $this->assertNotNull($studentSpecific->classroom);
        $this->assertEquals($customRombel->id, $studentSpecific->classroom->id);

        // 4. Test import dengan mode AUTO
        $importAuto = $this->post('/absensi/import', [
            'target_rombel_id' => 'AUTO',
            'students' => [
                [
                    'nis' => '99881123',
                    'name' => 'Dewi Lestari Auto',
                    'className' => 'XII RPL 3',
                    'gender' => 'P',
                    'parentWa' => '081234567891',
                ]
            ]
        ]);
        $importAuto->assertStatus(302);

        $studentAuto = \App\Models\Student::where('nis', '99881123')->first();
        $this->assertNotNull($studentAuto);
        $this->assertEquals($customRombel->id, $studentAuto->classroom_id);
        $this->assertEquals('XII RPL 3', $studentAuto->class_name);
        $this->assertNotNull($studentAuto->classroom);
        $this->assertEquals($customRombel->id, $studentAuto->classroom->id);
    }

    public function test_classroom_major_and_guru_bk_relations()
    {
        $this->seed();

        $admin = \App\Models\User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = \App\Models\User::factory()->create(['role' => 'admin', 'email' => 'admin@bkprima.sch.id']);
        }
        $this->actingAs($admin);

        // Pastikan jurusan dan guru tersedia
        $major = \App\Models\Major::firstOrCreate(
            ['code' => 'RPL'],
            ['name' => 'Rekayasa Perangkat Lunak', 'description' => 'Software engineering']
        );
        $guru = \App\Models\User::firstOrCreate(
            ['email' => 'nurul.hidayati@bkprima.sch.id'],
            [
                'name' => 'Dra. Hj. Nurul Hidayati, M.Pd',
                'password' => bcrypt('secret123'),
                'role' => 'guru',
            ]
        );
        $this->assertNotNull($major);
        $this->assertNotNull($guru);

        // 1. Simpan Kelas Baru via storeKelas
        $response = $this->post('/admin/kelas', [
            'name' => 'X RPL 99',
            'level' => 'Kelas 10 (X)',
            'jurusan_id' => $major->id,
            'jurusan' => $major->code,
            'waliKelas' => 'Bpk. Ahmad Santoso, S.Pd',
            'guruBkId' => $guru->id,
        ]);
        $response->assertStatus(302);

        $classroom = \App\Models\Classroom::where('name', 'X RPL 99')->first();
        $this->assertNotNull($classroom);
        $this->assertEquals($major->id, $classroom->jurusan_id);
        $this->assertEquals($guru->id, $classroom->guru_bk_id);
        $this->assertEquals($guru->name, $classroom->guru_bk_name);

        // Pastikan relasi model Eloquent berfungsi dua arah
        $this->assertNotNull($classroom->jurusan);
        $this->assertEquals($major->code, $classroom->jurusan->code);
        $this->assertTrue($major->classrooms->contains('id', $classroom->id));

        $this->assertNotNull($classroom->guruBk);
        $this->assertEquals($guru->name, $classroom->guruBk->name);

        // 2. Test updateKelas
        $updateResponse = $this->put('/admin/kelas/' . $classroom->id, [
            'name' => 'X RPL 100',
            'level' => 'Kelas 10 (X)',
            'jurusan_id' => $major->id,
            'jurusan' => $major->code,
            'waliKelas' => 'Ibu Siti Aminah, M.Kom',
            'guruBkId' => $guru->id,
        ]);
        $updateResponse->assertStatus(302);

        $classroom->refresh();
        $this->assertEquals('X RPL 100', $classroom->name);
        $this->assertEquals('Ibu Siti Aminah, M.Kom', $classroom->wali_kelas);

        // 3. Pastikan halaman AdminDashboard mengirimkan props initialJurusanList dan initialKelasList dengan relasi
        $dashResponse = $this->get('/admin');
        $dashResponse->assertStatus(200);
        $dashProps = $dashResponse->viewData('page')['props'] ?? [];
        $this->assertArrayHasKey('initialJurusanList', $dashProps);
        $this->assertArrayHasKey('initialKelasList', $dashProps);
    }
}
