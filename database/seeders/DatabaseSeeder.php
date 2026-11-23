<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;
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
        User::updateOrCreate(
            ['username' => 'damar'],
            [
                'name' => 'Damar',
                'email' => 'damar@bkprima.sch.id',
                'username' => 'damar',
                'role' => 'guru',
                'jabatan' => 'Guru BK',
                'phone' => '08123456789',
                'password' => Hash::make('damar123'),
                'rombel' => 'Semua Rombel',
            ]
        );

        // 3. Data Kelas & Siswa
        $classes = [
            ['name' => 'X TKKR 1', 'level' => '10', 'major' => 'TKKR', 'wali_kelas' => 'Bpk. Ahmad Fauzi, S.Kom', 'guru_bk_name' => 'Damar'],
            ['name' => 'X RPL 1', 'level' => '10', 'major' => 'RPL', 'wali_kelas' => 'Ibu Siti Aminah, S.Pd', 'guru_bk_name' => 'Damar'],
            ['name' => 'XI RPL 1', 'level' => '11', 'major' => 'RPL', 'wali_kelas' => 'Bpk. Budi Santoso, S.Kom', 'guru_bk_name' => 'Damar'],
            ['name' => 'XI TSM 1', 'level' => '11', 'major' => 'TSM', 'wali_kelas' => 'Bpk. Heru Wibowo, S.T', 'guru_bk_name' => 'Damar'],
            ['name' => 'XII RPL 1', 'level' => '12', 'major' => 'RPL', 'wali_kelas' => 'Ibu Ratna Sari, M.Pd', 'guru_bk_name' => 'Damar'],
        ];

        foreach ($classes as $c) {
            \App\Models\Classroom::updateOrCreate(['name' => $c['name']], $c);
        }

        $students = [
            ['nis' => '00829101', 'name' => 'Muhammad Bagas Saputra', 'class_name' => 'X TKKR 1', 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'L', 'parent_name' => 'Orang Tua Muhammad Bagas', 'parent_phone' => '+62 812-3344-5566', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '00829102', 'name' => 'Nabila Putri Ayu', 'class_name' => 'X TKKR 1', 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'P', 'parent_name' => 'Orang Tua Nabila Putri', 'parent_phone' => '+62 813-7788-9900', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '00829103', 'name' => 'Rendy Wahyu Pratama', 'class_name' => 'X TKKR 1', 'rombel' => '1', 'level' => '10', 'major' => 'TKKR', 'gender' => 'L', 'parent_name' => 'Orang Tua Rendy Wahyu', 'parent_phone' => '+62 856-1122-3344', 'total_violation_points' => 5, 'status' => 'aktif'],
            ['nis' => '0061234561', 'name' => 'Ahmad Fajar Prasetya', 'class_name' => 'X RPL 1', 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Bambang Prasetya', 'parent_phone' => '081298765432', 'total_violation_points' => 15, 'status' => 'aktif'],
            ['nis' => '0061234562', 'name' => 'Bayu Pratama Nugraha', 'class_name' => 'X RPL 1', 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Joko Nugraha', 'parent_phone' => '081298765433', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '0061234563', 'name' => 'Cantika Dewi Lestari', 'class_name' => 'X RPL 1', 'rombel' => '1', 'level' => '10', 'major' => 'RPL', 'gender' => 'P', 'parent_name' => 'Hartono', 'parent_phone' => '081298765434', 'total_violation_points' => 0, 'status' => 'aktif'],
            ['nis' => '0061234564', 'name' => 'Dimas Arya Putra', 'class_name' => 'XI RPL 1', 'rombel' => '1', 'level' => '11', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Gunawan', 'parent_phone' => '081298765435', 'total_violation_points' => 35, 'status' => 'aktif'],
            ['nis' => '0061234565', 'name' => 'Eka Rizky Wahyudi', 'class_name' => 'XI RPL 1', 'rombel' => '1', 'level' => '11', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Wahyudi', 'parent_phone' => '081298765436', 'total_violation_points' => 10, 'status' => 'aktif'],
            ['nis' => '0061234566', 'name' => 'Farhan Maulana', 'class_name' => 'XI TSM 1', 'rombel' => '1', 'level' => '11', 'major' => 'TSM', 'gender' => 'L', 'parent_name' => 'Suwito', 'parent_phone' => '081298765437', 'total_violation_points' => 50, 'status' => 'aktif'],
            ['nis' => '0061234567', 'name' => 'Gita Puspitasari', 'class_name' => 'XII RPL 1', 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'P', 'parent_name' => 'Surya Pratama', 'parent_phone' => '081298765438', 'total_violation_points' => 0, 'status' => 'aktif'],
        ];

        foreach ($students as $s) {
            $student = \App\Models\Student::updateOrCreate(['nis' => $s['nis']], $s);
            \App\Models\Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => now()->toDateString()],
                ['status' => 'Hadir', 'check_in_time' => '06:45', 'class_snapshot' => $student->class_name, 'notes' => 'Tepat waktu']
            );
        }

        // 4. Data Arsip Siswa Lulus (Alumni)
        $alumniData = [
            ['nis' => '00511001', 'name' => 'Rian Hidayat, S.Kom', 'class_name' => 'XII RPL 1', 'last_class_name' => 'XII RPL 1', 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Hidayatullah', 'parent_phone' => '081234567801', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2025, 'academic_year' => '2024/2025'],
            ['nis' => '00511002', 'name' => 'Siti Nurhaliza', 'class_name' => 'XII RPL 1', 'last_class_name' => 'XII RPL 1', 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'P', 'parent_name' => 'H. Halim Santoso', 'parent_phone' => '081234567802', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2025, 'academic_year' => '2024/2025'],
            ['nis' => '00511003', 'name' => 'Aditya Pratama Putra', 'class_name' => 'XII TSM 1', 'last_class_name' => 'XII TSM 1', 'rombel' => '1', 'level' => '12', 'major' => 'TSM', 'gender' => 'L', 'parent_name' => 'Slamet Widodo', 'parent_phone' => '081234567803', 'total_violation_points' => 15, 'status' => 'lulus', 'graduation_year' => 2025, 'academic_year' => '2024/2025'],
            ['nis' => '00511004', 'name' => 'Dwi Lestari Handayani', 'class_name' => 'XII TKKR 1', 'last_class_name' => 'XII TKKR 1', 'rombel' => '1', 'level' => '12', 'major' => 'TKKR', 'gender' => 'P', 'parent_name' => 'Bambang Irawan', 'parent_phone' => '081234567804', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2025, 'academic_year' => '2024/2025'],
            ['nis' => '00411005', 'name' => 'Fajar Ramadhan', 'class_name' => 'XII RPL 1', 'last_class_name' => 'XII RPL 1', 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Agus Ramadhan', 'parent_phone' => '081234567805', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2024, 'academic_year' => '2023/2024'],
            ['nis' => '00411006', 'name' => 'Hendra Setiawan', 'class_name' => 'XII TSM 1', 'last_class_name' => 'XII TSM 1', 'rombel' => '1', 'level' => '12', 'major' => 'TSM', 'gender' => 'L', 'parent_name' => 'Supriyadi', 'parent_phone' => '081234567806', 'total_violation_points' => 25, 'status' => 'lulus', 'graduation_year' => 2024, 'academic_year' => '2023/2024'],
            ['nis' => '00411007', 'name' => 'Maya Anggraini', 'class_name' => 'XII TKKR 1', 'last_class_name' => 'XII TKKR 1', 'rombel' => '1', 'level' => '12', 'major' => 'TKKR', 'gender' => 'P', 'parent_name' => 'Mulyadi', 'parent_phone' => '081234567807', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2024, 'academic_year' => '2023/2024'],
            ['nis' => '00611008', 'name' => 'Rizky Fauzi Rahman', 'class_name' => 'XII RPL 1', 'last_class_name' => 'XII RPL 1', 'rombel' => '1', 'level' => '12', 'major' => 'RPL', 'gender' => 'L', 'parent_name' => 'Fauzi Rahman', 'parent_phone' => '081234567808', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2026, 'academic_year' => '2025/2026'],
            ['nis' => '00611009', 'name' => 'Yuliana Sari Dewi', 'class_name' => 'XII TSM 1', 'last_class_name' => 'XII TSM 1', 'rombel' => '1', 'level' => '12', 'major' => 'TSM', 'gender' => 'P', 'parent_name' => 'Dwi Santoso', 'parent_phone' => '081234567809', 'total_violation_points' => 0, 'status' => 'lulus', 'graduation_year' => 2026, 'academic_year' => '2025/2026'],
        ];

        foreach ($alumniData as $alumni) {
            \App\Models\Student::updateOrCreate(['nis' => $alumni['nis']], $alumni);
        }

        // 5. Konfigurasi Dasar Pengaturan Sekolah
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
