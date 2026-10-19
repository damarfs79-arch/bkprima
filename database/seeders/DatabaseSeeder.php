<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Violation;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Admin & Guru BK)
        User::firstOrCreate(
            ['email' => 'admin@bkprima.sch.id'],
            [
                'name' => 'Admin IT Sekolah',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'nurul.hidayati@bkprima.sch.id'],
            [
                'name' => 'Dra. Hj. Nurul Hidayati, M.Pd',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Create Students
        $studentsData = [
            [
                'nisn' => '0061234561',
                'name' => 'Ahmad Fajar Pratama',
                'class_name' => 'X RPL 1',
                'level' => '10',
                'major' => 'RPL',
                'gender' => 'L',
                'phone' => '081234567890',
                'parent_name' => 'Bambang Sugeng',
                'parent_phone' => '081298765432',
                'total_violation_points' => 15,
                'status' => 'Peringatan 1',
            ],
            [
                'nisn' => '0061234562',
                'name' => 'Budi Santoso',
                'class_name' => 'X RPL 1',
                'level' => '10',
                'major' => 'RPL',
                'gender' => 'L',
                'phone' => '081234567891',
                'parent_name' => 'Santoso',
                'parent_phone' => '081298765433',
                'total_violation_points' => 5,
                'status' => 'Aman',
            ],
            [
                'nisn' => '0061234563',
                'name' => 'Citra Dewi Lestari',
                'class_name' => 'X RPL 1',
                'level' => '10',
                'major' => 'RPL',
                'gender' => 'P',
                'phone' => '081234567892',
                'parent_name' => 'Hartono',
                'parent_phone' => '081298765434',
                'total_violation_points' => 0,
                'status' => 'Aman',
            ],
            [
                'nisn' => '0061234564',
                'name' => 'Dimas Arya Putra',
                'class_name' => 'X TSM 1',
                'level' => '10',
                'major' => 'TSM',
                'gender' => 'L',
                'phone' => '081234567893',
                'parent_name' => 'Supriyadi',
                'parent_phone' => '081298765435',
                'total_violation_points' => 45,
                'status' => 'SP 1',
            ],
            [
                'nisn' => '0061234565',
                'name' => 'Eka Rizky Wahyudi',
                'class_name' => 'XI RPL 1',
                'level' => '11',
                'major' => 'RPL',
                'gender' => 'L',
                'phone' => '081234567894',
                'parent_name' => 'Wahyudi',
                'parent_phone' => '081298765436',
                'total_violation_points' => 25,
                'status' => 'Peringatan 2',
            ],
            [
                'nisn' => '0061234566',
                'name' => 'Farhan Maulana',
                'class_name' => 'XI TSM 1',
                'level' => '11',
                'major' => 'TSM',
                'gender' => 'L',
                'phone' => '081234567895',
                'parent_name' => 'Suwito',
                'parent_phone' => '081298765437',
                'total_violation_points' => 75,
                'status' => 'Panggilan Orang Tua',
            ],
            [
                'nisn' => '0061234567',
                'name' => 'Gita Puspitasari',
                'class_name' => 'XII RPL 1',
                'level' => '12',
                'major' => 'RPL',
                'gender' => 'P',
                'phone' => '081234567896',
                'parent_name' => 'Surya Pratama',
                'parent_phone' => '081298765438',
                'total_violation_points' => 0,
                'status' => 'Aman',
            ],
        ];

        foreach ($studentsData as $item) {
            $student = Student::updateOrCreate(['nisn' => $item['nisn']], $item);

            // Attendance today
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => now()->toDateString(),
                ],
                [
                    'check_in_time' => '06:45:10',
                    'check_out_time' => '14:30:00',
                    'status' => 'Hadir',
                    'notes' => 'Tepat waktu via mesin HadirMu',
                ]
            );
        }

        // 3. Seed sample violations
        $studentFajar = Student::where('nisn', '0061234561')->first();
        if ($studentFajar) {
            Violation::create([
                'student_id' => $studentFajar->id,
                'student_name' => $studentFajar->name,
                'student_class' => $studentFajar->class_name,
                'category' => 'Kedisiplinan',
                'violation_name' => 'Terlambat Masuk Sekolah (> 15 Menit)',
                'points' => 5,
                'date' => now()->subDays(2)->toDateString(),
                'time' => '07:18',
                'reporter' => 'Bpk. Slamet Riyadi, S.T (Piket)',
                'penalty_action' => 'Piket membersihkan halaman & pembinaan',
                'status' => 'Selesai',
                'notes' => 'Ban bocor di jalan, sudah lapor orang tua.',
            ]);

            Violation::create([
                'student_id' => $studentFajar->id,
                'student_name' => $studentFajar->name,
                'student_class' => $studentFajar->class_name,
                'category' => 'Kerapian',
                'violation_name' => 'Rambut Tidak Sesuai Ketentuan (Melebihi Kerah)',
                'points' => 10,
                'date' => now()->subDay()->toDateString(),
                'time' => '07:30',
                'reporter' => 'Dra. Hj. Nurul Hidayati, M.Pd (BK)',
                'penalty_action' => 'Diberi tenggat waktu 3 hari untuk merapikan rambut',
                'status' => 'Ditindaklanjuti',
                'notes' => 'Diingatkan secara lisan dan dicatat pada buku pembinaan.',
            ]);
        }

        $studentFarhan = Student::where('nisn', '0061234566')->first();
        if ($studentFarhan) {
            Violation::create([
                'student_id' => $studentFarhan->id,
                'student_name' => $studentFarhan->name,
                'student_class' => $studentFarhan->class_name,
                'category' => 'Kepribadian & Berat',
                'violation_name' => 'Merokok / Membawa Rokok di Lingkungan Sekolah',
                'points' => 50,
                'date' => now()->subDays(3)->toDateString(),
                'time' => '10:15',
                'reporter' => 'Bpk. Agus Wicaksono, S.Pd',
                'penalty_action' => 'Surat Pemanggilan Orang Tua Tahap 1 & Skorsing 3 Hari',
                'status' => 'Ditindaklanjuti',
                'notes' => 'Ditemukan di area belakang bengkel TSM saat jam istirahat pertama.',
            ]);
        }
    }
}
