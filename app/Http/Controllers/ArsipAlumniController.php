<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;

class ArsipAlumniController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $graduationYear = $request->query('tahun', 'ALL');
        $major = $request->query('jurusan', 'ALL');
        $rombel = $request->query('rombel', 'ALL');

        $query = Student::graduated()
            ->with(['violations' => function ($q) {
                $q->orderBy('date', 'desc');
            }]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($graduationYear !== 'ALL' && !empty($graduationYear)) {
            $query->where('graduation_year', $graduationYear);
        }

        if ($major !== 'ALL' && !empty($major)) {
            $query->where('major', $major);
        }

        if ($rombel !== 'ALL' && !empty($rombel)) {
            $query->where('rombel', $rombel);
        }

        $alumni = $query->orderBy('graduation_year', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Distinct filters
        $availableYears = Student::graduated()
            ->whereNotNull('graduation_year')
            ->select('graduation_year')
            ->distinct()
            ->orderBy('graduation_year', 'desc')
            ->pluck('graduation_year')
            ->toArray();

        $availableMajors = Student::graduated()
            ->select('major')
            ->distinct()
            ->orderBy('major')
            ->pluck('major')
            ->toArray();

        $availableRombels = Student::graduated()
            ->select('rombel')
            ->distinct()
            ->whereNotNull('rombel')
            ->orderBy('rombel')
            ->pluck('rombel')
            ->toArray();

        // KPI Counts
        $totalAlumni = Student::graduated()->count();
        $selectedYearCount = $graduationYear !== 'ALL' && !empty($graduationYear) 
            ? Student::graduated()->where('graduation_year', $graduationYear)->count() 
            : $totalAlumni;
        $cleanAlumniCount = Student::graduated()->where('total_violation_points', 0)->count();

        return Inertia::render('ArsipAlumni', [
            'alumni' => $alumni,
            'filters' => [
                'search' => $search,
                'tahun' => $graduationYear,
                'jurusan' => $major,
                'rombel' => $rombel,
            ],
            'options' => [
                'years' => $availableYears,
                'majors' => $availableMajors,
                'rombels' => $availableRombels,
            ],
            'kpi' => [
                'totalAlumni' => $totalAlumni,
                'selectedYearCount' => $selectedYearCount,
                'cleanAlumniCount' => $cleanAlumniCount,
            ],
        ]);
    }

    public function showTimeline($id)
    {
        $student = Student::with(['violations' => function ($q) {
            $q->orderBy('date', 'desc');
        }])->findOrFail($id);

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'last_class_name' => $student->last_class_name ?: $student->class_name,
                'graduation_year' => $student->graduation_year,
                'major' => $student->major,
                'total_points' => $student->total_violation_points,
                'status' => $student->status,
                'violation_status' => $student->violation_status,
            ],
            'timeline' => $student->violations->map(function ($v) {
                return [
                    'id' => $v->id,
                    'date' => $v->date ? $v->date->format('Y-m-d') : null,
                    'date_formatted' => $v->date ? $v->date->translatedFormat('d F Y') : '-',
                    'time' => $v->time ?: '-',
                    'category' => $v->category,
                    'violation_name' => $v->violation_name,
                    'class_snapshot' => $v->class_snapshot ?: $v->student_class,
                    'points' => $v->points,
                    'penalty_action' => $v->penalty_action,
                    'reporter' => $v->reporter,
                    'status' => $v->status,
                    'notes' => $v->notes,
                ];
            }),
        ]);
    }
}
