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
        $allDbClasses = Classroom::orderBy('name')->pluck('name')->toArray();
        if (empty($allDbClasses)) {
            $allDbClasses = Student::select('class_name')->distinct()->orderBy('class_name')->pluck('class_name')->toArray();
        }
        $allDbClasses = array_values(array_unique(array_filter($allDbClasses)));

        $user = Auth::user();
        if ($user && $user->role === 'guru') {
            $userRombel = (string)($user->rombel ?? '');

            $fromClassrooms = Classroom::where('guru_bk_id', $user->id)
                ->orWhere('guru_bk_name', $user->name)
                ->pluck('name')->toArray();

            $fromUserRombel = [];
            if (!empty($userRombel)) {
                $decoded = json_decode($userRombel, true);
                if (is_array($decoded)) {
                    $fromUserRombel = $decoded;
                } else {
                    $fromUserRombel = array_map('trim', explode(',', $userRombel));
                }
            }

            // Bersihkan string non-kelas seperti "Semua Rombel"
            $fromUserRombel = array_filter($fromUserRombel, function ($r) {
                return !empty($r) && !str_contains(strtolower($r), 'semua');
            });

            $guruRombels = array_values(array_unique(array_filter(array_merge($fromClassrooms, $fromUserRombel))));
            sort($guruRombels, SORT_NATURAL | SORT_FLAG_CASE);
            return $guruRombels;
        }

        return $allDbClasses;
    }

    public function index(Request $request)
    {
        $today = now()->toDateString();
        $authorizedClasses = $this->getAuthorizedClasses();

        $studentQuery = Student::active()->with(['attendances' => function ($q) use ($today) {
            $q->where('date', $today);
        }]);

        $user = Auth::user();
        if ($user && $user->role === 'guru') {
            if (!empty($authorizedClasses)) {
                $studentQuery->whereIn('class_name', $authorizedClasses);
            } else {
                $studentQuery->whereRaw('1 = 0');
            }
        }

        $students = $studentQuery->orderBy('class_name')->orderedByAbsen()->get();
        $classes = $authorizedClasses;

        $visibleStudentIds = $students->pluck('id');

        $stats = [
            'totalStudents' => $students->count(),
            'presentToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Hadir')->count(),
            'lateToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Terlambat')->count(),
            'permitToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->whereIn('status', ['Izin', 'Sakit'])->count(),
            'alphaToday' => Attendance::whereIn('student_id', $visibleStudentIds)->where('date', $today)->where('status', 'Alpa')->count(),
        ];

        $rombelQuery = Classroom::orderBy('level')->orderBy('name');
        if ($user && $user->role === 'guru') {
            if (!empty($authorizedClasses)) {
                $rombelQuery->whereIn('name', $authorizedClasses);
            } else {
                $rombelQuery->whereRaw('1 = 0');
            }
        }

        $dbRombels = $rombelQuery->get()->map(function ($r) {
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

        $majors = Major::orderBy('code')->get(['id', 'code', 'name']);

        return Inertia::render('AttendanceList', [
            'dbStudents' => $students,
            'classes' => $classes,
            'rombelList' => $dbRombels,
            'rombels' => $dbRombels,
            'majors' => $majors,
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
        $perClassCounter = [];

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

            $perClassCounter[$className] = ($perClassCounter[$className] ?? 0) + 1;
            $rawNoAbsen = $item['noAbsen'] ?? ($item['no_absen'] ?? null);
            $noAbsen = (is_numeric($rawNoAbsen) && (int)$rawNoAbsen > 0) ? (int)$rawNoAbsen : $perClassCounter[$className];

            $student = Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'classroom_id' => $classId,
                    'no_absen' => $noAbsen,
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $level,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => (!empty($item['parentWa']) && $item['parentWa'] !== '-') ? $item['parentWa'] : ((!empty($item['parent_phone']) && $item['parent_phone'] !== '-') ? $item['parent_phone'] : null),
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
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'class_name' => 'nullable|string|max:50',
            'level' => 'nullable|string|max:20',
            'major' => 'nullable|string|max:50',
            'rombel' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'no_absen' => 'nullable|integer|min:1|max:999',
        ]);

        $nis = !empty($validated['nis']) ? trim($validated['nis']) : (!empty($validated['nisn']) ? trim($validated['nisn']) : '');
        if (empty($nis)) {
            return redirect()->back()->withErrors(['nis' => 'NISN wajib diisi.']);
        }
        if (Student::where('nis', $nis)->exists()) {
            return redirect()->back()->withErrors(['nis' => 'NISN sudah terdaftar. Silakan gunakan NISN lain.']);
        }

        // Resolusi Tingkat (Level), Jurusan (Major), dan Rombel
        $rawLevel = trim((string)($request->input('level') ?? ''));
        $rawMajor = trim((string)($request->input('major') ?? ''));
        $rawRombel = trim((string)($request->input('rombel') ?? ''));
        $className = trim((string)($request->input('class_name') ?? ''));

        $parts = !empty($className) ? preg_split('/\s+/', $className) : [];
        $prefix = $parts[0] ?? '';

        // Tentukan Level standar ('10', '11', '12')
        $level = '10';
        if (!empty($rawLevel)) {
            $upperLvl = strtoupper($rawLevel);
            if ($upperLvl === 'X' || $rawLevel === '10' || str_contains($rawLevel, '10')) $level = '10';
            elseif ($upperLvl === 'XI' || $rawLevel === '11' || str_contains($rawLevel, '11')) $level = '11';
            elseif ($upperLvl === 'XII' || $rawLevel === '12' || str_contains($rawLevel, '12')) $level = '12';
            else $level = $rawLevel;
        } elseif (!empty($prefix)) {
            $upper = strtoupper($prefix);
            if ($upper === 'X' || $upper === '10') $level = '10';
            elseif ($upper === 'XI' || $upper === '11') $level = '11';
            elseif ($upper === 'XII' || $upper === '12') $level = '12';
            elseif (is_numeric($prefix)) $level = $prefix;
        }

        $romanPrefix = $level === '10' ? 'X' : ($level === '11' ? 'XI' : ($level === '12' ? 'XII' : $level));
        $major = !empty($rawMajor) ? strtoupper($rawMajor) : (isset($parts[1]) ? strtoupper($parts[1]) : 'RPL');
        $rombel = !empty($rawRombel) ? $rawRombel : (isset($parts[2]) ? $parts[2] : (count($parts) > 2 ? end($parts) : '1'));

        if (empty($className)) {
            $className = "{$romanPrefix} {$major} {$rombel}";
        }

        // Pastikan Jurusan terdaftar
        Major::firstOrCreate(
            ['code' => $major],
            ['name' => $major, 'description' => 'Program Keahlian ' . $major]
        );

        // Pastikan Classroom terdaftar dengan level standar ('10', '11', '12')
        $user = Auth::user();
        $classroom = Classroom::firstOrCreate(
            ['name' => $className],
            [
                'level' => $level,
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $className,
                'guru_bk_id' => $user?->id,
                'guru_bk_name' => $user?->name ?? 'Damar',
            ]
        );

        $student = Student::create([
            'classroom_id' => $classroom->id,
            'nis' => $nis,
            'no_absen' => !empty($validated['no_absen']) ? (int)$validated['no_absen'] : null,
            'name' => $validated['name'],
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'rombel' => $rombel,
            'gender' => $validated['gender'],
            'parent_name' => $validated['parent_name'] ?? 'Bpk/Ibu ' . $validated['name'],
            'parent_phone' => !empty($validated['parent_phone']) ? $validated['parent_phone'] : null,
            'total_violation_points' => 0,
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
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'class_name' => 'nullable|string|max:50',
            'level' => 'nullable|string|max:20',
            'major' => 'nullable|string|max:50',
            'rombel' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'no_absen' => 'nullable|integer|min:1|max:999',
        ]);

        $nis = !empty($validated['nis']) ? trim($validated['nis']) : (!empty($validated['nisn']) ? trim($validated['nisn']) : $student->nis);
        if (Student::where('nis', $nis)->where('id', '!=', $student->id)->exists()) {
            return redirect()->back()->withErrors(['nis' => 'NISN sudah digunakan oleh siswa lain.']);
        }

        $rawLevel = trim((string)($request->input('level') ?? ''));
        $rawMajor = trim((string)($request->input('major') ?? ''));
        $rawRombel = trim((string)($request->input('rombel') ?? ''));
        $className = trim((string)($request->input('class_name') ?? ''));

        $parts = !empty($className) ? preg_split('/\s+/', $className) : [];
        $prefix = $parts[0] ?? '';

        $level = $student->level ?: '10';
        if (!empty($rawLevel)) {
            $upperLvl = strtoupper($rawLevel);
            if ($upperLvl === 'X' || $rawLevel === '10' || str_contains($rawLevel, '10')) $level = '10';
            elseif ($upperLvl === 'XI' || $rawLevel === '11' || str_contains($rawLevel, '11')) $level = '11';
            elseif ($upperLvl === 'XII' || $rawLevel === '12' || str_contains($rawLevel, '12')) $level = '12';
            else $level = $rawLevel;
        } elseif (!empty($prefix)) {
            $upper = strtoupper($prefix);
            if ($upper === 'X' || $upper === '10') $level = '10';
            elseif ($upper === 'XI' || $upper === '11') $level = '11';
            elseif ($upper === 'XII' || $upper === '12') $level = '12';
            elseif (is_numeric($prefix)) $level = $prefix;
        }

        $romanPrefix = $level === '10' ? 'X' : ($level === '11' ? 'XI' : ($level === '12' ? 'XII' : $level));
        $major = !empty($rawMajor) ? strtoupper($rawMajor) : (isset($parts[1]) ? strtoupper($parts[1]) : ($student->major ?: 'RPL'));
        $rombel = !empty($rawRombel) ? $rawRombel : (isset($parts[2]) ? $parts[2] : ($student->rombel ?: '1'));

        if (empty($className)) {
            $className = "{$romanPrefix} {$major} {$rombel}";
        }

        Major::firstOrCreate(
            ['code' => $major],
            ['name' => $major, 'description' => 'Program Keahlian ' . $major]
        );

        $user = Auth::user();
        $classroom = Classroom::firstOrCreate(
            ['name' => $className],
            [
                'level' => $level,
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $className,
                'guru_bk_id' => $user?->id,
                'guru_bk_name' => $user?->name ?? 'Damar',
            ]
        );

        $student->update([
            'classroom_id' => $classroom->id,
            'nis' => $nis,
            'no_absen' => array_key_exists('no_absen', $validated) ? ($validated['no_absen'] ? (int)$validated['no_absen'] : null) : $student->no_absen,
            'name' => $validated['name'],
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'rombel' => $rombel,
            'gender' => $validated['gender'],
            'parent_name' => $validated['parent_name'] ?? $student->parent_name,
            'parent_phone' => array_key_exists('parent_phone', $validated) ? ($validated['parent_phone'] ?: null) : $student->parent_phone,
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

    public function deleteClassroom(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:100',
        ]);

        $className = trim($validated['class_name']);

        // 1. Hapus seluruh siswa di kelas tersebut beserta data terkait
        $students = Student::where('class_name', $className)->get();
        $count = $students->count();

        foreach ($students as $student) {
            $student->attendances()->delete();
            $student->violations()->delete();
            $student->delete();
        }

        // 2. Hapus master kelas dari tabel classrooms
        Classroom::where('name', $className)->delete();

        $msg = $count > 0 
            ? "Kelas {$className} beserta {$count} siswa di dalamnya berhasil dihapus langsung!" 
            : "Kelas {$className} berhasil dihapus dari sistem!";

        return redirect('/absensi')->with('success', $msg);
    }

    public function clearClassStudents(Request $request)
    {
        return $this->deleteClassroom($request);
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        $authorizedClasses = $this->getAuthorizedClasses();

        $studentBaseQuery = Student::active();
        if ($user && $user->role === 'guru') {
            if (!empty($authorizedClasses)) {
                $studentBaseQuery->whereIn('class_name', $authorizedClasses);
            } else {
                $studentBaseQuery->whereRaw('1 = 0');
            }
        }

        $visibleStudentIds = (clone $studentBaseQuery)->pluck('id');
        $attendanceBaseQuery = Attendance::whereIn('student_id', $visibleStudentIds);

        // 1. Hitung ringkasan presensi aktual dari database
        $totalAttendances = (clone $attendanceBaseQuery)->count();
        $hadirCount = (clone $attendanceBaseQuery)->where('status', 'Hadir')->count();
        $terlambatCount = (clone $attendanceBaseQuery)->where('status', 'Terlambat')->count();
        $sakitCount = (clone $attendanceBaseQuery)->where('status', 'Sakit')->count();
        $izinCount = (clone $attendanceBaseQuery)->where('status', 'Izin')->count();
        $alphaCount = (clone $attendanceBaseQuery)->whereIn('status', ['Alpa', 'Alpha'])->count();

        $hadirRate = $totalAttendances > 0 ? round(($hadirCount / $totalAttendances) * 100, 1) : 0;
        $terlambatRate = $totalAttendances > 0 ? round(($terlambatCount / $totalAttendances) * 100, 1) : 0;
        $sakitRate = $totalAttendances > 0 ? round(($sakitCount / $totalAttendances) * 100, 1) : 0;
        $izinRate = $totalAttendances > 0 ? round(($izinCount / $totalAttendances) * 100, 1) : 0;
        $alphaRate = $totalAttendances > 0 ? round(($alphaCount / $totalAttendances) * 100, 1) : 0;

        $summaryMetrics = [
            'totalPresensi' => $totalAttendances,
            'hadirCount' => $hadirCount,
            'terlambatCount' => $terlambatCount,
            'sakitCount' => $sakitCount,
            'izinCount' => $izinCount,
            'alphaCount' => $alphaCount,
            'hadirRate' => $hadirRate,
            'terlambatRate' => $terlambatRate,
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

            $monthTotal = (clone $attendanceBaseQuery)->whereYear('date', $year)->whereMonth('date', $month)->count();
            $monthHadir = (clone $attendanceBaseQuery)->whereYear('date', $year)->whereMonth('date', $month)->where('status', 'Hadir')->count();
            $monthLate = (clone $attendanceBaseQuery)->whereYear('date', $year)->whereMonth('date', $month)->where('status', 'Terlambat')->count();

            $presentRateSeries[] = $monthTotal > 0 ? round(($monthHadir / $monthTotal) * 100, 1) : 0;
            $lateRateSeries[] = $monthTotal > 0 ? round(($monthLate / $monthTotal) * 100, 1) : 0;
        }

        $monthlyStats = [
            'labels' => $monthLabels,
            'presentRate' => $presentRateSeries,
            'lateRate' => $lateRateSeries,
        ];

        // 3. Rekap Statistik Siswa Aktif sesuai rombel guru beserta Catatan Presensi & Pelanggaran
        $students = (clone $studentBaseQuery)->with(['attendances', 'violations'])->get()->map(function ($student) {
            $attendances = $student->attendances;
            $hadir = $attendances->where('status', 'Hadir')->count();
            $terlambat = $attendances->where('status', 'Terlambat')->count();
            $sakit = $attendances->where('status', 'Sakit')->count();
            $izin = $attendances->where('status', 'Izin')->count();
            $alpa = $attendances->filter(fn ($a) => in_array($a->status, ['Alpa', 'Alpha']))->count();
            $total = $attendances->count();

            $rate = $total > 0 ? round((($hadir + $terlambat) / $total) * 100, 1) : 100.0;
            $violationPoints = (int) ($student->violations->sum('points') ?: ($student->total_violation_points ?? 0));

            // Klasifikasi Tingkat Atensi / Risiko
            if ($alpa >= 3 || $violationPoints >= 50) {
                $riskLevel = 'Kritis (SP-2)';
                $badgeType = 'critical';
            } elseif ($alpa >= 1 || $terlambat >= 3 || $violationPoints >= 25) {
                $riskLevel = 'Peringatan (SP-1)';
                $badgeType = 'warning';
            } elseif ($terlambat >= 1 || $violationPoints > 0) {
                $riskLevel = 'Perhatian';
                $badgeType = 'attention';
            } else {
                $riskLevel = 'Tertib';
                $badgeType = 'good';
            }

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nis,
                'class_name' => $student->class_name,
                'major' => $student->major,
                'level' => $student->level ?? (preg_match('/^(X|XI|XII)\b/i', $student->class_name, $m) ? ($m[1] === 'XII' ? '12' : ($m[1] === 'XI' ? '11' : '10')) : '10'),
                'parent_name' => $student->parent_name ?? '-',
                'parent_phone' => $student->parent_phone ?? '-',
                'hadir_count' => $hadir,
                'terlambat_count' => $terlambat,
                'sakit_count' => $sakit,
                'izin_count' => $izin,
                'alpha_count' => $alpa,
                'total_presensi' => $total,
                'rate' => $rate,
                'violation_points' => $violationPoints,
                'risk_level' => $riskLevel,
                'badge_type' => $badgeType,
                'status_action' => $alpa >= 3 ? 'Perlu Pemanggilan' : 'Tercatat',
            ];
        });

        // Siswa yang butuh perhatian/atensi BK (Alpa, Terlambat, atau Pelanggaran)
        $atRiskStudents = $students->filter(function ($s) {
            return $s['alpha_count'] >= 1 || $s['terlambat_count'] >= 1 || $s['violation_points'] > 0;
        })
        ->sortByDesc(function ($s) {
            return ($s['alpha_count'] * 100) + ($s['violation_points'] * 2) + ($s['terlambat_count'] * 5);
        })
        ->values();

        // Opsi Tingkat dan Jurusan dari master jurusan Admin
        $availableMajors = Major::orderBy('code')->pluck('code')->values();
        $availableLevels = collect(['10', '11', '12']);

        return Inertia::render('AttendanceStats', [
            'monthlyStats' => $monthlyStats,
            'summaryMetrics' => $summaryMetrics,
            'totalStudents' => $students->count(),
            'atRiskStudents' => $atRiskStudents,
            'allStudents' => $students->values(),
            'availableMajors' => $availableMajors,
            'availableLevels' => $availableLevels,
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
            ->orderedByAbsen()
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
                'npsn' => \App\Models\Setting::get('school_npsn', '20523801'),
                'address' => \App\Models\Setting::get('school_address', 'Jl. Krakatau No. 60, Kencong, Kab. Jember - Jawa Timur'),
                'waliKelas' => $classroomObj?->wali_kelas ?? ('Wali Kelas ' . $selectedClass),
                'guruBk' => $classroomObj?->guru_bk_name ?? ($user?->name ?? 'Guru BK'),
                'kepalaSekolah' => \App\Models\Setting::get('kepala_sekolah', 'Saiful Anwar, S.Pd.'),
                'nipKepalaSekolah' => \App\Models\Setting::get('nip_kepala_sekolah', '19780707 200902 1 002'),
                'tahunAjaran' => \App\Models\Setting::get('tahun_ajaran', \App\Models\Setting::get('academic_year', '2026/2027')),
            ]
        ]);
    }

    public function sync(Request $request)
    {
        // Simulate syncing from HadirMu fingerprint device
        return back()->with('success', 'Sinkronisasi mesin HadirMu berhasil diselesaikan!');
    }
}
