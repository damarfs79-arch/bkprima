<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Major;

class AttendanceController extends Controller
{
    private function getAuthorizedClasses(): array
    {
        $user = Auth::user();
        if ($user && $user->role === 'guru') {
            $fromClassrooms = Classroom::where('guru_bk_id', $user->id)
                ->orWhere('guru_bk_name', $user->name)
                ->pluck('name')->toArray();
            
            $fromUserRombel = [];
            if (!empty($user->rombel)) {
                $decoded = json_decode($user->rombel, true);
                if (is_array($decoded)) {
                    $fromUserRombel = $decoded;
                } else {
                    $fromUserRombel = array_map('trim', explode(',', $user->rombel));
                }
            }

            $guruRombels = array_values(array_unique(array_filter(array_merge($fromClassrooms, $fromUserRombel))));
            if (!empty($guruRombels)) {
                return $guruRombels;
            }
        }

        // Admin atau fallback semua kelas yang ada di database
        $dbClasses = Classroom::orderBy('name')->pluck('name')->toArray();
        if (empty($dbClasses)) {
            $dbClasses = Student::select('class_name')->distinct()->orderBy('class_name')->pluck('class_name')->toArray();
        }
        return array_values(array_unique($dbClasses));
    }

    public function index(Request $request)
    {
        $today = now()->toDateString();
        $authorizedClasses = $this->getAuthorizedClasses();

        $studentQuery = Student::active()->with(['attendances' => function ($q) use ($today) {
            $q->where('date', $today);
        }]);

        $user = Auth::user();
        if ($user && $user->role === 'guru' && !empty($authorizedClasses)) {
            $studentQuery->whereIn('class_name', $authorizedClasses);
        }

        $students = $studentQuery->orderBy('class_name')->orderBy('name')->get();
        $classes = $authorizedClasses;

        $visibleStudentIds = $students->pluck('id');

        $stats = [
            'totalStudents' => $students->count(),
            'presentToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Hadir')->count(),
            'lateToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Terlambat')->count(),
            'permitToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->whereIn('status', ['Izin', 'Sakit'])->count(),
            'alphaToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Alpa')->count(),
        ];

        $dbRombels = Classroom::orderBy('level')->orderBy('name')->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'nama_rombel' => $r->name,
                'level' => $r->level,
                'major' => $r->major,
                'wali_kelas' => $r->wali_kelas,
                'guru_bk_name' => $r->guru_bk_name,
            ];
        });

        return Inertia::render('AttendanceList', [
            'dbStudents' => $students,
            'classes' => $classes,
            'rombelList' => $dbRombels,
            'rombels' => $dbRombels,
            'stats' => $stats,
            'todayDate' => now()->translatedFormat('l, d M Y'),
            'initialFilters' => [
                'tingkat' => $request->query('tingkat', 'ALL'),
                'jurusan' => $request->query('jurusan', 'ALL'),
                'kelas' => $request->query('kelas', 'ALL'),
            ],
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpa',
            'check_in_time' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $today = now()->toDateString();
        $fallbackTime = match ($validated['status']) {
            'Hadir' => '06:45 WIB',
            'Terlambat' => '07:15 WIB',
            default => '-'
        };

        Attendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $today,
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => !empty($validated['check_in_time']) ? $validated['check_in_time'] : $fallbackTime,
                'notes' => !empty($validated['notes']) ? $validated['notes'] : ($validated['status'] === 'Hadir' ? 'Tepat Waktu' : $validated['status']),
            ]
        );

        return redirect()->back()->with('success', 'Status presensi siswa berhasil diperbarui!');
    }

    public function importStudents(Request $request)
    {
        $studentsData = $request->input('students');
        $targetRombelId = $request->input('target_rombel_id', 'AUTO');

        if (empty($studentsData) || !is_array($studentsData)) {
            return redirect()->back()->with('error', 'Tidak ada data siswa yang valid untuk diimpor.');
        }

        $user = Auth::user();
        $authorizedClasses = $this->getAuthorizedClasses();

        // Cari rombel spesifik jika dipilih di dropdown
        $specificClassroom = null;
        if (!empty($targetRombelId) && $targetRombelId !== 'AUTO') {
            $specificClassroom = is_numeric($targetRombelId)
                ? Classroom::find($targetRombelId)
                : Classroom::where('name', $targetRombelId)->first();
        }

        $importedCount = 0;
        $touchedClasses = [];

        foreach ($studentsData as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $targetClass = null;

            if ($specificClassroom) {
                // Mode spesifik: kaitkan ke rombel yang dipilih
                $targetClass = $specificClassroom;
            } else {
                // Mode Otomatis: cocokkan nama kelas spreadsheet dengan nama rombel di master data
                $className = !empty($item['className']) ? strtoupper(trim($item['className'])) : (!empty($authorizedClasses) ? $authorizedClasses[0] : 'X RPL 1');
                $targetClass = Classroom::where('name', $className)->first();

                if (!$targetClass) {
                    $parts = explode(' ', $className);
                    $level = '10';
                    if (!empty($parts[0])) {
                        $upper = strtoupper($parts[0]);
                        if ($upper === 'X') $level = '10';
                        elseif ($upper === 'XI') $level = '11';
                        elseif ($upper === 'XII') $level = '12';
                        elseif (is_numeric($parts[0])) $level = $parts[0];
                    }
                    $major = $parts[1] ?? 'RPL';

                    $targetClass = Classroom::create([
                        'name' => $className,
                        'level' => "Kelas {$level} ({$parts[0]})",
                        'major' => $major,
                        'wali_kelas' => 'Wali Kelas ' . $className,
                        'guru_bk_id' => $user ? $user->id : null,
                        'guru_bk_name' => $user ? $user->name : null,
                    ]);
                }
            }

            // Jika guru, pastikan kelas termasuk kelas binaannya
            if ($user && $user->role === 'guru' && !empty($authorizedClasses)) {
                if (!in_array($targetClass->name, $authorizedClasses)) {
                    continue; // Skip siswa di luar rombel binaan guru
                }
            }

            $className = $targetClass->name;
            $classId = $targetClass->id;
            $touchedClasses[] = $className;
            $parts = explode(' ', $className);
            $level = '10';
            if (!empty($parts[0])) {
                $upper = strtoupper($parts[0]);
                if ($upper === 'X') $level = '10';
                elseif ($upper === 'XI') $level = '11';
                elseif ($upper === 'XII') $level = '12';
                elseif (is_numeric($parts[0])) $level = $parts[0];
            }
            $major = $targetClass->major ?: ($parts[1] ?? 'RPL');

            $nis = !empty($item['nis']) ? trim((string)$item['nis']) : (!empty($item['nisn']) ? trim((string)$item['nisn']) : '00' . rand(10000000, 99999999));

            $student = Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'classroom_id' => $classId,
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $level,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => $item['parentWa'] ?? ($item['parent_phone'] ?? null),
                    'parent_name' => $item['parent_name'] ?? ('Orang Tua ' . trim($item['name'])),
                    'rombel' => end($parts),
                    'status' => 'aktif',
                ]
            );

            // Tambahkan record attendance hari ini jika belum ada
            Attendance::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => now()->toDateString(),
                ],
                [
                    'check_in_time' => '06:45 WIB',
                    'status' => 'Hadir',
                    'notes' => 'Tepat Waktu',
                ]
            );

            $importedCount++;
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$importedCount} data siswa ke rombel binaan Anda.");
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $nis = !empty($validated['nis']) ? trim($validated['nis']) : (!empty($validated['nisn']) ? trim($validated['nisn']) : '');
        if (empty($nis)) {
            return redirect()->back()->withErrors(['nis' => 'NIS wajib diisi.']);
        }
        if (Student::where('nis', $nis)->exists()) {
            return redirect()->back()->withErrors(['nis' => 'NIS sudah terdaftar. Silakan gunakan NIS lain.']);
        }

        $parts = explode(' ', trim($validated['class_name']));
        $level = '10';
        if (!empty($parts[0])) {
            $upper = strtoupper($parts[0]);
            if ($upper === 'X') $level = '10';
            elseif ($upper === 'XI') $level = '11';
            elseif ($upper === 'XII') $level = '12';
            elseif (is_numeric($parts[0])) $level = $parts[0];
        }
        $major = $parts[1] ?? 'Umum';

        Major::firstOrCreate(
            ['code' => $major],
            ['name' => $major, 'description' => 'Program Keahlian ' . $major]
        );

        $classroom = Classroom::firstOrCreate(
            ['name' => $validated['class_name']],
            [
                'level' => "Kelas {$level} ({$parts[0]})",
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $validated['class_name'],
            ]
        );

        $student = Student::create([
            'classroom_id' => $classroom->id,
            'nis' => $nis,
            'name' => $validated['name'],
            'class_name' => $validated['class_name'],
            'level' => $level,
            'major' => $major,
            'gender' => $validated['gender'],
            'parent_name' => $validated['parent_name'] ?? 'Bpk/Ibu ' . $validated['name'],
            'parent_phone' => $validated['parent_phone'] ?? '081234567890',
            'total_violation_points' => 0,
            'rombel' => end($parts),
            'status' => 'aktif',
        ]);

        // Default record attendance today
        Attendance::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'check_in_time' => '06:45 WIB',
            'status' => 'Hadir',
            'notes' => 'Tepat Waktu',
        ]);

        return redirect()->back()->with('success', 'Siswa baru berhasil ditambahkan!');
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $nis = !empty($validated['nis']) ? trim($validated['nis']) : (!empty($validated['nisn']) ? trim($validated['nisn']) : $student->nis);
        if (Student::where('nis', $nis)->where('id', '!=', $student->id)->exists()) {
            return redirect()->back()->withErrors(['nis' => 'NIS sudah digunakan oleh siswa lain.']);
        }

        $parts = explode(' ', trim($validated['class_name']));
        $level = $student->level;
        if (!empty($parts[0])) {
            $upper = strtoupper($parts[0]);
            if ($upper === 'X') $level = '10';
            elseif ($upper === 'XI') $level = '11';
            elseif ($upper === 'XII') $level = '12';
            elseif (is_numeric($parts[0])) $level = $parts[0];
        }
        $major = $parts[1] ?? $student->major;

        Major::firstOrCreate(
            ['code' => $major],
            ['name' => $major, 'description' => 'Program Keahlian ' . $major]
        );

        $classroom = Classroom::firstOrCreate(
            ['name' => $validated['class_name']],
            [
                'level' => "Kelas {$level} ({$parts[0]})",
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $validated['class_name'],
            ]
        );

        $student->update([
            'classroom_id' => $classroom->id,
            'nis' => $nis,
            'name' => $validated['name'],
            'class_name' => $validated['class_name'],
            'level' => $level,
            'major' => $major,
            'gender' => $validated['gender'],
            'parent_name' => $validated['parent_name'] ?? $student->parent_name,
            'parent_phone' => $validated['parent_phone'] ?? $student->parent_phone,
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $student->delete();

        return redirect()->back()->with('success', 'Siswa "' . $name . '" berhasil dihapus dari sistem!');
    }

    public function clearClassStudents(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
        ]);

        $className = trim($validated['class_name']);
        $students = Student::where('class_name', $className)->get();
        $count = $students->count();

        if ($count === 0) {
            return redirect()->back()->with('error', "Tidak ada siswa yang terdaftar di kelas {$className}.");
        }

        foreach ($students as $student) {
            $student->attendances()->delete();
            $student->violations()->delete();
            $student->delete();
        }

        return redirect()->back()->with('success', "Berhasil menghapus seluruh ({$count}) siswa di kelas {$className}!");
    }

    public function stats(Request $request)
    {
        // 1. Hitung ringkasan presensi aktual dari database
        $totalAttendances = Attendance::count();
        $hadirCount = Attendance::where('status', 'Hadir')->count();
        $sakitCount = Attendance::where('status', 'Sakit')->count();
        $izinCount = Attendance::where('status', 'Izin')->count();
        $alphaCount = Attendance::where('status', 'Alpa')->count();

        $hadirRate = $totalAttendances > 0 ? round(($hadirCount / $totalAttendances) * 100, 1) : 0;
        $sakitRate = $totalAttendances > 0 ? round(($sakitCount / $totalAttendances) * 100, 1) : 0;
        $izinRate = $totalAttendances > 0 ? round(($izinCount / $totalAttendances) * 100, 1) : 0;
        $alphaRate = $totalAttendances > 0 ? round(($alphaCount / $totalAttendances) * 100, 1) : 0;

        $summaryMetrics = [
            'totalPresensi' => $totalAttendances,
            'hadirCount' => $hadirCount,
            'sakitCount' => $sakitCount,
            'izinCount' => $izinCount,
            'alphaCount' => $alphaCount,
            'hadirRate' => $hadirRate,
            'sakitRate' => $sakitRate,
            'izinRate' => $izinRate,
            'alphaRate' => $alphaRate,
        ];

        // 2. Hitung tren 6 bulan terakhir dari database aktual
        $monthLabels = [];
        $presentRateSeries = [];
        $lateRateSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthLabels[] = $monthDate->translatedFormat('F');
            $year = $monthDate->year;
            $month = $monthDate->month;

            $monthTotal = Attendance::whereYear('date', $year)->whereMonth('date', $month)->count();
            $monthHadir = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('status', 'Hadir')->count();
            $monthLate = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('status', 'Terlambat')->count();

            $presentRateSeries[] = $monthTotal > 0 ? round(($monthHadir / $monthTotal) * 100, 1) : 0;
            $lateRateSeries[] = $monthTotal > 0 ? round(($monthLate / $monthTotal) * 100, 1) : 0;
        }

        $monthlyStats = [
            'labels' => $monthLabels,
            'presentRate' => $presentRateSeries,
            'lateRate' => $lateRateSeries,
        ];

        $oneWeekAgo = now()->subDays(7)->toDateString();
        $today = now()->toDateString();

        // 3. Siswa yang memiliki catatan Alpa >= 3 kali dalam 7 hari terakhir dari database riil
        $atRiskStudents = Student::active()->with(['attendances' => function ($q) use ($oneWeekAgo, $today) {
            $q->whereBetween('date', [$oneWeekAgo, $today])
              ->where('status', 'Alpa')
              ->orderBy('date', 'desc');
        }])
        ->get()
        ->map(function ($student) {
            $alphaAttendances = $student->attendances;
            $alphaCount = $alphaAttendances->count();

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nis,
                'class_name' => $student->class_name,
                'major' => $student->major,
                'parent_name' => $student->parent_name ?? '-',
                'parent_phone' => $student->parent_phone ?? '-',
                'alpha_count' => $alphaCount,
                'dates' => $alphaAttendances->pluck('date')->toArray(),
                'risk_level' => $alphaCount >= 4 ? 'Tinggi (SP-2)' : 'Peringatan (SP-1)',
                'status_action' => 'Belum Dipanggil',
            ];
        })
        ->filter(function ($s) {
            return $s['alpha_count'] >= 3;
        })
        ->values();

        return Inertia::render('AttendanceStats', [
            'monthlyStats' => $monthlyStats,
            'summaryMetrics' => $summaryMetrics,
            'totalStudents' => Student::active()->count(),
            'atRiskStudents' => $atRiskStudents,
        ]);
    }

    public function monthly(Request $request)
    {
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $authorizedClasses = $this->getAuthorizedClasses();
        $classes = collect($authorizedClasses);
        
        $selectedClass = $request->query('class_name', $classes->first() ?? 'X RPL 1');
        if (!in_array($selectedClass, $authorizedClasses) && $classes->isNotEmpty()) {
            $selectedClass = $classes->first();
        }
        
        // Parse date info
        try {
            $monthCarbon = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Exception $e) {
            $monthCarbon = now()->startOfMonth();
            $selectedMonth = $monthCarbon->format('Y-m');
        }

        $daysInMonth = $monthCarbon->daysInMonth;
        $monthName = $monthCarbon->translatedFormat('F Y');

        // Query students in selected class
        $students = Student::active()->where('class_name', $selectedClass)
            ->orderBy('name')
            ->get();

        // Get attendances for this month & class
        $studentIds = $students->pluck('id');
        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->whereYear('date', $monthCarbon->year)
            ->whereMonth('date', $monthCarbon->month)
            ->get()
            ->groupBy('student_id');

        // Build calendar days array (day 1..daysInMonth)
        $days = [];
        $effectiveDaysCount = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = $monthCarbon->copy()->day($d);
            $isWeekend = $currentDate->isWeekend(); // Saturday / Sunday
            if (!$isWeekend) {
                $effectiveDaysCount++;
            }
            $days[] = [
                'day' => $d,
                'dayName' => $currentDate->translatedFormat('D'),
                'fullDate' => $currentDate->toDateString(),
                'isWeekend' => $isWeekend,
            ];
        }

        // Generate student attendance matrix
        $studentMatrix = [];

        foreach ($students as $student) {
            $records = $attendances->get($student->id, collect())->keyBy('date');
            $daily = [];
            $hadirCount = 0;
            $terlambatCount = 0;
            $sakitCount = 0;
            $izinCount = 0;
            $alpaCount = 0;

            foreach ($days as $idx => $d) {
                if ($d['isWeekend']) {
                    $daily[$d['day']] = [
                        'code' => 'L', // Libur
                        'status' => 'Libur',
                        'time' => '-',
                    ];
                    continue;
                }

                // Check actual database record
                if ($records->has($d['fullDate'])) {
                    $att = $records->get($d['fullDate']);
                    $code = match ($att->status) {
                        'Hadir' => 'H',
                        'Terlambat' => 'T',
                        'Sakit' => 'S',
                        'Izin' => 'I',
                        'Alpa' => 'A',
                        default => '-'
                    };
                    $daily[$d['day']] = [
                        'code' => $code,
                        'status' => $att->status,
                        'time' => $att->check_in_time ?? '-',
                    ];
                } else {
                    // Tidak ada data presensi untuk hari ini
                    $daily[$d['day']] = [
                        'code' => '-',
                        'status' => '-',
                        'time' => '-',
                    ];
                }

                // Count accumulation
                $code = $daily[$d['day']]['code'];
                if ($code === 'H') $hadirCount++;
                elseif ($code === 'T') $terlambatCount++;
                elseif ($code === 'S') $sakitCount++;
                elseif ($code === 'I') $izinCount++;
                elseif ($code === 'A') $alpaCount++;
            }

            $totalPresensi = $hadirCount + $terlambatCount;
            $attendancePercentage = $effectiveDaysCount > 0 
                ? round(($totalPresensi / $effectiveDaysCount) * 100, 1) 
                : 0;

            $studentMatrix[] = [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'gender' => $student->gender ?? 'L',
                'daily' => $daily,
                'summary' => [
                    'hadir' => $hadirCount,
                    'terlambat' => $terlambatCount,
                    'sakit' => $sakitCount,
                    'izin' => $izinCount,
                    'alpa' => $alpaCount,
                    'total' => $effectiveDaysCount,
                    'percentage' => $attendancePercentage,
                ]
            ];
        }

        // Summary Statistics for the class
        $classStats = [
            'totalStudents' => $students->count(),
            'effectiveDays' => $effectiveDaysCount,
            'avgPercentage' => count($studentMatrix) > 0 
                ? round(collect($studentMatrix)->avg('summary.percentage'), 1) 
                : 0,
        ];

        $classroomObj = Classroom::where('name', $selectedClass)->first();

        return Inertia::render('AttendanceMonthly', [
            'students' => $studentMatrix,
            'days' => $days,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedMonth' => $selectedMonth,
            'monthName' => $monthName,
            'classStats' => $classStats,
            'schoolInfo' => [
                'name' => \App\Models\Setting::get('school_name', 'SMK PGRI 05 JEMBER'),
                'npsn' => '20523821',
                'address' => \App\Models\Setting::get('school_address', 'Jl. Krakatau No. 54 Tanggul, Kab. Jember - Jawa Timur'),
                'waliKelas' => $classroomObj?->wali_kelas ?? 'Bpk. Ahmad Fauzi, S.Kom',
                'guruBk' => $classroomObj?->guru_bk_name ?? 'Dra. Hj. Nurul Hidayati, M.Pd',
                'kepalaSekolah' => \App\Models\Setting::get('kepala_sekolah', 'Drs. H. Saiful Bahri, M.Pd'),
                'nipKepalaSekolah' => \App\Models\Setting::get('nip_kepala_sekolah', '19680315 199403 1 008'),
                'tahunAjaran' => \App\Models\Setting::get('tahun_ajaran', '2024/2025'),
            ]
        ]);
    }

    public function sync(Request $request)
    {
        // Simulate syncing from HadirMu fingerprint device
        return back()->with('success', 'Sinkronisasi mesin HadirMu berhasil diselesaikan!');
    }
}
