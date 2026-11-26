<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Major;
use Carbon\Carbon;

class DummyAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Jurusan
        $majors = [
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL'],
            ['name' => 'Teknik Kendaraan Ringan', 'code' => 'TKKR'],
            ['name' => 'Akuntansi dan Keuangan Lembaga', 'code' => 'AKL'],
        ];

        foreach ($majors as $m) {
            Major::updateOrCreate(['code' => $m['code']], $m);
        }

        // Kelas
        $classrooms = [
            ['name' => 'X RPL 1',   'level' => 'Kelas 10 (X)',   'major' => 'RPL',  'wali_kelas' => 'Siti Aminah, S.Kom.'],
            ['name' => 'X RPL 2',   'level' => 'Kelas 10 (X)',   'major' => 'RPL',  'wali_kelas' => 'Budi Hartono, S.T.'],
            ['name' => 'X TKKR',    'level' => 'Kelas 10 (X)',   'major' => 'TKKR', 'wali_kelas' => 'Agus Supriadi, S.Pd.'],
            ['name' => 'XI RPL 1',  'level' => 'Kelas 11 (XI)',  'major' => 'RPL',  'wali_kelas' => 'Dewi Ratnasari, S.Pd.'],
            ['name' => 'XI AKL 1',  'level' => 'Kelas 11 (XI)',  'major' => 'AKL',  'wali_kelas' => 'Rina Wulandari, S.E.'],
            ['name' => 'XII RPL 1', 'level' => 'Kelas 12 (XII)', 'major' => 'RPL',  'wali_kelas' => 'Hendra Saputra, S.Kom.'],
        ];

        foreach ($classrooms as $c) {
            Classroom::updateOrCreate(['name' => $c['name']], $c);
        }

        // Daftar nama unik (tidak ada duplikasi antar siswa)
        $maleNames = [
            'Ahmad Fauzi', 'Bayu Setiawan', 'Dimas Pratama', 'Eko Wahyudi', 'Fajar Ramadhan',
            'Galih Permana', 'Haris Maulana', 'Ilham Saputra', 'Joko Susilo', 'Kevin Ardiansyah',
            'Lukman Hakim', 'Miftahul Huda', 'Naufal Rizki', 'Oscar Firmansyah', 'Putra Aditya',
            'Rafi Hidayat', 'Satrio Wibowo', 'Taufik Hidayat', 'Umar Faruq', 'Vino Dwi Cahyo',
            'Wahyu Nugroho', 'Yoga Pratama', 'Zaki Mubarok', 'Aldi Kurniawan', 'Bagus Tri Wicaksono',
            'Candra Wijaya', 'Dani Setiawan', 'Fikri Ramadhan', 'Gilang Mahardika', 'Hendri Saputra',
            'Adi Nugraha', 'Bima Sakti', 'Dafa Prasetyo', 'Erwin Hidayat', 'Farhan Kamil',
            'Guntur Wibisono', 'Hafiz Alamsyah', 'Irfan Hakim', 'Jihan Pratama', 'Kurnia Sandi',
            'Lutfi Hakim', 'Maulana Yusuf', 'Nanda Kurnia', 'Oki Setiawan', 'Panji Laksono',
            'Qomarudin Hasan', 'Reza Pahlevi', 'Syahrul Gunawan', 'Tegar Saputra', 'Ubaydillah Rahman',
            'Vicky Hermawan', 'Wildan Firdaus', 'Yusuf Bahtiar', 'Zainal Abidin', 'Arif Rahman',
            'Bastian Putra', 'Dicky Firmansyah', 'Evan Kurniawan', 'Ferry Anggara', 'Gio Pratama',
        ];

        $femaleNames = [
            'Ayu Lestari', 'Bunga Citra', 'Citra Dewi', 'Dina Mariana', 'Eka Putri',
            'Fitri Handayani', 'Gita Savitri', 'Hana Pertiwi', 'Indah Permata', 'Jasmine Azzahra',
            'Kartika Sari', 'Lina Marlina', 'Maya Anggraeni', 'Nisa Aulia', 'Oktavia Rahmawati',
            'Putri Wulandari', 'Qonita Zahra', 'Rani Kusuma', 'Sinta Bella', 'Tia Anggraini',
            'Umi Kalsum', 'Vina Melati', 'Winda Sari', 'Yuni Astuti', 'Zahra Nabilah',
            'Amira Salsa', 'Bella Puspita', 'Clara Permata', 'Devi Kurnia', 'Elsa Safitri',
            'Farah Nabila', 'Ghea Ananda', 'Hesti Rahayu', 'Intan Permatasari', 'Julia Pratiwi',
        ];

        $maleIdx = 0;
        $femaleIdx = 0;

        // Sinkronkan siswa existing agar level/major/rombel sesuai master Classroom
        $classModels = Classroom::orderBy('id')->get();
        foreach ($classModels as $cls) {
            $shortLevel = match (true) {
                str_starts_with(strtoupper($cls->name), 'XII ') => '12',
                str_starts_with(strtoupper($cls->name), 'XI ')  => '11',
                default => '10',
            };
            $parts = preg_split('/\s+/', trim($cls->name));
            $rombelCode = count($parts) >= 3 ? end($parts) : '1';

            Student::active()
                ->where('class_name', $cls->name)
                ->update([
                    'classroom_id' => $cls->id,
                    'level' => $shortLevel,
                    'major' => $cls->major,
                    'rombel' => $rombelCode,
                ]);
        }

        // Hapus siswa dummy sebelumnya (NIS 00820xxx) agar penomoran absen & nama bersih tanpa duplikat
        $oldDummyIds = Student::where('nis', 'like', '00820%')->pluck('id');
        if ($oldDummyIds->isNotEmpty()) {
            Attendance::whereIn('student_id', $oldDummyIds)->delete();
            Student::whereIn('id', $oldDummyIds)->delete();
        }

        // Generate 15 siswa baru per kelas (melanjutkan no_absen siswa existing di kelas tsb)
        $allStudents = collect();

        foreach ($classModels as $classIndex => $cls) {
            $shortLevel = match (true) {
                str_starts_with(strtoupper($cls->name), 'XII ') => '12',
                str_starts_with(strtoupper($cls->name), 'XI ')  => '11',
                default => '10',
            };
            $parts = preg_split('/\s+/', trim($cls->name));
            $rombelCode = count($parts) >= 3 ? end($parts) : '1';

            $existingMaxAbsen = (int) (Student::active()
                ->where('class_name', $cls->name)
                ->max('no_absen') ?? 0);

            $targetNewCount = 15;
            for ($i = 1; $i <= $targetNewCount; $i++) {
                $gender = $i % 3 === 0 ? 'P' : 'L';
                if ($gender === 'L') {
                    $name = $maleNames[$maleIdx % count($maleNames)];
                    $maleIdx++;
                } else {
                    $name = $femaleNames[$femaleIdx % count($femaleNames)];
                    $femaleIdx++;
                }

                $nis = '00820' . ($classIndex + 1) . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $noAbsen = $existingMaxAbsen + $i;

                $student = Student::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'name' => $name,
                        'class_name' => $cls->name,
                        'classroom_id' => $cls->id,
                        'level' => $shortLevel,
                        'major' => $cls->major,
                        'rombel' => $rombelCode,
                        'gender' => $gender,
                        'no_absen' => $noAbsen,
                        'parent_phone' => '08' . rand(10, 99) . rand(1000, 9999) . rand(1000, 9999),
                        'parent_name' => 'Wali ' . explode(' ', $name)[0],
                        'status' => 'aktif',
                    ]
                );

                $allStudents->push($student);
            }
        }

        // Sertakan juga siswa aktif non-dummy agar grafik/presensi hari ini terisi lengkap
        $seededIds = $allStudents->pluck('id')->all();
        $extraActiveStudents = Student::active()->whereNotIn('id', $seededIds)->get();
        $allStudents = $allStudents->merge($extraActiveStudents);

        // Generate attendance for the last 30 weekdays
        $today = Carbon::today();
        $dates = collect();

        $d = $today->copy();
        while ($dates->count() < 30) {
            if ($d->isWeekday()) {
                $dates->push($d->copy());
            }
            $d->subDay();
        }

        $dateStrings = $dates->map(fn ($dt) => $dt->toDateString())->all();
        Attendance::whereIn('student_id', $allStudents->pluck('id'))
            ->whereIn('date', $dateStrings)
            ->delete();

        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'];

        // Weight: Hadir 70%, Terlambat 12%, Izin 7%, Sakit 6%, Alpa 5%
        $weights = [70, 12, 7, 6, 5];

        $records = [];
        $now = Carbon::now()->toDateTimeString();

        foreach ($allStudents as $student) {
            foreach ($dates as $date) {
                $roll = rand(1, 100);
                $cumulative = 0;
                $status = 'Hadir';
                foreach ($statuses as $idx => $s) {
                    $cumulative += $weights[$idx];
                    if ($roll <= $cumulative) {
                        $status = $s;
                        break;
                    }
                }

                $checkIn = null;
                $checkOut = null;
                $notes = null;

                if ($status === 'Hadir') {
                    $checkIn = sprintf('%02d:%02d', 6, rand(30, 55));
                    $checkOut = sprintf('%02d:%02d', rand(14, 15), rand(0, 30));
                } elseif ($status === 'Terlambat') {
                    $checkIn = sprintf('%02d:%02d', 7, rand(5, 45));
                    $checkOut = sprintf('%02d:%02d', rand(14, 15), rand(0, 30));
                    $notes = 'Terlambat masuk';
                } elseif ($status === 'Izin') {
                    $notes = 'Izin keperluan keluarga';
                } elseif ($status === 'Sakit') {
                    $notes = 'Sakit (surat keterangan dokter)';
                }

                $records[] = [
                    'student_id' => $student->id,
                    'class_snapshot' => $student->class_name,
                    'date' => $date->toDateString(),
                    'check_in_time' => $checkIn,
                    'check_out_time' => $checkOut,
                    'status' => $status,
                    'notes' => $notes,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Batch insert (chunks of 500)
        foreach (array_chunk($records, 500) as $chunk) {
            Attendance::insert($chunk);
        }

        $this->command->info('Dummy data created: ' . $allStudents->count() . ' students, ' . count($records) . ' attendance records across 30 days.');
    }
}
