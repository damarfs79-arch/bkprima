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
                'name' => 'Admin BK',
                'username' => 'admin_bkprima',
                'role' => 'admin',
                'password' => Hash::make('admin2026!'),
            ]
        );

        // 2. Akun Guru BK Default (tanpa rombel dummy)
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
                'rombel' => '',
            ]
        );

        // 3. Konfigurasi Dasar Pengaturan Sekolah
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
