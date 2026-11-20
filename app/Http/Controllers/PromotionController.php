<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Classroom;

class PromotionController extends Controller
{
    private function checkAuthorization(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['masteradmin', 'admin'])) {
            abort(403, 'Akses terbatas untuk Master Administrator.');
        }
    }

    public function summary()
    {
        $this->checkAuthorization();

        $countXII = Student::active()
            ->where(function ($q) {
                $q->where('level', '12')
                  ->orWhere('level', 'XII')
                  ->orWhere('class_name', 'like', 'XII %');
            })->count();

        $countXI = Student::active()
            ->where(function ($q) {
                $q->where('level', '11')
                  ->orWhere('level', 'XI')
                  ->orWhere('class_name', 'like', 'XI %');
            })->count();

        $countX = Student::active()
            ->where(function ($q) {
                $q->where('level', '10')
                  ->orWhere('level', 'X')
                  ->orWhere('class_name', 'like', 'X %');
            })->count();

        $activeStudents = Student::active()
            ->orderBy('class_name')
            ->orderBy('name')
            ->get(['id', 'nis', 'name', 'class_name', 'level', 'major']);

        return response()->json([
            'count_xii' => $countXII,
            'count_xi' => $countXI,
            'count_x' => $countX,
            'current_year' => (int) now()->format('Y'),
            'default_academic_year' => ((int) now()->format('Y')) . '/' . (((int) now()->format('Y')) + 1),
            'students' => $activeStudents,
        ]);
    }

    public function promote(Request $request)
    {
        $this->checkAuthorization();

        $validated = $request->validate([
            'graduation_year' => 'required|integer|min:2000|max:2100',
            'new_academic_year' => 'required|string|max:20',
            'retained_student_ids' => 'nullable|array',
            'retained_student_ids.*' => 'integer|exists:students,id',
            'reset_points' => 'nullable|boolean',
            'confirmation' => 'required|string|in:PROSES',
        ]);

        $retainedIds = $validated['retained_student_ids'] ?? [];
        $graduationYear = (int) $validated['graduation_year'];
        $newAcademicYear = $validated['new_academic_year'];
        $resetPoints = (bool) ($validated['reset_points'] ?? false);

        $graduatedCount = 0;
        $promotedToXIICount = 0;
        $promotedToXICount = 0;
        $retainedCount = count($retainedIds);

        DB::transaction(function () use (
            $retainedIds,
            $graduationYear,
            $newAcademicYear,
            $resetPoints,
            &$graduatedCount,
            &$promotedToXIICount,
            &$promotedToXICount
        ) {
            // 1. GRADUATE XII: Semua siswa aktif tingkat XII yang TIDAK tinggal kelas
            $studentsXII = Student::active()
                ->whereNotIn('id', $retainedIds)
                ->where(function ($q) {
                    $q->where('level', '12')
                      ->orWhere('level', 'XII')
                      ->orWhere('class_name', 'like', 'XII %');
                })
                ->get();

            foreach ($studentsXII as $student) {
                $lastClass = $student->class_name ?: "XII {$student->major} {$student->rombel}";
                $student->update([
                    'status' => 'lulus',
                    'graduation_year' => $graduationYear,
                    'last_class_name' => $lastClass,
                ]);
                $graduatedCount++;
            }

            // 2. PROMOTE XI -> XII: Siswa aktif tingkat XI yang TIDAK tinggal kelas
            $studentsXI = Student::active()
                ->whereNotIn('id', $retainedIds)
                ->where(function ($q) {
                    $q->where('level', '11')
                      ->orWhere('level', 'XI')
                      ->orWhere('class_name', 'like', 'XI %');
                })
                ->get();

            foreach ($studentsXI as $student) {
                $oldClass = $student->class_name;
                $newClass = preg_replace('/^XI\b/i', 'XII', $oldClass);
                if ($newClass === $oldClass) {
                    $newClass = "XII {$student->major} " . ($student->rombel ?: '1');
                }

                // Pastikan Classroom tingkat XII terelasi
                $parts = explode(' ', trim($newClass));
                $majorCode = $student->major ?: ($parts[1] ?? 'RPL');
                Classroom::firstOrCreate(
                    ['name' => $newClass],
                    [
                        'level' => 'Kelas 12 (XII)',
                        'major' => $majorCode,
                        'wali_kelas' => 'Wali Kelas ' . $newClass,
                    ]
                );

                $student->update([
                    'level' => '12',
                    'class_name' => $newClass,
                    'academic_year' => $newAcademicYear,
                ]);
                $promotedToXIICount++;
            }

            // 3. PROMOTE X -> XI: Siswa aktif tingkat X yang TIDAK tinggal kelas
            $studentsX = Student::active()
                ->whereNotIn('id', $retainedIds)
                ->where(function ($q) {
                    $q->where('level', '10')
                      ->orWhere('level', 'X')
                      ->orWhere('class_name', 'like', 'X %');
                })
                ->get();

            foreach ($studentsX as $student) {
                $oldClass = $student->class_name;
                $newClass = preg_replace('/^X\b/i', 'XI', $oldClass);
                if ($newClass === $oldClass) {
                    $newClass = "XI {$student->major} " . ($student->rombel ?: '1');
                }

                // Pastikan Classroom tingkat XI terelasi
                $parts = explode(' ', trim($newClass));
                $majorCode = $student->major ?: ($parts[1] ?? 'RPL');
                Classroom::firstOrCreate(
                    ['name' => $newClass],
                    [
                        'level' => 'Kelas 11 (XI)',
                        'major' => $majorCode,
                        'wali_kelas' => 'Wali Kelas ' . $newClass,
                    ]
                );

                $student->update([
                    'level' => '11',
                    'class_name' => $newClass,
                    'academic_year' => $newAcademicYear,
                ]);
                $promotedToXICount++;
            }

            // 4. Update tahun ajaran untuk siswa yang tinggal kelas
            if (!empty($retainedIds)) {
                Student::whereIn('id', $retainedIds)->update([
                    'academic_year' => $newAcademicYear,
                ]);
            }

            // 5. RESET POIN jika diminta (hanya untuk siswa aktif)
            if ($resetPoints) {
                Student::active()->update([
                    'total_violation_points' => 0,
                ]);
            }
        });

        $message = "Proses Kenaikan Kelas & Kelulusan berhasil dilaksanakan! {$graduatedCount} siswa lulus, {$promotedToXIICount} siswa naik ke XII, {$promotedToXICount} siswa naik ke XI" . ($retainedCount > 0 ? ", {$retainedCount} siswa tinggal kelas." : ".");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'graduated' => $graduatedCount,
                    'promoted_to_xii' => $promotedToXIICount,
                    'promoted_to_xi' => $promotedToXICount,
                    'retained' => $retainedCount,
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
