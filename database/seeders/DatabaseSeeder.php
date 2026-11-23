<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;
use App\Models\Major;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Super Administrator Utama
        User::updateOrCreate(
            ['email' => 'admin@bkprima.sch.id'],
            [
                'name' => 'Admin IT Sekolah',
                'username' => 'admin_bkprima',
                'role' => 'admin',
                'password' => Hash::make('admin2026!'),
            ]
        );

        // 2. Akun Guru BK Damar
        $damar = User::updateOrCreate(
            ['username' => 'damar'],
            [
                'name' => 'Damar',
                'email' => 'damar@bkprima.sch.id',
                'username' => 'damar',
                'role' => 'guru',
                'jabatan' => 'Guru BK',
                'phone' => '08123456789',
                'password' => Hash::make('damar123'),
                'rombel' => 'X TKKR 1, X RPL 1, XI RPL 1, XI TSM 1, XII RPL 1',
            ]
        );

        // 3. Master Data Jurusan
        $majorsData = [
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak / PPLG', 'description' => 'Pengembangan software, aplikasi mobile & web'],
            ['code' => 'TKKR', 'name' => 'Teknik Kendaraan Ringan (TKRO)', 'description' => 'Teknik otomotif kendaraan roda empat & mesin'],
            ['code' => 'TSM', 'name' => 'Teknik Sepeda Motor', 'description' => 'Teknik otomotif kendaraan roda dua & mesin motor'],
            ['code' => 'DKV', 'name' => 'Desain Komunikasi Visual', 'description' => 'Kreatif grafis, animasi, & multimedia'],
            ['code' => 'AKL', 'name' => 'Akuntansi & Keuangan Lembaga', 'description' => 'Keuangan, perbankan, & perpajakan'],
            ['code' => 'OTKP', 'name' => 'Otomatisasi & Tata Kelola Perkantoran', 'description' => 'Administrasi perkantoran & manajemen bisnis'],
        ];

        $majorMap = [];
        foreach ($majorsData as $m) {
            $majorObj = Major::updateOrCreate(['code' => $m['code']], $m);
            $majorMap[$m['code']] = $majorObj->id;
        }

        // 4. Data Rombel Kelas
        $classes = [
            ['name' => 'X TKKR 1', 'level' => 'Kelas 10 (X)', 'major' => 'TKKR', 'jurusan_id' => $majorMap['TKKR'] ?? null, 'wali_kelas' => 'Bpk. Ahmad Fauzi, S.Kom', 'guru_bk_id' => $damar->id, 'guru_bk_name' => $damar->name],
            ['name' => 'X RPL 1', 'level' => 'Kelas 10 (X)', 'major' => 'RPL', 'jurusan_id' => $majorMap['RPL'] ?? null, 'wali_kelas' => 'Ibu Siti Aminah, S.Pd', 'guru_bk_id' => $damar->id, 'guru_bk_name' => $damar->name],
            ['name' => 'XI RPL 1', 'level' => 'Kelas 11 (XI)', 'major' => 'RPL', 'jurusan_id' => $majorMap['RPL'] ?? null, 'wali_kelas' => 'Bpk. Budi Santoso, S.Kom', 'guru_bk_id' => $damar->id, 'guru_bk_name' => $damar->name],
            ['name' => 'XI TSM 1', 'level' => 'Kelas 11 (XI)', 'major' => 'TSM', 'jurusan_id' => $majorMap['TSM'] ?? null, 'wali_kelas' => 'Bpk. Heru Wibowo, S.T', 'guru_bk_id' => $damar->id, 'guru_bk_name' => $damar->name],
            ['name' => 'XII RPL 1', 'level' => 'Kelas 12 (XII)', 'major' => 'RPL', 'jurusan_id' => $majorMap['RPL'] ?? null, 'wali_kelas' => 'Ibu Ratna Sari, M.Pd', 'guru_bk_id' => $damar->id, 'guru_bk_name' => $damar->name],
        ];

        $classMap = [];
        foreach ($classes as $c) {
            $classObj = Classroom::updateOrCreate(['name' => $c['name']], $c);
            $classMap[$c['name']] = $classObj->id;
        }

        // 5. Data Siswa
        $students = [
            ['nis' => '00829101', 'name' => 'Muhammad Bagas Saputra', 'class_name' => 'X TKKR 1', 'classroom_id' => $classMap['X TKKR 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'L', 'parent_name' => 'Orang Tua Muhammad Bagas', 'parent_phone' => '+62 812-3344-5566', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '00829102', 'name' => 'Nabila Putri Ayu', 'class_name' => 'X TKKR 1', 'classroom_id' => $classMap['X TKKR 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'P', 'parent_name' => 'Orang Tua Nabila Putri', 'parent_phone' => '+62 813-7788-9900', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '00829103', 'name' => 'Rendy Wahyu Pratama', 'class_name' => 'X TKKR 1', 'classroom_id' => $classMap['X TKKR 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'L', 'parent_name' => 'Orang Tua Rendy Wahyu', 'parent_phone' => '+62 856-1122-3344', 'total_violation_points' => 5, 'status' => 'aktif'],
            ['nis' => '0061234561', 'name' => 'Ahmad Fajar Prasetya', 'class_name' => 'X RPL 1', 'classroom_id' => $classMap['X RPL 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Bambang Prasetya', 'parent_phone' => '081298765432', 'total_violation_points' => 15, 'status' => 'aktif'],
            ['nis' => '0061234562', 'name' => 'Bayu Pratama Nugraha', 'class_name' => 'X RPL 1', 'classroom_id' => $classMap['X RPL 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Joko Nugraha', 'parent_phone' => '081298765433', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '0061234563', 'name' => 'Cantika Dewi Lestari', 'class_name' => 'X RPL 1', 'classroom_id' => $classMap['X RPL 1'] ?? null, 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'P', 'parent_name' => 'Hartono', 'parent_phone' => '081298765434', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '0061234564', 'name' => 'Dimas Arya Putra', 'class_name' => 'XI RPL 1', 'classroom_id' => $classMap['XI RPL 1'] ?? null, 'rombel' => '1', 'level' => '11', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Gunawan', 'parent_phone' => '081298765435', 'total_violation_points' => 35, 'status' => 'aktif'],
            ['nis' => '0061234565', 'name' => 'Eka Rizky Wahyudi', 'class_name' => 'XI RPL 1', 'classroom_id' => $classMap['XI RPL 1'] ?? null, 'rombel' => '1', 'level' => '11', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Wahyudi', 'parent_phone' => '081298765436', 'total_violation_points' => 10, 'status' => 'aktif'],
            ['nis' => '0061234566', 'name' => 'Farhan Maulana', 'class_name' => 'XI TSM 1', 'classroom_id' => $classMap['XI TSM 1'] ?? null, 'rombel' => '1', 'level' => '11', 'major' => 'TSM', 'gender' => 'L', 'parent_name' => 'Suwito', 'parent_phone' => '081298765437', 'total_violation_points' => 50, 'status' => 'aktif'],
            ['nis' => '0061234567', 'name' => 'Gita Puspitasari', 'class_name' => 'XII RPL 1', 'classroom_id' => $classMap['XII RPL 1'] ?? null, 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'P', 'parent_name' => 'Surya Pratama', 'parent_phone' => '081298765438', 'total_violation_points' => 0, 'status' => 'aktif'],
        ];

        foreach ($students as $s) {
            $student = Student::updateOrCreate(['nis' => $s['nis']], $s);
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => now()->toDateString()],
                ['status' => 'Hadir', 'check_in_time' => '06:45', 'class_snapshot' => $student->class_name, 'notes' => 'Tepat waktu']
            );
        }

        // 6. Konfigurasi Dasar Pengaturan Sekolah
        $defaultSettings = [
            'school_name' => 'SMK PGRI 05 JEMBER',
            'school_address' => 'Jl. Krakatau No. 54 Tanggul, Jember - Jawa Timur',
            'school_npsn' => '20523801',
            'school_status' => 'Terakreditasi A (Unggul)',
            'school_email' => 'smkpgri5jember@yahoo.co.id',
            'school_website' => 'www.smkpgri05jember.sch.id',
            'headmaster_name' => 'Drs. H. Saiful Bahri, M.Pd',
            'headmaster_nip' => '196802151994031003',
            'academic_year' => '2026/2027',
            'semester' => 'Semester Genap 2026/2027',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
