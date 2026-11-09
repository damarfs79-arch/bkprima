<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $violations = Violation::with('student')->latest()->get();
        $students = Student::all();

        return Inertia::render('ViolationList', [
            'dbViolations' => $violations,
            'dbStudents' => $students,
            'initialTab' => $request->query('tab', 'daftar'),
        ]);
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();

        return Inertia::render('InputViolation', [
            'studentsList' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $rawStudentName = $request->input('student_name', 'Siswa');
        $extractedClass = 'X RPL 1';
        $cleanedName = $rawStudentName;

        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawStudentName, $matches)) {
            $cleanedName = trim($matches[1]);
            $extractedClass = trim($matches[2]);
        }

        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'student_class' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'violation_name' => 'nullable|string|max:255',
            'points' => 'nullable|integer|min:1',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'reporter' => 'nullable|string',
            'penalty_action' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo_url' => 'nullable|string',
        ]);

        $validated['student_name'] = $cleanedName;
        $validated['student_class'] = $validated['student_class'] ?? $extractedClass;
        $validated['violation_name'] = $validated['violation_name'] ?? $validated['category'];
        $validated['points'] = $validated['points'] ?? 5;
        $validated['date'] = $validated['date'] ?? now()->toDateString();
        $validated['time'] = $validated['time'] ?? now()->format('H:i');
        $validated['reporter'] = $validated['reporter'] ?? 'Petugas Piket & BK';
        $validated['penalty_action'] = $validated['penalty_action'] ?? 'Teguran lisan & pembinaan';

        // Auto find student if student_id not specified
        if (empty($validated['student_id'])) {
            $matchedStudent = Student::where('name', 'LIKE', "%{$cleanedName}%")->first();
            if ($matchedStudent) {
                $validated['student_id'] = $matchedStudent->id;
            }
        }

        $violation = Violation::create($validated);

        if (!empty($validated['student_id'])) {
            $student = Student::find($validated['student_id']);
            if ($student) {
                $student->increment('total_violation_points', $validated['points']);
                // Update status threshold
                if ($student->total_violation_points >= 75) {
                    $student->status = 'Panggilan Orang Tua';
                } elseif ($student->total_violation_points >= 50) {
                    $student->status = 'SP 2';
                } elseif ($student->total_violation_points >= 30) {
                    $student->status = 'SP 1';
                } elseif ($student->total_violation_points >= 20) {
                    $student->status = 'Peringatan 2';
                } elseif ($student->total_violation_points >= 10) {
                    $student->status = 'Peringatan 1';
                }
                $student->save();
            }
        }

        return redirect('/pelanggaran')->with('success', 'Pelanggaran berhasil dicatat ke sistem.');
    }

    public function show(Request $request)
    {
        $id = $request->query('id', 1);
        $violation = Violation::with('student')->find($id) ?? Violation::with('student')->first();

        return Inertia::render('ViolationDetail', [
            'violation' => $violation,
        ]);
    }

    public function summons(Request $request)
    {
        $studentsWithSummons = Student::where('total_violation_points', '>=', 50)
            ->with('violations')
            ->get();

        return Inertia::render('ParentSummons', [
            'students' => $studentsWithSummons,
        ]);
    }
}
