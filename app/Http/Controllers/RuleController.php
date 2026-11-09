<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Rule;
use App\Models\PointDeduction;
use App\Models\Student;

class RuleController extends Controller
{
    public function index(Request $request)
    {
        // Auto-seed default rules if database is empty
        if (Rule::count() === 0) {
            $defaultRules = [
                // Pelanggaran Ringan
                ['type' => 'pelanggaran', 'category' => 'Ringan', 'code' => 'PL-01', 'name' => 'Terlambat masuk sekolah', 'points' => 5, 'sanction' => 'Teguran lisan & pencatatan'],
                ['type' => 'pelanggaran', 'category' => 'Ringan', 'code' => 'PL-02', 'name' => 'Tidak memakai seragam lengkap / atribut atribut atribut', 'points' => 10, 'sanction' => 'Teguran tertulis & pembinaan kerapian'],
                ['type' => 'pelanggaran', 'category' => 'Ringan', 'code' => 'PL-03', 'name' => 'Membawa HP saat jam pelajaran tanpa izin guru', 'points' => 10, 'sanction' => 'HP disita sementara & pembinaan'],
                // Pelanggaran Sedang
                ['type' => 'pelanggaran', 'category' => 'Sedang', 'code' => 'PS-01', 'name' => 'Membolos / tidak hadir tanpa keterangan sah', 'points' => 15, 'sanction' => 'Panggilan ortu & surat teguran'],
                ['type' => 'pelanggaran', 'category' => 'Sedang', 'code' => 'PS-02', 'name' => 'Mencontek / kecurangan akademik saat ujian', 'points' => 15, 'sanction' => 'Pembatalan nilai ujian & penugasan ulang'],
                ['type' => 'pelanggaran', 'category' => 'Sedang', 'code' => 'PS-03', 'name' => 'Merusak fasilitas sekolah atau rombel', 'points' => 20, 'sanction' => 'Ganti rugi fisik & pembinaan karakter'],
                ['type' => 'pelanggaran', 'category' => 'Sedang', 'code' => 'PS-04', 'name' => 'Merokok / vape di lingkungan sekolah', 'points' => 25, 'sanction' => 'Skorsing 3 hari & surat perjanjian ortu'],
                // Pelanggaran Berat
                ['type' => 'pelanggaran', 'category' => 'Berat', 'code' => 'PB-01', 'name' => 'Berkelahi / tawuran antarpelajar', 'points' => 30, 'sanction' => 'Skorsing 5 hari & SP-2 dinas'],
                ['type' => 'pelanggaran', 'category' => 'Berat', 'code' => 'PB-02', 'name' => 'Perundungan / bullying fisik & verbal', 'points' => 35, 'sanction' => 'Skorsing 1 pekan & konseling intensif'],
                ['type' => 'pelanggaran', 'category' => 'Berat', 'code' => 'PB-03', 'name' => 'Membawa senjata tajam / zat terlarang', 'points' => 50, 'sanction' => 'Konferensi kasus & dikembalikan ke ortu'],
                // Apresiasi / Pemutihan Poin
                ['type' => 'apresiasi', 'category' => 'Pemutihan', 'code' => 'PM-01', 'name' => 'Tugas pembinaan kebersihan & lingkungan sekolah', 'points' => 10, 'sanction' => 'Pengurangan 10 poin pelanggaran aktif'],
                ['type' => 'apresiasi', 'category' => 'Pemutihan', 'code' => 'PM-02', 'name' => 'Konseling tuntas & komitmen perbaikan karakter', 'points' => 15, 'sanction' => 'Pengurangan 15 poin pelanggaran aktif'],
                ['type' => 'apresiasi', 'category' => 'Prestasi', 'code' => 'PR-01', 'name' => 'Prestasi lomba / kejuaraan tingkat sekolah/daerah', 'points' => 20, 'sanction' => 'Pengurangan 20 poin & sertifikat apresiasi'],
                ['type' => 'apresiasi', 'category' => 'Prestasi', 'code' => 'PR-02', 'name' => 'Ketertiban kehadiran 100% selama 1 bulan penuh', 'points' => 10, 'sanction' => 'Pengurangan 10 poin sanksi akumulatif'],
            ];

            foreach ($defaultRules as $r) {
                Rule::create($r);
            }
        }

        $rules = Rule::orderBy('type')->orderBy('points')->get();
        $deductions = PointDeduction::with('student', 'rule')->latest()->take(50)->get();
        $students = Student::orderBy('class_name')->orderBy('name')->get(['id', 'nisn', 'name', 'class_name', 'total_violation_points', 'status']);

        return Inertia::render('RulePoints', [
            'rules' => $rules,
            'deductions' => $deductions,
            'students' => $students,
            'activeTab' => $request->query('tab', 'pelanggaran'),
        ]);
    }

    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:pelanggaran,apresiasi',
            'category' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'sanction' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        Rule::create($validated);

        return redirect()->back()->with('success', 'Aturan tata tertib berhasil ditambahkan.');
    }

    public function updateRule(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:pelanggaran,apresiasi',
            'category' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'sanction' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rule->update($validated);

        return redirect()->back()->with('success', 'Aturan tata tertib berhasil diperbarui.');
    }

    public function deleteRule($id)
    {
        $rule = Rule::findOrFail($id);
        $rule->delete();

        return redirect()->back()->with('success', 'Aturan tata tertib berhasil dihapus.');
    }

    public function storeDeduction(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'rule_id' => 'nullable|exists:rules,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'points_deducted' => 'required|integer|min:1',
            'date' => 'required|date',
            'officer' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'evidence_photo_url' => 'nullable|string',
        ]);

        $validated['officer'] = $validated['officer'] ?? 'Guru BK / Tim Disiplin';

        $deduction = PointDeduction::create($validated);

        // Recalculate net points for the student
        $student = Student::find($validated['student_id']);
        if ($student) {
            $student->recalculatePoints();
        }

        return redirect()->back()->with('success', 'Pemutihan / apresiasi poin siswa berhasil dicatat.');
    }

    public function deleteDeduction($id)
    {
        $deduction = PointDeduction::findOrFail($id);
        $studentId = $deduction->student_id;
        $deduction->delete();

        $student = Student::find($studentId);
        if ($student) {
            $student->recalculatePoints();
        }

        return redirect()->back()->with('success', 'Catatan pemutihan poin berhasil dihapus.');
    }
}
