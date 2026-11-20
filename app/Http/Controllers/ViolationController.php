<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Http\Requests\StoreViolationRequest;
use App\Http\Requests\UpdateViolationRequest;
use Illuminate\Support\Facades\Storage;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $violations = Violation::with('student')
            ->orderByDesc('points')
            ->latest()
            ->get();
        $students = Student::active()
            ->orderByDesc('total_violation_points')
            ->orderBy('name')
            ->get();
        $summons = \App\Models\ParentSummon::with('student')->latest()->get();

        return Inertia::render('ViolationList', [
            'dbViolations' => $violations,
            'dbStudents' => $students,
            'dbSummons' => $summons,
            'initialTab' => $request->query('tab', 'daftar'),
        ]);
    }

    public function create()
    {
        $students = Student::active()->orderByDesc('total_violation_points')->orderBy('name')->get();

        return Inertia::render('InputViolation', [
            'studentsList' => $students,
        ]);
    }

    public function store(StoreViolationRequest $request)
    {
        $validated = $request->validated();

        $rawStudentName = $request->input('student_name', 'Siswa');
        $extractedClass = 'X RPL 1';
        $cleanedName = $rawStudentName;

        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawStudentName, $matches)) {
            $cleanedName = trim($matches[1]);
            $extractedClass = trim($matches[2]);
        }

        $validated['student_name'] = $cleanedName;
        $validated['student_class'] = $validated['student_class'] ?? $extractedClass;
        $validated['class_snapshot'] = $validated['student_class'];
        $validated['violation_name'] = $validated['violation_name'] ?? $validated['category'];
        $validated['date'] = $validated['date'] ?? now()->toDateString();
        $validated['time'] = $validated['time'] ?? now()->format('H:i');
        $validated['reporter'] = $validated['reporter'] ?? 'Petugas Piket & BK';
        $validated['penalty_action'] = $validated['penalty_action'] ?? 'Teguran lisan & pembinaan';

        // 1. Penanganan Poin (Opsional: jika null/kosong, biarkan NULL tanpa nilai default)
        $rawPoints = $request->input('poin', $request->input('points'));
        if ($rawPoints === null || $rawPoints === '') {
            $validated['points'] = null;
        } else {
            $validated['points'] = (int) $rawPoints;
        }

        // 2. Penanganan Bukti Foto (Upload Berkas Fisik / Base64 Kamera)
        if ($request->hasFile('foto_bukti') || $request->hasFile('foto')) {
            $file = $request->file('foto_bukti') ?? $request->file('foto');
            $path = $file->store('pelanggaran', 'public');
            $validated['foto_bukti'] = $path;
            $validated['photo_url'] = Storage::disk('public')->url($path);
        } elseif (!empty($validated['photo_url']) && str_starts_with($validated['photo_url'], 'data:image')) {
            if (preg_match('/^data:image\/(\w+);base64,/', $validated['photo_url'], $matches)) {
                $data = substr($validated['photo_url'], strpos($validated['photo_url'], ',') + 1);
                $ext = strtolower($matches[1]);
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $filename = 'pelanggaran/' . uniqid('cam_') . '.' . $ext;
                    Storage::disk('public')->put($filename, $decoded);
                    $validated['foto_bukti'] = $filename;
                    $validated['photo_url'] = Storage::disk('public')->url($filename);
                }
            }
        }

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
                $student->recalculatePoints();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggaran berhasil dicatat ke sistem.',
                'data' => $violation
            ]);
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

    public function update(UpdateViolationRequest $request, $id)
    {
        $violation = Violation::findOrFail($id);
        $validated = $request->validated();

        // 1. Penanganan Poin (Opsional)
        if ($request->has('poin') || $request->has('points')) {
            $rawPoints = $request->input('poin', $request->input('points'));
            $validated['points'] = ($rawPoints === null || $rawPoints === '') ? null : (int) $rawPoints;
        }

        // 2. Penanganan Hapus Foto atau Ganti Foto
        if ($request->boolean('hapus_foto')) {
            $violation->deleteAttachedPhoto();
            $validated['foto_bukti'] = null;
            $validated['photo_url'] = null;
        } elseif ($request->hasFile('foto_bukti') || $request->hasFile('foto')) {
            $violation->deleteAttachedPhoto();
            $file = $request->file('foto_bukti') ?? $request->file('foto');
            $path = $file->store('pelanggaran', 'public');
            $validated['foto_bukti'] = $path;
            $validated['photo_url'] = Storage::disk('public')->url($path);
        } elseif (!empty($validated['photo_url']) && str_starts_with($validated['photo_url'], 'data:image')) {
            $violation->deleteAttachedPhoto();
            if (preg_match('/^data:image\/(\w+);base64,/', $validated['photo_url'], $matches)) {
                $data = substr($validated['photo_url'], strpos($validated['photo_url'], ',') + 1);
                $ext = strtolower($matches[1]);
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $filename = 'pelanggaran/' . uniqid('cam_') . '.' . $ext;
                    Storage::disk('public')->put($filename, $decoded);
                    $validated['foto_bukti'] = $filename;
                    $validated['photo_url'] = Storage::disk('public')->url($filename);
                }
            }
        }

        $violation->update($validated);

        if ($violation->student_id) {
            $student = Student::find($violation->student_id);
            if ($student) {
                $student->recalculatePoints();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggaran berhasil diperbarui.',
                'data' => $violation
            ]);
        }

        return redirect()->back()->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $violation = Violation::findOrFail($id);
        $studentId = $violation->student_id;

        $violation->deleteAttachedPhoto();
        $violation->delete();

        if ($studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $student->recalculatePoints();
            }
        }

        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }

    public function summons(Request $request)
    {
        $studentsWithSummons = Student::where('total_violation_points', '>=', 50)
            ->with('violations')
            ->get();
        $summons = \App\Models\ParentSummon::with('student')->latest()->get();

        return Inertia::render('ParentSummons', [
            'students' => $studentsWithSummons,
            'dbSummons' => $summons,
        ]);
    }

    public function storeSummons(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'stage' => 'required|string|max:100',
            'case_title' => 'required|string|max:255',
            'case_description' => 'nullable|string',
            'points' => 'nullable|integer',
            'letter_number' => 'nullable|string|max:100',
            'meeting_date' => 'required|string|max:100',
            'meeting_time' => 'required|string|max:50',
            'room' => 'nullable|string|max:100',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'counselor' => 'nullable|string|max:255',
        ]);

        $student = Student::find($validated['student_id']);
        if (!$student) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan.');
        }

        // Auto generate letter number jika belum ada
        if (empty($validated['letter_number'])) {
            $count = \App\Models\ParentSummon::count() + 1;
            $validated['letter_number'] = sprintf("421.5/%03d/BK/SMK-PGRI5/%s/%s", $count, strtoupper(now()->format('M')), now()->format('Y'));
        }

        $validated['student_name'] = $student->name;
        $validated['nis'] = $student->nis;
        $validated['class_name'] = $student->class_name;
        $validated['points'] = $validated['points'] ?? ($student->total_violation_points ?? 0);
        $validated['parent_name'] = $validated['parent_name'] ?? $student->parent_name;
        $validated['parent_phone'] = $validated['parent_phone'] ?? $student->parent_phone;
        $validated['room'] = $validated['room'] ?? 'Ruang BK Gedung A';
        $validated['status'] = 'Menunggu Kehadiran';
        $validated['counselor'] = $validated['counselor'] ?? 'Dra. Hj. Nurul Hidayati, M.Pd';

        \App\Models\ParentSummon::create($validated);

        return redirect()->back()->with('success', 'Surat panggilan orang tua berhasil diterbitkan dan tersimpan di database.');
    }

    public function updateSummons(Request $request, $id)
    {
        $summon = \App\Models\ParentSummon::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|string|max:50',
            'mediation_notes' => 'nullable|string',
            'stage' => 'nullable|string|max:100',
        ]);

        $summon->update($validated);

        return redirect()->back()->with('success', 'Status dan berita acara mediasi berhasil disimpan.');
    }

    public function destroySummons($id)
    {
        $summon = \App\Models\ParentSummon::findOrFail($id);
        $summon->delete();

        return redirect()->back()->with('success', 'Data surat panggilan berhasil dihapus.');
    }
}
