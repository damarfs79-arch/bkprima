<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;

use App\Models\ConsultationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'totalStudents' => Student::count(),
            'totalViolations' => Violation::count(),
            'activeUsers' => User::count(),
            'systemHealth' => '99.9%',
        ];

        $consultationRequests = ConsultationRequest::orderBy('created_at', 'desc')->get();
        $unreadConsultationsCount = ConsultationRequest::where('status', 'unread')->count();

        // Ambil data akun Guru BK dari database users
        $guruBkList = User::where('role', 'guru')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'username' => $u->username ?? '',
                    'nip' => $u->nip ?? '-',
                    'nuptk' => $u->nuptk ?? '-',
                    'jabatan' => $u->jabatan ?? 'Guru Pembimbing BK',
                    'email' => $u->email,
                    'wa' => $u->phone ?? '+62 8...',
                    'rombel' => $u->rombel ? array_map('trim', explode(',', $u->rombel)) : ['X RPL 1'],
                ];
            });

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
            'consultations' => $consultationRequests,
            'unreadConsultationsCount' => $unreadConsultationsCount,
            'initialGuruBkList' => $guruBkList,
        ]);
    }

    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ]);

        $username = strtolower(trim($validated['username']));
        $email = !empty($validated['email']) ? $validated['email'] : ($username . '@guru.smk.belajar.id');

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'nip' => $validated['nip'] ?? '-',
            'nuptk' => $validated['nuptk'] ?? '-',
            'jabatan' => $validated['jabatan'],
            'phone' => $validated['wa'] ?? '',
            'rombel' => $validated['rombelText'] ?? '',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'guru' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'nip' => $user->nip,
                    'nuptk' => $user->nuptk,
                    'jabatan' => $user->jabatan,
                    'email' => $user->email,
                    'wa' => $user->phone,
                    'rombel' => $user->rombel ? array_map('trim', explode(',', $user->rombel)) : ['X RPL 1'],
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Akun Guru BK ' . $user->name . ' (@' . $user->username . ') berhasil dibuat!');
    }

    public function updateGuru(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ]);

        $username = strtolower(trim($validated['username']));
        $email = !empty($validated['email']) ? $validated['email'] : ($username . '@guru.smk.belajar.id');

        $updateData = [
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'nip' => $validated['nip'] ?? '-',
            'nuptk' => $validated['nuptk'] ?? '-',
            'jabatan' => $validated['jabatan'],
            'phone' => $validated['wa'] ?? '',
            'rombel' => $validated['rombelText'] ?? '',
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'guru' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'nip' => $user->nip,
                    'nuptk' => $user->nuptk,
                    'jabatan' => $user->jabatan,
                    'email' => $user->email,
                    'wa' => $user->phone,
                    'rombel' => $user->rombel ? array_map('trim', explode(',', $user->rombel)) : ['X RPL 1'],
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Data Akun Guru BK ' . $user->name . ' berhasil diperbarui!');
    }

    public function deleteGuru($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', 'Akun Guru BK ' . $name . ' berhasil dihapus.');
    }

    public function updateConsultationStatus(Request $request, $id)
    {
        $consultation = ConsultationRequest::findOrFail($id);
        $status = $request->input('status', 'contacted');
        
        $consultation->update([
            'status' => $status,
            'contacted_at' => $status === 'contacted' ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $consultation]);
        }

        return redirect()->back()->with('success', 'Status pesan konsultasi berhasil diperbarui.');
    }

    public function deleteConsultation($id)
    {
        $consultation = ConsultationRequest::findOrFail($id);
        $consultation->delete();

        return redirect()->back()->with('success', 'Pesan konsultasi berhasil dihapus.');
    }
}
