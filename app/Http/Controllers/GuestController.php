<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $nisn = $request->query('nisn');
        $studentData = null;

        if ($nisn) {
            $studentData = Student::with(['violations', 'attendances'])->where('nisn', $nisn)->first();
        }

        return Inertia::render('GuestPortal', [
            'searchedStudent' => $studentData,
            'nisnQuery' => $nisn,
        ]);
    }

    public function storeConsultation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'student' => 'required|string|max:150',
            'topic' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
        ]);

        $consultation = \App\Models\ConsultationRequest::create([
            'parent_name' => $validated['name'],
            'student_name' => $validated['student'],
            'topic' => !empty($validated['topic']) ? $validated['topic'] : 'Permohonan konsultasi perkembangan siswa',
            'phone' => $validated['phone'] ?? null,
            'status' => 'unread',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan konsultasi berhasil dikirim ke Ruang BK.',
                'data' => $consultation,
            ]);
        }

        return redirect()->back()->with('success', 'Pesan konsultasi berhasil dikirim ke Ruang BK.');
    }
}
