<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Major;
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
            'systemHealth' => '100%',
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
                    'avatar' => $u->avatar,
                    'avatar_url' => $u->avatar_url,
                    'jabatan' => $u->jabatan ?? 'Guru Pembimbing BK',
                    'email' => $u->email,
                    'wa' => $u->phone ?? '-',
                    'rombel' => $u->rombel ? array_map('trim', explode(',', $u->rombel)) : [],
                ];
            });

        // 2. Ambil data Kelas / Rombel dari database
        $kelasList = Classroom::with(['guruBk', 'jurusan'])->withCount('students')->orderBy('name')->get()->map(function ($c) {
            $guruName = '-';
            if ($c->guruBk && $c->guruBk->role === 'guru') {
                $guruName = $c->guruBk->name;
            } elseif (!empty($c->guru_bk_name) && $c->guru_bk_name !== 'Admin IT Sekolah') {
                $guruName = $c->guru_bk_name;
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'level' => $c->level,
                'jurusan' => $c->major,
                'jurusan_id' => $c->jurusan_id,
                'jurusan_nama' => $c->jurusan ? $c->jurusan->name : $c->major,
                'waliKelas' => $c->wali_kelas ?? '-',
                'guruBK' => $guruName,
                'guruBkId' => ($c->guruBk && $c->guruBk->role === 'guru') ? $c->guru_bk_id : null,
                'studentCount' => $c->students_count,
            ];
        });

        // 3. Ambil data Siswa dari database
        $siswaList = Student::orderBy('class_name')->orderBy('name')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'nis' => $s->nis,
                'nisn' => $s->nis,
                'name' => $s->name,
                'className' => $s->class_name,
                'gender' => $s->gender,
                'parentWa' => $s->parent_phone ?? '-',
                'poin' => $s->total_violation_points,
                'status' => $s->status,
            ];
        });

        $jurusanList = Major::orderBy('code')->get();

        // 4. Hitung statistik presensi aktual dari database
        $totalAttendances = \App\Models\Attendance::count();
        $hadirCount = \App\Models\Attendance::where('status', 'Hadir')->count();
        $sakitCount = \App\Models\Attendance::where('status', 'Sakit')->count();
        $izinCount = \App\Models\Attendance::where('status', 'Izin')->count();
        $alphaCount = \App\Models\Attendance::where('status', 'Alpa')->count();

        $hadirRate = $totalAttendances > 0 ? round(($hadirCount / $totalAttendances) * 100, 1) : 0;
        $sakitRate = $totalAttendances > 0 ? round(($sakitCount / $totalAttendances) * 100, 1) : 0;
        $izinRate = $totalAttendances > 0 ? round(($izinCount / $totalAttendances) * 100, 1) : 0;
        $alphaRate = $totalAttendances > 0 ? round(($alphaCount / $totalAttendances) * 100, 1) : 0;

        $attendanceStats = [
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

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
            'attendanceStats' => $attendanceStats,
            'consultations' => $consultationRequests,
            'unreadConsultationsCount' => $unreadConsultationsCount,
            'initialGuruBkList' => $guruBkList,
            'initialKelasList' => $kelasList,
            'initialSiswaList' => $siswaList,
            'initialJurusanList' => $jurusanList,
            'settings' => \App\Models\Setting::getAll(),
        ]);
    }

    public function storeGuru(Request $request)
    {
        $rawEmail = strtolower(trim((string)$request->input('email', '')));
        $rawPassword = (string)$request->input('password', '');
        if (empty($rawPassword)) {
            $rawPassword = '123456';
        }

        $baseUser = explode('@', $rawEmail)[0] ?? '';
        $baseUser = preg_replace('/[^a-z0-9_]/', '', strtolower($baseUser));
        if (empty($baseUser)) {
            $baseUser = preg_replace('/[^a-z0-9_]/', '', strtolower($request->input('name', 'guru_bk')));
            if (empty($baseUser)) $baseUser = 'guru_bk';
        }
        $rawUsername = $baseUser;
        $counter = 1;
        while (User::where('username', $rawUsername)->exists()) {
            $rawUsername = $baseUser . $counter++;
        }

        $request->merge([
            'email' => $rawEmail,
            'username' => $rawUsername,
            'password' => $rawPassword,
            'jabatan' => !empty(trim((string)$request->input('jabatan', ''))) ? trim($request->input('jabatan')) : 'Guru BK',
            'wa' => !empty(trim((string)$request->input('wa', ''))) ? trim($request->input('wa')) : '-',
            'rombelText' => !empty(trim((string)$request->input('rombelText', ''))) ? trim($request->input('rombelText')) : '-',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
            'jabatan' => 'nullable|string|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $rawUsername,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'jabatan' => $validated['jabatan'] ?? 'Guru BK',
            'phone' => $validated['wa'] ?? '-',
            'rombel' => $validated['rombelText'] ?? '-',
        ]);

        // Jika rombelText diisi dan bukan '-', kaitkan kelas-kelas tersebut ke Guru BK ini
        if (!empty($validated['rombelText']) && $validated['rombelText'] !== '-') {
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
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'jabatan' => $user->jabatan,
                    'email' => $user->email,
                    'wa' => $user->phone,
                    'rombel' => $user->rombel ? array_map('trim', explode(',', $user->rombel)) : [],
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Akun Guru BK ' . $user->name . ' (' . $user->email . ') berhasil dibuat!');
    }

    public function updateGuru(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rawEmail = strtolower(trim((string)$request->input('email', $user->email)));

        $request->merge([
            'email' => $rawEmail,
            'jabatan' => !empty(trim((string)$request->input('jabatan', ''))) ? trim($request->input('jabatan')) : ($user->jabatan ?: 'Guru BK'),
            'wa' => !empty(trim((string)$request->input('wa', ''))) ? trim($request->input('wa')) : '-',
            'rombelText' => !empty(trim((string)$request->input('rombelText', ''))) ? trim($request->input('rombelText')) : '-',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'jabatan' => 'nullable|string|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'jabatan' => $validated['jabatan'] ?? 'Guru BK',
            'phone' => $validated['wa'] ?? '-',
            'rombel' => $validated['rombelText'] ?? '-',
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update classrooms associated with this Guru BK
        Classroom::where('guru_bk_id', $user->id)->update(['guru_bk_name' => $user->name]);
        if (!empty($validated['rombelText']) && $validated['rombelText'] !== '-') {
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
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
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
            'jurusan_id' => 'nullable|exists:majors,id',
            'jurusan' => 'nullable|string|max:50',
            'waliKelas' => 'required|string|max:255',
            'guruBK' => 'nullable|string|max:255',
            'guruBkId' => 'nullable|exists:users,id',
        ]);

        $className = strtoupper(trim($validated['name']));

        // Cari atau buat Jurusan
        $jurusanId = null;
        $majorCode = '';
        if (!empty($validated['jurusan_id'])) {
            $major = Major::find($validated['jurusan_id']);
            if ($major) {
                $jurusanId = $major->id;
                $majorCode = $major->code;
            }
        }

        if (empty($majorCode) && !empty($validated['jurusan'])) {
            $majorCode = strtoupper(trim($validated['jurusan']));
            $major = Major::firstOrCreate(
                ['code' => $majorCode],
                ['name' => $majorCode, 'description' => 'Program Keahlian ' . $majorCode]
            );
            $jurusanId = $major->id;
        }

        // Cari Guru BK yang valid (hanya user dengan role 'guru')
        $guruUser = null;
        if (!empty($validated['guruBkId'])) {
            $guruUser = User::where('id', $validated['guruBkId'])->where('role', 'guru')->first();
        } elseif (!empty($validated['guruBK'])) {
            $guruUser = User::where('name', $validated['guruBK'])->where('role', 'guru')->first();
        }

        $classroom = Classroom::updateOrCreate(
            ['name' => $className],
            [
                'level' => $validated['level'],
                'major' => $majorCode,
                'jurusan_id' => $jurusanId,
                'wali_kelas' => $validated['waliKelas'],
                'guru_bk_id' => $guruUser ? $guruUser->id : null,
                'guru_bk_name' => $guruUser ? $guruUser->name : null,
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
                    'jurusan_id' => $classroom->jurusan_id,
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
            'jurusan_id' => 'nullable|exists:majors,id',
            'jurusan' => 'nullable|string|max:50',
            'waliKelas' => 'required|string|max:255',
            'guruBK' => 'nullable|string|max:255',
            'guruBkId' => 'nullable|exists:users,id',
        ]);

        $className = strtoupper(trim($validated['name']));

        // Cari atau buat Jurusan
        $jurusanId = null;
        $majorCode = '';
        if (!empty($validated['jurusan_id'])) {
            $major = Major::find($validated['jurusan_id']);
            if ($major) {
                $jurusanId = $major->id;
                $majorCode = $major->code;
            }
        }

        if (empty($majorCode) && !empty($validated['jurusan'])) {
            $majorCode = strtoupper(trim($validated['jurusan']));
            $major = Major::firstOrCreate(
                ['code' => $majorCode],
                ['name' => $majorCode, 'description' => 'Program Keahlian ' . $majorCode]
            );
            $jurusanId = $major->id;
        }

        // Cari Guru BK yang valid (hanya user dengan role 'guru')
        $guruUser = null;
        if (!empty($validated['guruBkId'])) {
            $guruUser = User::where('id', $validated['guruBkId'])->where('role', 'guru')->first();
        } elseif (!empty($validated['guruBK'])) {
            $guruUser = User::where('name', $validated['guruBK'])->where('role', 'guru')->first();
        }

        $oldName = $classroom->name;
        $oldGuruBkId = $classroom->guru_bk_id;

        $classroom->update([
            'name' => $className,
            'level' => $validated['level'],
            'major' => $majorCode,
            'jurusan_id' => $jurusanId,
            'wali_kelas' => $validated['waliKelas'],
            'guru_bk_id' => $guruUser ? $guruUser->id : null,
            'guru_bk_name' => $guruUser ? $guruUser->name : null,
        ]);

        // Sinkronisasi data siswa jika nama rombel kelas diperbarui
        if ($oldName !== $className) {
            Student::where('class_name', $oldName)->update([
                'class_name' => $className,
                'level' => $validated['level'],
                'major' => $majorCode,
            ]);
        }

        // Jika Guru BK berubah, bersihkan nama kelas dari rombel guru lama
        if ($oldGuruBkId && (!$guruUser || $guruUser->id !== $oldGuruBkId)) {
            $oldGuru = User::find($oldGuruBkId);
            if ($oldGuru && !empty($oldGuru->rombel)) {
                $oldRombels = array_map('trim', explode(',', $oldGuru->rombel));
                $filtered = array_filter($oldRombels, fn($r) => $r !== $oldName && $r !== $className);
                $oldGuru->update(['rombel' => implode(', ', array_values($filtered))]);
            }
        }

        // Sinkronisasi rombel Guru BK baru
        if ($guruUser) {
            $currentRombels = !empty($guruUser->rombel) ? array_map('trim', explode(',', $guruUser->rombel)) : [];
            // Ganti nama lama dengan nama baru jika ada
            $currentRombels = array_map(fn($r) => ($r === $oldName ? $className : $r), $currentRombels);
            if (!in_array($className, $currentRombels)) {
                $currentRombels[] = $className;
            }
            $guruUser->update(['rombel' => implode(', ', array_filter(array_unique($currentRombels)))]);
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

        // Proteksi jika masih ada siswa yang terdaftar di kelas ini
        $studentCount = Student::where('class_name', $name)->count();
        if ($studentCount > 0) {
            return redirect()->back()->with('error', "Rombel Kelas {$name} tidak dapat dihapus karena masih digunakan oleh {$studentCount} siswa aktif. Hapus atau pindahkan data siswa terlebih dahulu.");
        }

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

    public function storeStudent(Request $request)
    {
        $rawNis = trim((string)($request->input('nis') ?: $request->input('nisn') ?: ''));
        $request->merge(['nis' => $rawNis]);

        $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis',
            'name' => 'required|string|max:255',
            'className' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'parentWa' => 'nullable|string|max:50',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'className.required' => 'Kelas rombel wajib dipilih.',
        ]);

        $className = strtoupper(trim($request->input('className')));
        $level = 'X';
        if (str_starts_with($className, 'XII')) {
            $level = 'XII';
        } elseif (str_starts_with($className, 'XI')) {
            $level = 'XI';
        } elseif (str_starts_with($className, 'X')) {
            $level = 'X';
        }

        $major = 'RPL';
        $availableMajors = Major::orderByRaw('LENGTH(code) DESC')->pluck('code')->toArray();
        foreach ($availableMajors as $mCode) {
            if (str_contains($className, $mCode)) {
                $major = $mCode;
                break;
            }
        }

        Classroom::firstOrCreate(
            ['name' => $className],
            [
                'level' => "Kelas {$level} ({$level})",
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $className,
            ]
        );

        $rombelParts = explode(' ', trim($className));
        $rombel = end($rombelParts);

        $student = Student::create([
            'nis' => $rawNis,
            'name' => trim($request->input('name')),
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'gender' => $request->input('gender'),
            'parent_phone' => $request->input('parentWa') ?: null,
            'parent_name' => 'Orang Tua ' . trim($request->input('name')),
            'total_violation_points' => 0,
            'rombel' => $rombel,
            'status' => 'aktif',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'student' => $student]);
        }

        return redirect()->back()->with('success', "Data siswa {$student->name} berhasil ditambahkan!");
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $rawNis = trim((string)($request->input('nis') ?: $request->input('nisn') ?: $student->nis));
        $request->merge(['nis' => $rawNis]);

        $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'className' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'parentWa' => 'nullable|string|max:50',
            'poin' => 'nullable|integer|min:0',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'className.required' => 'Kelas rombel wajib dipilih.',
        ]);

        $className = strtoupper(trim($request->input('className')));
        $level = 'X';
        if (str_starts_with($className, 'XII')) {
            $level = 'XII';
        } elseif (str_starts_with($className, 'XI')) {
            $level = 'XI';
        } elseif (str_starts_with($className, 'X')) {
            $level = 'X';
        }

        $major = 'RPL';
        $availableMajors = Major::orderByRaw('LENGTH(code) DESC')->pluck('code')->toArray();
        foreach ($availableMajors as $mCode) {
            if (str_contains($className, $mCode)) {
                $major = $mCode;
                break;
            }
        }

        Classroom::firstOrCreate(
            ['name' => $className],
            [
                'level' => "Kelas {$level} ({$level})",
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $className,
            ]
        );

        $rombelParts = explode(' ', trim($className));
        $rombel = end($rombelParts);

        $student->update([
            'nis' => $rawNis,
            'name' => trim($request->input('name')),
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'gender' => $request->input('gender'),
            'parent_phone' => $request->input('parentWa') ?: null,
            'total_violation_points' => $request->has('poin') ? (int)$request->input('poin') : $student->total_violation_points,
            'rombel' => $rombel,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'student' => $student]);
        }

        return redirect()->back()->with('success', "Data siswa {$student->name} berhasil diperbarui!");
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $student->delete();

        return redirect()->back()->with('success', "Data siswa {$name} berhasil dihapus dari sistem.");
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ], [
            'file.required' => 'Pilih file spreadsheet (.xlsx, .csv, atau .txt) yang ingin diunggah.',
            'file.max' => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['xlsx'])) {
            $rows = $this->parseXlsxFile($file->getRealPath());
        } else {
            $rows = $this->parseCsvFile($file->getRealPath());
        }

        $overrideClass = $request->input('targetClass');
        $parsed = $this->mapRowsToStudents($rows, $overrideClass);

        return response()->json([
            'success' => true,
            'fileName' => $file->getClientOriginalName(),
            'count' => count($parsed['students']),
            'detectedClass' => $parsed['detectedClass'],
            'students' => $parsed['students'],
        ]);
    }

    public function importStudents(Request $request)
    {
        $studentsData = $request->input('students');
        $targetClass = $request->input('targetClass', 'AUTO');

        // Jika upload file langsung tanpa preview
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $rows = in_array($ext, ['xlsx']) ? $this->parseXlsxFile($file->getRealPath()) : $this->parseCsvFile($file->getRealPath());
            $parsed = $this->mapRowsToStudents($rows, $targetClass);
            $studentsData = $parsed['students'];
        }

        if (empty($studentsData) || !is_array($studentsData)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data siswa yang valid untuk diimpor.'], 422);
            }
            return redirect()->back()->with('error', 'Tidak ada data siswa yang valid untuk diimpor.');
        }

        $importedCount = 0;
        $availableMajors = Major::orderByRaw('LENGTH(code) DESC')->pluck('code')->toArray();

        foreach ($studentsData as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $nis = !empty($item['nis']) ? trim((string)$item['nis']) : (!empty($item['nisn']) ? trim((string)$item['nisn']) : 'NIS' . rand(10000000, 99999999));
            
            $className = ($targetClass !== 'AUTO' && !empty($targetClass))
                ? strtoupper(trim($targetClass))
                : (!empty($item['className']) ? strtoupper(trim($item['className'])) : 'X RPL 1');
            
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
            foreach ($availableMajors as $mCode) {
                if (str_contains($className, $mCode)) {
                    $major = $mCode;
                    break;
                }
            }

            // Pastikan Classroom ada di database
            Classroom::firstOrCreate(
                ['name' => $className],
                [
                    'level' => "Kelas {$level} ({$level})",
                    'major' => $major,
                    'wali_kelas' => 'Wali Kelas ' . $className,
                ]
            );

            $parentPhone = !empty($item['parentWa']) && $item['parentWa'] !== '-' ? $item['parentWa'] : ($item['parent_phone'] ?? null);

            $rombelParts = explode(' ', trim($className));
            $rombel = end($rombelParts);

            Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $level,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => $parentPhone,
                    'parent_name' => $item['parent_name'] ?? ('Orang Tua ' . trim($item['name'])),
                    'rombel' => $rombel,
                    'status' => 'aktif',
                ]
            );

            $importedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $importedCount,
                'targetClass' => $targetClass,
                'message' => "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima."
            ]);
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima.");
    }

    private function parseXlsxFile(string $filePath): array
    {
        $rows = [];
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $sharedStrings = [];
        $stringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXmlContent !== false) {
            $xml = @simplexml_load_string($stringsXmlContent);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $text = '';
                        foreach ($val->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetPath = 'xl/worksheets/sheet1.xml';
        if ($zip->locateName($sheetPath) === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/sheet\d+\.xml$#i', $name)) {
                    $sheetPath = $name;
                    break;
                }
            }
        }

        $sheetXmlContent = $zip->getFromName($sheetPath);
        $zip->close();

        if ($sheetXmlContent === false) {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        foreach ($sheetXml->sheetData->row as $row) {
            $rowCells = [];
            foreach ($row->c as $cell) {
                $cellVal = '';
                $type = (string)($cell['t'] ?? '');
                $val = (string)($cell->v ?? '');

                if ($type === 's') {
                    $idx = (int)$val;
                    $cellVal = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $cellVal = (string)$cell->is->t;
                } else {
                    $cellVal = $val;
                }

                $r = (string)($cell['r'] ?? '');
                $colIndex = 0;
                if (preg_match('/^([A-Z]+)(\d+)$/i', $r, $matches)) {
                    $colLetters = strtoupper($matches[1]);
                    for ($ci = 0; $ci < strlen($colLetters); $ci++) {
                        $colIndex = $colIndex * 26 + (ord($colLetters[$ci]) - 64);
                    }
                    $colIndex = max(1, $colIndex) - 1;
                } else {
                    $colIndex = count($rowCells);
                }

                $rowCells[$colIndex] = trim($cellVal);
            }

            if (!empty($rowCells)) {
                $maxKey = max(array_keys($rowCells));
                $normalizedRow = [];
                for ($k = 0; $k <= $maxKey; $k++) {
                    $normalizedRow[$k] = $rowCells[$k] ?? '';
                }
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    private function parseCsvFile(string $filePath): array
    {
        $rows = [];
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);
        if (empty($lines)) {
            return [];
        }

        $delimiter = ',';
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $semicolons = substr_count($trimmed, ';');
                $commas = substr_count($trimmed, ',');
                $tabs = substr_count($trimmed, "\t");
                $pipes = substr_count($trimmed, '|');

                if ($semicolons > $commas && $semicolons > $tabs) {
                    $delimiter = ';';
                } elseif ($tabs > $commas && $tabs > $semicolons) {
                    $delimiter = "\t";
                } elseif ($pipes > $commas && $pipes > $semicolons) {
                    $delimiter = '|';
                }
                break;
            }
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }
            $cols = str_getcsv($trimmed, $delimiter);
            $rows[] = array_map('trim', $cols);
        }

        return $rows;
    }

    private function mapRowsToStudents(array $rows, ?string $overrideClass = null): array
    {
        $students = [];
        $detectedClass = '';

        if (empty($rows)) {
            return ['detectedClass' => '', 'students' => []];
        }

        $availableClasses = Classroom::orderByRaw('LENGTH(name) DESC')->pluck('name')->toArray();
        $classRegex = '/\b(X|XI|XII)\s+([A-Z0-9]+)(\s+\d+)?\b/i';

        foreach (array_slice($rows, 0, 10) as $row) {
            $rowStr = strtoupper(implode(' ', $row));
            foreach ($availableClasses as $ac) {
                if (str_contains($rowStr, strtoupper($ac))) {
                    $detectedClass = $ac;
                    break 2;
                }
            }
            if (empty($detectedClass) && preg_match($classRegex, $rowStr, $match)) {
                $detectedClass = trim($match[0]);
            }
        }

        $headerIndex = -1;
        $colMap = [
            'nisn' => -1,
            'name' => -1,
            'class' => -1,
            'gender' => -1,
            'phone' => -1,
        ];

        foreach ($rows as $idx => $row) {
            $hasName = false;
            $hasNisn = false;
            foreach ($row as $colIdx => $colVal) {
                $normalized = strtolower(trim((string)$colVal));
                if (in_array($normalized, ['nama', 'nama siswa', 'nama lengkap', 'nama peserta didik', 'nama murid'])) {
                    $hasName = true;
                    $colMap['name'] = $colIdx;
                } elseif (in_array($normalized, ['nisn', 'nis', 'no induk', 'nomor induk'])) {
                    $hasNisn = true;
                    $colMap['nisn'] = $colIdx;
                } elseif (in_array($normalized, ['kelas', 'rombel', 'nama rombel', 'tingkat'])) {
                    $colMap['class'] = $colIdx;
                } elseif (in_array($normalized, ['jk', 'jenis kelamin', 'l/p', 'gender', 'sex'])) {
                    $colMap['gender'] = $colIdx;
                } elseif (in_array($normalized, ['no hp', 'no wa', 'no telepon', 'telepon', 'whatsapp', 'no hp wali', 'wa wali', 'kontak', 'hp', 'no wa orang tua'])) {
                    $colMap['phone'] = $colIdx;
                }
            }

            if ($hasName || ($hasNisn && count($row) >= 2)) {
                $headerIndex = $idx;
                break;
            }
        }

        $startIndex = 0;
        if ($headerIndex !== -1) {
            $startIndex = $headerIndex + 1;
        } else {
            $colMap['nisn'] = 0;
            $colMap['name'] = 1;
            $colMap['class'] = 2;
            $colMap['gender'] = 3;
            $colMap['phone'] = 4;
            $startIndex = 0;
        }

        for ($i = $startIndex; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row)) continue;

            $name = '';
            if ($colMap['name'] !== -1 && isset($row[$colMap['name']])) {
                $name = trim((string)$row[$colMap['name']]);
            } elseif (isset($row[1])) {
                $name = trim((string)$row[1]);
            }

            if ($name === '' || is_numeric($name) || preg_match('/^(mengetahui|kepala sekolah|wali kelas|total|jumlah|catatan)/i', $name)) {
                continue;
            }

            $nisn = '';
            if ($colMap['nisn'] !== -1 && isset($row[$colMap['nisn']])) {
                $nisn = trim((string)$row[$colMap['nisn']]);
            } elseif (isset($row[0])) {
                $nisn = trim((string)$row[0]);
            }
            $nisn = preg_replace('/[^a-zA-Z0-9]/', '', $nisn);
            if (empty($nisn) || strlen($nisn) < 4) {
                $nisn = '008' . str_pad((string)(count($students) + 1001), 7, '0', STR_PAD_LEFT);
            }

            $className = '';
            if (!empty($overrideClass) && $overrideClass !== 'AUTO') {
                $className = $overrideClass;
            } elseif ($colMap['class'] !== -1 && isset($row[$colMap['class']]) && trim((string)$row[$colMap['class']]) !== '') {
                $className = strtoupper(trim((string)$row[$colMap['class']]));
            } elseif (!empty($detectedClass)) {
                $className = $detectedClass;
            } else {
                $className = 'X RPL 1';
            }

            $gender = 'L';
            if ($colMap['gender'] !== -1 && isset($row[$colMap['gender']])) {
                $rawG = strtoupper(trim((string)$row[$colMap['gender']]));
                if (str_starts_with($rawG, 'P') || str_contains($rawG, 'PEREMPUAN') || str_contains($rawG, 'WANITA')) {
                    $gender = 'P';
                } else {
                    $gender = 'L';
                }
            }

            $phone = '';
            if ($colMap['phone'] !== -1 && isset($row[$colMap['phone']])) {
                $phone = trim((string)$row[$colMap['phone']]);
            }

            $students[] = [
                'nis' => $nisn,
                'nisn' => $nisn,
                'name' => $name,
                'className' => $className,
                'gender' => $gender,
                'parentWa' => !empty($phone) ? $phone : '-'
            ];
        }

        return [
            'detectedClass' => $detectedClass,
            'students' => $students
        ];
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

    public function storeJurusan(Request $request)
    {
        $request->merge([
            'code' => strtoupper(trim((string)$request->input('code', ''))),
            'name' => trim((string)$request->input('name', '')),
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'code.required' => 'Kode jurusan wajib diisi.',
            'code.unique' => 'Kode jurusan sudah terdaftar. Silakan gunakan kode lain.',
            'name.required' => 'Nama lengkap jurusan wajib diisi.',
        ]);

        $major = Major::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'major' => $major]);
        }

        return redirect()->back()->with('success', 'Jurusan ' . $major->code . ' berhasil ditambahkan!');
    }

    public function updateJurusan(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $request->merge([
            'code' => strtoupper(trim((string)$request->input('code', ''))),
            'name' => trim((string)$request->input('name', '')),
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors,code,' . $major->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'code.required' => 'Kode jurusan wajib diisi.',
            'code.unique' => 'Kode jurusan sudah terdaftar. Silakan gunakan kode lain.',
            'name.required' => 'Nama lengkap jurusan wajib diisi.',
        ]);

        $oldCode = $major->code;
        $major->update($validated);

        // Jika kode jurusan diubah, perbarui juga rombel kelas dan siswa yang terikat
        if ($oldCode !== $major->code) {
            Classroom::where('major', $oldCode)->update(['major' => $major->code]);
            Student::where('major', $oldCode)->update(['major' => $major->code]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'major' => $major]);
        }

        return redirect()->back()->with('success', 'Jurusan ' . $major->code . ' berhasil diperbarui!');
    }

    public function deleteJurusan($id)
    {
        $major = Major::findOrFail($id);
        $code = $major->code;

        // Cek apakah masih digunakan oleh rombel kelas aktif atau siswa
        $usedClasses = $major->classrooms()->count();
        $usedStudents = $major->students()->count();
        if ($usedClasses > 0 || $usedStudents > 0) {
            return redirect()->back()->with('error', "Jurusan {$code} tidak dapat dihapus karena masih digunakan oleh {$usedClasses} rombel kelas dan {$usedStudents} data siswa.");
        }

        $major->delete();

        return redirect()->back()->with('success', 'Jurusan ' . $code . ' berhasil dihapus.');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'kepala_sekolah' => 'required|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'semester' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $val) {
            \App\Models\Setting::set($key, (string)$val);
        }

        return redirect()->back()->with('success', 'Pengaturan sekolah, kepala sekolah, dan semester berhasil disimpan.');
    }
}
