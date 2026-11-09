<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;
use App\Models\Classroom;
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

        // 1. Ambil data akun Guru BK dari database users
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

        // 2. Auto-seed default classrooms jika kosong
        if (Classroom::count() === 0) {
            $firstGuru = User::where('role', 'guru')->first();
            $guruName = $firstGuru ? $firstGuru->name : 'Dra. Hj. Nurul Hidayati, M.Pd';
            $guruId = $firstGuru ? $firstGuru->id : null;

            $defaultClasses = [
                ['name' => 'X RPL 1', 'level' => 'Kelas 10 (X)', 'major' => 'RPL', 'wali_kelas' => 'Bpk. Kurniawan, S.Kom', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X RPL 2', 'level' => 'Kelas 10 (X)', 'major' => 'RPL', 'wali_kelas' => 'Bpk. Suherman, S.Pd', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X RPL 3', 'level' => 'Kelas 10 (X)', 'major' => 'RPL', 'wali_kelas' => 'Ibu Maya, S.Kom', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X TSM 1', 'level' => 'Kelas 10 (X)', 'major' => 'TSM', 'wali_kelas' => 'Bpk. Joko, S.T', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X TSM 2', 'level' => 'Kelas 10 (X)', 'major' => 'TSM', 'wali_kelas' => 'Bpk. Dedi, S.Pd', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X DKV 1', 'level' => 'Kelas 10 (X)', 'major' => 'DKV', 'wali_kelas' => 'Ibu Sinta, S.Ds', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'X DKV 2', 'level' => 'Kelas 10 (X)', 'major' => 'DKV', 'wali_kelas' => 'Bpk. Fajar, S.Kom', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
                ['name' => 'XI RPL 1', 'level' => 'Kelas 11 (XI)', 'major' => 'RPL', 'wali_kelas' => 'Ibu Rina, S.Kom', 'guru_bk_id' => $guruId, 'guru_bk_name' => $guruName],
            ];

            foreach ($defaultClasses as $dc) {
                Classroom::create($dc);
            }
        }

        // 3. Ambil data Kelas / Rombel dari database
        $kelasList = Classroom::with('guruBk')->orderBy('name')->get()->map(function ($c) {
            $count = Student::where('class_name', $c->name)->count();
            return [
                'id' => $c->id,
                'name' => $c->name,
                'level' => $c->level,
                'jurusan' => $c->major,
                'waliKelas' => $c->wali_kelas ?? '-',
                'guruBK' => $c->guru_bk_name ?? ($c->guruBk ? $c->guruBk->name : '-'),
                'guruBkId' => $c->guru_bk_id,
                'studentCount' => $count > 0 ? $count : 36,
            ];
        });

        // 4. Ambil data Siswa dari database
        $siswaList = Student::orderBy('class_name')->orderBy('name')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'nisn' => $s->nisn,
                'name' => $s->name,
                'className' => $s->class_name,
                'gender' => $s->gender,
                'parentWa' => $s->parent_phone ?? '-',
                'poin' => $s->total_violation_points,
                'status' => $s->status,
            ];
        });

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
            'consultations' => $consultationRequests,
            'unreadConsultationsCount' => $unreadConsultationsCount,
            'initialGuruBkList' => $guruBkList,
            'initialKelasList' => $kelasList,
            'initialSiswaList' => $siswaList,
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

        // Jika rombelText diisi, kaitkan kelas-kelas tersebut ke Guru BK ini
        if (!empty($validated['rombelText'])) {
            $rombels = array_filter(array_map('trim', explode(',', $validated['rombelText'])));
            foreach ($rombels as $rName) {
                Classroom::where('name', $rName)->update([
                    'guru_bk_id' => $user->id,
                    'guru_bk_name' => $user->name,
                ]);
            }
        }

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
                    'rombel' => $user->rombel ? array_map('trim', explode(',', $user->rombel)) : [],
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

        // Update classrooms associated with this Guru BK
        if (!empty($validated['rombelText'])) {
            $rombels = array_filter(array_map('trim', explode(',', $validated['rombelText'])));
            Classroom::where('guru_bk_id', $user->id)->update([
                'guru_bk_id' => null,
                'guru_bk_name' => null,
            ]);
            foreach ($rombels as $rName) {
                Classroom::where('name', $rName)->update([
                    'guru_bk_id' => $user->id,
                    'guru_bk_name' => $user->name,
                ]);
            }
        }

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
                    'rombel' => $user->rombel ? array_map('trim', explode(',', $user->rombel)) : [],
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Data Akun Guru BK ' . $user->name . ' berhasil diperbarui!');
    }

    public function deleteGuru($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;

        // Reset classrooms assigned to this guru
        Classroom::where('guru_bk_id', $user->id)->update([
            'guru_bk_id' => null,
            'guru_bk_name' => null,
        ]);

        $user->delete();

        return redirect()->back()->with('success', 'Akun Guru BK ' . $name . ' berhasil dihapus.');
    }

    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'required|string|max:50',
            'jurusan' => 'required|string|max:50',
            'waliKelas' => 'required|string|max:255',
            'guruBK' => 'nullable|string|max:255',
            'guruBkId' => 'nullable|exists:users,id',
        ]);

        $className = strtoupper(trim($validated['name']));

        // Cari Guru BK
        $guruUser = null;
        if (!empty($validated['guruBkId'])) {
            $guruUser = User::find($validated['guruBkId']);
        } elseif (!empty($validated['guruBK'])) {
            $guruUser = User::where('name', $validated['guruBK'])->first();
        }

        $classroom = Classroom::updateOrCreate(
            ['name' => $className],
            [
                'level' => $validated['level'],
                'major' => $validated['jurusan'],
                'wali_kelas' => $validated['waliKelas'],
                'guru_bk_id' => $guruUser ? $guruUser->id : null,
                'guru_bk_name' => $guruUser ? $guruUser->name : ($validated['guruBK'] ?? null),
            ]
        );

        // Update rombel pada User Guru BK agar tersinkronisasi
        if ($guruUser) {
            $currentRombels = !empty($guruUser->rombel) ? array_map('trim', explode(',', $guruUser->rombel)) : [];
            if (!in_array($className, $currentRombels)) {
                $currentRombels[] = $className;
                $guruUser->update(['rombel' => implode(', ', array_filter(array_unique($currentRombels)))]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'classroom' => [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'level' => $classroom->level,
                    'jurusan' => $classroom->major,
                    'waliKelas' => $classroom->wali_kelas,
                    'guruBK' => $classroom->guru_bk_name,
                    'guruBkId' => $classroom->guru_bk_id,
                    'studentCount' => Student::where('class_name', $classroom->name)->count(),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Rombel Kelas ' . $classroom->name . ' berhasil disimpan!');
    }

    public function updateKelas(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'required|string|max:50',
            'jurusan' => 'required|string|max:50',
            'waliKelas' => 'required|string|max:255',
            'guruBK' => 'nullable|string|max:255',
            'guruBkId' => 'nullable|exists:users,id',
        ]);

        $className = strtoupper(trim($validated['name']));

        // Cari Guru BK
        $guruUser = null;
        if (!empty($validated['guruBkId'])) {
            $guruUser = User::find($validated['guruBkId']);
        } elseif (!empty($validated['guruBK'])) {
            $guruUser = User::where('name', $validated['guruBK'])->first();
        }

        $oldName = $classroom->name;
        $classroom->update([
            'name' => $className,
            'level' => $validated['level'],
            'major' => $validated['jurusan'],
            'wali_kelas' => $validated['waliKelas'],
            'guru_bk_id' => $guruUser ? $guruUser->id : null,
            'guru_bk_name' => $guruUser ? $guruUser->name : ($validated['guruBK'] ?? null),
        ]);

        // Sinkronisasi rombel Guru BK
        if ($guruUser) {
            $currentRombels = !empty($guruUser->rombel) ? array_map('trim', explode(',', $guruUser->rombel)) : [];
            if (!in_array($className, $currentRombels)) {
                $currentRombels[] = $className;
                $guruUser->update(['rombel' => implode(', ', array_filter(array_unique($currentRombels)))]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'classroom' => $classroom]);
        }

        return redirect()->back()->with('success', 'Rombel Kelas ' . $classroom->name . ' berhasil diperbarui!');
    }

    public function deleteKelas($id)
    {
        $classroom = Classroom::findOrFail($id);
        $name = $classroom->name;

        // Hapus nama kelas dari rombel binaan Guru BK jika ada
        if ($classroom->guru_bk_id) {
            $guruUser = User::find($classroom->guru_bk_id);
            if ($guruUser && !empty($guruUser->rombel)) {
                $rombels = array_map('trim', explode(',', $guruUser->rombel));
                $rombels = array_filter($rombels, fn($r) => $r !== $name);
                $guruUser->update(['rombel' => implode(', ', $rombels)]);
            }
        }

        $classroom->delete();

        return redirect()->back()->with('success', 'Rombel Kelas ' . $name . ' berhasil dihapus.');
    }

    public function importStudents(Request $request)
    {
        $studentsData = $request->input('students');

        if (empty($studentsData) || !is_array($studentsData)) {
            return redirect()->back()->with('error', 'Tidak ada data siswa yang valid untuk diimpor.');
        }

        $importedCount = 0;
        foreach ($studentsData as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $nisn = !empty($item['nisn']) ? trim((string)$item['nisn']) : 'NISN' . rand(10000000, 99999999);
            $className = !empty($item['className']) ? strtoupper(trim($item['className'])) : 'X RPL 1';
            
            // Ekstrak level & jurusan dari nama kelas
            $level = 'X';
            if (str_starts_with($className, 'XII')) {
                $level = 'XII';
            } elseif (str_starts_with($className, 'XI')) {
                $level = 'XI';
            } elseif (str_starts_with($className, 'X')) {
                $level = 'X';
            }

            $major = 'RPL';
            if (str_contains($className, 'TSM')) $major = 'TSM';
            elseif (str_contains($className, 'DKV')) $major = 'DKV';
            elseif (str_contains($className, 'TKJ')) $major = 'TKJ';
            elseif (str_contains($className, 'AKL')) $major = 'AKL';
            elseif (str_contains($className, 'OTKP')) $major = 'OTKP';

            // Pastikan Classroom ada di database
            Classroom::firstOrCreate(
                ['name' => $className],
                [
                    'level' => "Kelas {$level} ({$level})",
                    'major' => $major,
                    'wali_kelas' => 'Wali Kelas ' . $className,
                ]
            );

            Student::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $level,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => $item['parentWa'] ?? ($item['parent_phone'] ?? null),
                    'parent_name' => $item['parent_name'] ?? ('Orang Tua ' . trim($item['name'])),
                    'status' => 'Aman',
                ]
            );

            $importedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $importedCount,
                'message' => "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima."
            ]);
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima.");
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
