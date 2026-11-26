<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Sanction;
use App\Models\ConsultationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'totalStudents' => Student::active()->count(),
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
        $kelasList = Classroom::with(['guruBk', 'jurusan'])->withCount(['students' => fn ($q) => $q->active()])->orderBy('name')->get()->map(function ($c) {
            $guruName = '-';
            if ($c->guruBk && $c->guruBk->role === 'guru') {
                $guruName = $c->guruBk->name;
            } elseif (!empty($c->guru_bk_name) && !in_array($c->guru_bk_name, ['Admin IT Sekolah', 'Admin BK'])) {
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

        // 3. Ambil data Siswa Aktif dari database (tidak termasuk alumni lulus)
        $siswaList = Student::active()->orderBy('class_name')->orderedByAbsen()->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'noAbsen' => $s->no_absen,
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
        $sanksiList = Schema::hasTable('sanctions')
            ? Sanction::orderBy('sort_order')->orderBy('id')->get()
            : collect();

        $alumniList = Student::graduated()
            ->orderByDesc('graduation_year')
            ->orderBy('name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nis' => $s->nis,
                    'nisn' => $s->nis,
                    'name' => $s->name,
                    'lastClassName' => $s->last_class_name ?: $s->class_name,
                    'graduationYear' => $s->graduation_year,
                    'major' => $s->major,
                    'gender' => $s->gender,
                    'parentWa' => $s->parent_phone ?? '-',
                    'poin' => $s->total_violation_points,
                    'status' => $s->status,
                ];
            });

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
            'initialSanksiList' => $sanksiList,
            'initialAlumniList' => $alumniList,
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
            'wa' => $request->has('wa') ? (!empty(trim((string)$request->input('wa', ''))) ? trim($request->input('wa')) : '-') : ($user->phone ?: '-'),
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

        // Kaitkan siswa yang sudah memiliki nama rombel ini ke classroom_id baru
        Student::where('class_name', $className)->update(['classroom_id' => $classroom->id]);

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
                    'studentCount' => Student::active()->where('class_name', $classroom->name)->count(),
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
                'classroom_id' => $classroom->id,
                'level' => $validated['level'],
                'major' => $majorCode,
            ]);
        } else {
            Student::where('class_name', $className)->whereNull('classroom_id')->update([
                'classroom_id' => $classroom->id,
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
        $studentCount = Student::active()->where(function ($q) use ($classroom, $name) {
            $q->where('classroom_id', $classroom->id)->orWhere('class_name', $name);
        })->count();
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
            'noAbsen' => 'nullable|integer|min:1|max:999',
        ], [
            'nis.required' => 'NISN wajib diisi.',
            'nis.unique' => 'NISN sudah terdaftar.',
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

        $cls = Classroom::firstOrCreate(
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
            'classroom_id' => $cls->id,
            'nis' => $rawNis,
            'no_absen' => $request->filled('noAbsen') ? (int)$request->input('noAbsen') : ($request->filled('no_absen') ? (int)$request->input('no_absen') : null),
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
            'noAbsen' => 'nullable|integer|min:1|max:999',
        ], [
            'nis.required' => 'NISN wajib diisi.',
            'nis.unique' => 'NISN sudah digunakan oleh siswa lain.',
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

        $cls = Classroom::firstOrCreate(
            ['name' => $className],
            [
                'level' => "Kelas {$level} ({$level})",
                'major' => $major,
                'wali_kelas' => 'Wali Kelas ' . $className,
            ]
        );

        $rombelParts = explode(' ', trim($className));
        $rombel = end($rombelParts);

        $noAbsen = ($request->has('noAbsen') || $request->has('no_absen'))
            ? ($request->filled('noAbsen') ? (int)$request->input('noAbsen') : ($request->filled('no_absen') ? (int)$request->input('no_absen') : null))
            : $student->no_absen;

        $student->update([
            'classroom_id' => $cls->id,
            'nis' => $rawNis,
            'no_absen' => $noAbsen,
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

        if ($ext === 'xls') {
            return response()->json([
                'success' => false,
                'message' => 'Format .XLS lama tidak didukung. Silakan simpan ulang file sebagai .XLSX atau .CSV.',
            ], 422);
        }

        $overrideClass = $request->input('targetClass');
        $parsed = $this->extractStudentsFromUploadedFile($file, $overrideClass);

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
            $parsed = $this->extractStudentsFromUploadedFile($request->file('file'), $targetClass);
            $studentsData = $parsed['students'];
        }

        if (empty($studentsData) || !is_array($studentsData)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data siswa yang valid untuk diimpor.'], 422);
            }
            return redirect()->back()->with('error', 'Tidak ada data siswa yang valid untuk diimpor.');
        }

        $importedCount = 0;
        $perClassCounter = [];
        $majorsByCode = Major::orderByRaw('LENGTH(code) DESC')->get()->keyBy('code');
        $availableMajors = $majorsByCode->keys()->toArray();

        foreach ($studentsData as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $nis = !empty($item['nis']) ? trim((string)$item['nis']) : (!empty($item['nisn']) ? trim((string)$item['nisn']) : '008' . rand(1000000, 9999999));

            $className = ($targetClass !== 'AUTO' && !empty($targetClass))
                ? strtoupper(trim($targetClass))
                : (!empty($item['className']) ? strtoupper(trim($item['className'])) : 'X RPL 1');

            // Ekstrak level & jurusan dari nama kelas
            $levelNum = '10';
            $roman = 'X';
            if (str_starts_with($className, 'XII') || str_contains($className, ' 12 ') || str_contains($className, '12')) {
                $levelNum = '12';
                $roman = 'XII';
            } elseif (str_starts_with($className, 'XI') || str_contains($className, ' 11 ') || str_contains($className, '11')) {
                $levelNum = '11';
                $roman = 'XI';
            } elseif (str_starts_with($className, 'X') || str_contains($className, ' 10 ') || str_contains($className, '10')) {
                $levelNum = '10';
                $roman = 'X';
            }

            $major = $availableMajors[0] ?? 'UMUM';
            foreach ($availableMajors as $mCode) {
                if (str_contains($className, strtoupper($mCode))) {
                    $major = $mCode;
                    break;
                }
            }
            $majorModel = $majorsByCode->get($major);

            // Pastikan Classroom ada di database
            $cls = Classroom::firstOrCreate(
                ['name' => $className],
                [
                    'level' => "Kelas {$levelNum} ({$roman})",
                    'major' => $major,
                    'jurusan_id' => $majorModel?->id,
                    'wali_kelas' => 'Wali Kelas ' . $className,
                ]
            );

            $parentPhone = !empty($item['parentWa']) && $item['parentWa'] !== '-' ? $item['parentWa'] : (!empty($item['parent_phone']) ? $item['parent_phone'] : null);

            $rombelParts = explode(' ', trim($className));
            $rombel = end($rombelParts);

            $perClassCounter[$className] = ($perClassCounter[$className] ?? 0) + 1;
            $rawNoAbsen = $item['noAbsen'] ?? ($item['no_absen'] ?? null);
            $noAbsen = (is_numeric($rawNoAbsen) && (int)$rawNoAbsen > 0) ? (int)$rawNoAbsen : $perClassCounter[$className];

            $student = Student::updateOrCreate(
                ['nis' => $nis],
                [
                    'classroom_id' => $cls->id,
                    'no_absen' => $noAbsen,
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $levelNum,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => $parentPhone,
                    'parent_name' => $item['parent_name'] ?? ('Orang Tua ' . trim($item['name'])),
                    'rombel' => $rombel,
                    'status' => 'aktif',
                ]
            );

            \App\Models\Attendance::firstOrCreate(
                ['student_id' => $student->id, 'date' => now()->toDateString()],
                ['status' => 'Hadir', 'check_in_time' => '07:00', 'class_snapshot' => $className, 'notes' => 'Tepat waktu']
            );

            $importedCount++;
        }

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'count' => $importedCount,
                'targetClass' => $targetClass,
                'message' => "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima."
            ]);
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$importedCount} data siswa ke database BK Prima.");
    }

    private function extractStudentsFromUploadedFile(\Illuminate\Http\UploadedFile $file, ?string $overrideClass = null): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $sheets = [];

        if ($ext === 'xlsx' && class_exists('\ZipArchive')) {
            $sheets = $this->parseXlsxSheets($file->getRealPath());
        } else {
            $sheets = [[
                'sheetName' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'rows' => $this->parseCsvFile($file->getRealPath()),
            ]];
        }

        $allStudents = [];
        $detectedClass = '';

        foreach ($sheets as $sheet) {
            $parsed = $this->mapRowsToStudents(
                $sheet['rows'] ?? [],
                $overrideClass,
                $sheet['sheetName'] ?? null,
                count($allStudents)
            );
            if (!empty($parsed['students'])) {
                if (empty($detectedClass) && !empty($parsed['detectedClass'])) {
                    $detectedClass = $parsed['detectedClass'];
                }
                foreach ($parsed['students'] as $st) {
                    $allStudents[] = $st;
                }
            }
        }

        return [
            'detectedClass' => $detectedClass,
            'students' => $allStudents,
        ];
    }

    private function parseXlsxSheets(string $filePath): array
    {
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

        $sheetEntries = [];
        $relTargets = [];

        $relsXmlContent = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($relsXmlContent !== false) {
            $relsXml = @simplexml_load_string($relsXmlContent);
            if ($relsXml) {
                foreach ($relsXml->Relationship as $rel) {
                    $id = (string)($rel['Id'] ?? '');
                    $target = ltrim((string)($rel['Target'] ?? ''), '/');
                    if ($id !== '' && $target !== '') {
                        if (!str_starts_with($target, 'xl/')) {
                            $target = 'xl/' . $target;
                        }
                        $relTargets[$id] = $target;
                    }
                }
            }
        }

        $workbookXmlContent = $zip->getFromName('xl/workbook.xml');
        if ($workbookXmlContent !== false) {
            $wbXml = @simplexml_load_string($workbookXmlContent);
            if ($wbXml && isset($wbXml->sheets->sheet)) {
                foreach ($wbXml->sheets->sheet as $sheetNode) {
                    $sheetName = (string)($sheetNode['name'] ?? '');
                    $rAttrs = $sheetNode->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                    $rId = (string)($rAttrs['id'] ?? '');
                    $path = $relTargets[$rId] ?? '';
                    if ($path !== '' && $zip->locateName($path) !== false) {
                        $sheetEntries[] = ['name' => $sheetName, 'path' => $path];
                    }
                }
            }
        }

        if (empty($sheetEntries)) {
            $fallbackPaths = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/sheet\d+\.xml$#i', $name)) {
                    $fallbackPaths[] = $name;
                }
            }
            natsort($fallbackPaths);
            foreach ($fallbackPaths as $path) {
                $sheetEntries[] = ['name' => pathinfo($path, PATHINFO_FILENAME), 'path' => $path];
            }
        }

        $sheets = [];
        foreach ($sheetEntries as $entry) {
            $sheetXmlContent = $zip->getFromName($entry['path']);
            if ($sheetXmlContent === false) {
                continue;
            }
            $sheetXml = @simplexml_load_string($sheetXmlContent);
            if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
                continue;
            }

            $rows = [];
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

            if (!empty($rows)) {
                $sheets[] = [
                    'sheetName' => $entry['name'],
                    'rows' => $rows,
                ];
            }
        }

        $zip->close();
        return $sheets;
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

    private function mapRowsToStudents(array $rows, ?string $overrideClass = null, ?string $sheetName = null, int $generatedNisOffset = 0): array
    {
        $students = [];
        $detectedClass = '';

        if (empty($rows)) {
            return ['detectedClass' => '', 'students' => []];
        }

        $availableClasses = Classroom::orderByRaw('LENGTH(name) DESC')->pluck('name')->toArray();
        $classRegex = '/\b(X|XI|XII)\s+([A-Z0-9]+)(\s+\d+)?\b/i';

        foreach (array_slice($rows, 0, 12) as $row) {
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

        if (empty($detectedClass) && !empty($sheetName)) {
            $upperSheet = strtoupper(trim($sheetName));
            foreach ($availableClasses as $ac) {
                if (str_contains($upperSheet, strtoupper($ac))) {
                    $detectedClass = $ac;
                    break;
                }
            }
            if (empty($detectedClass) && preg_match($classRegex, $upperSheet, $match)) {
                $detectedClass = trim($match[0]);
            }
        }

        $headerIndex = -1;
        $colMap = [
            'no_absen' => -1,
            'nisn' => -1,
            'name' => -1,
            'class' => -1,
            'gender' => -1,
            'phone' => -1,
        ];
        $nisnPriority = 99;
        $namePriority = 99;
        $classPriority = 99;
        $phonePriority = 99;
        $fallbackPhoneCols = [];

        foreach ($rows as $idx => $row) {
            $hasName = false;
            $hasNisn = false;
            $rowColMap = ['no_absen' => -1, 'nisn' => -1, 'name' => -1, 'class' => -1, 'gender' => -1, 'phone' => -1];
            $nisnPriority = 99;
            $namePriority = 99;
            $classPriority = 99;
            $phonePriority = 99;
            $fallbackPhoneCols = [];

            foreach ($row as $colIdx => $colVal) {
                $normalized = strtolower(preg_replace('/\s+/', ' ', trim((string)$colVal)));
                $alpha = preg_replace('/[^a-z0-9]/', '', $normalized);
                if ($alpha === '') {
                    continue;
                }

                if (in_array($alpha, ['no', 'noabsen', 'nomorabsen', 'nourut', 'nomorurut', 'absen', 'urut']) && $rowColMap['no_absen'] === -1) {
                    $rowColMap['no_absen'] = $colIdx;
                } elseif (in_array($alpha, ['nisn', 'nisnisn'])) {
                    $hasNisn = true;
                    $rowColMap['nisn'] = $colIdx;
                    $nisnPriority = 1;
                } elseif (in_array($alpha, ['nis', 'nipd', 'noinduk', 'nomorinduk', 'noinduksiswa']) && $nisnPriority > 2) {
                    $hasNisn = true;
                    $rowColMap['nisn'] = $colIdx;
                    $nisnPriority = 2;
                } elseif (
                    !preg_match('/(ayah|ibu|wali|orangtua|ortu|sekolah|rombel|kelas|jurusan|guru)/', $alpha)
                    && in_array($alpha, ['namalengkap', 'namasiswa', 'namasiswai', 'namapesertadidik', 'namamurid', 'namasiswapesertadidik'])
                ) {
                    $hasName = true;
                    $rowColMap['name'] = $colIdx;
                    $namePriority = 1;
                } elseif (
                    !preg_match('/(ayah|ibu|wali|orangtua|ortu|sekolah|rombel|kelas|jurusan|guru)/', $alpha)
                    && in_array($alpha, ['nama', 'pesertadidik', 'studentname', 'fullname'])
                    && $namePriority > 2
                ) {
                    $hasName = true;
                    $rowColMap['name'] = $colIdx;
                    $namePriority = 2;
                } elseif (in_array($alpha, ['rombelsaatini', 'kelassaatini', 'kelasrombel', 'namarombel', 'kelas', 'rombel', 'kls'])) {
                    $rowColMap['class'] = $colIdx;
                    $classPriority = 1;
                } elseif ($alpha === 'tingkat' && $classPriority > 2) {
                    $rowColMap['class'] = $colIdx;
                    $classPriority = 2;
                } elseif (in_array($alpha, ['jk', 'jeniskelamin', 'lp', 'kelamin', 'gender', 'sex'])) {
                    $rowColMap['gender'] = $colIdx;
                } elseif (in_array($alpha, ['nowawali', 'wawali', 'nowaorangtua', 'nowaortu', 'nohpwali', 'hpwali', 'nohporangtua', 'hportu', 'teleponwali', 'kontakwali', 'nohpibu', 'hpibu', 'nohpayah', 'hpayah'])) {
                    $fallbackPhoneCols[] = $colIdx;
                    if ($phonePriority > 1) {
                        $rowColMap['phone'] = $colIdx;
                        $phonePriority = 1;
                    }
                } elseif (in_array($alpha, ['nowa', 'whatsapp', 'nohp', 'hp', 'notelepon', 'telepon', 'telp', 'kontak'])) {
                    $fallbackPhoneCols[] = $colIdx;
                    if ($phonePriority > 2) {
                        $rowColMap['phone'] = $colIdx;
                        $phonePriority = 2;
                    }
                }
            }

            if ($hasName || ($hasNisn && count($row) >= 2)) {
                $colMap = $rowColMap;
                $headerIndex = $idx;
                break;
            }
        }

        $startIndex = 0;
        if ($headerIndex !== -1) {
            $startIndex = $headerIndex + 1;
        } else {
            $firstRow = $rows[0] ?? [];
            $col0 = trim((string)($firstRow[0] ?? ''));
            $col1 = trim((string)($firstRow[1] ?? ''));
            if (is_numeric($col0) && (int)$col0 > 0 && (int)$col0 <= 100 && preg_match('/^\d{5,}$/', $col1)) {
                $colMap['no_absen'] = 0;
                $colMap['nisn'] = 1;
                $colMap['name'] = 2;
                $colMap['class'] = 3;
                $colMap['gender'] = 4;
                $colMap['phone'] = 5;
            } else {
                $colMap['nisn'] = 0;
                $colMap['name'] = 1;
                $colMap['class'] = 2;
                $colMap['gender'] = 3;
                $colMap['phone'] = 4;
            }
            $startIndex = 0;
        }

        $perClassCounter = [];

        for ($i = $startIndex; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row)) continue;

            $name = '';
            if ($colMap['name'] !== -1 && isset($row[$colMap['name']])) {
                $name = preg_replace('/\s+/', ' ', trim((string)$row[$colMap['name']]));
            } elseif ($headerIndex === -1 && isset($row[1])) {
                $name = preg_replace('/\s+/', ' ', trim((string)$row[1]));
            }

            if (
                $name === ''
                || mb_strlen($name) < 2
                || preg_match('/^[\d\W_]+$/', $name)
                || in_array(strtolower($name), ['nama', 'nama siswa', 'nama lengkap', 'nama peserta didik', 'nama murid', 'l/p', 'jk', 'nisn', 'nis', 'no', 'keterangan', 'status', 'jumlah', 'total'])
                || preg_match('/^(mengetahui|kepala\s*sekolah|wakil\s*kepala|wali\s*kelas|guru\s*bk|ketua|sekretaris|bendahara|total|jumlah|sub\s*total|rekap|rata-rata|persentase|catatan|keterangan|ket\.?|nip\.?|nuptk\.?|laki-laki|perempuan|hadir|sakit|izin|alpa|jember,|tanggal|dicetak|sumber\s*:|daftar\s*hadir|absensi|kelas\s*:)/i', $name)
                || preg_match('/^(l|p)\s*[:=]/i', $name)
            ) {
                continue;
            }

            $nisn = '';
            if ($colMap['nisn'] !== -1 && isset($row[$colMap['nisn']])) {
                $nisn = trim((string)$row[$colMap['nisn']]);
            } elseif ($headerIndex === -1 && isset($row[0])) {
                $nisn = trim((string)$row[0]);
            }
            $nisn = preg_replace('/[^a-zA-Z0-9]/', '', $nisn);
            if (empty($nisn) || strlen($nisn) < 4) {
                $nisn = '008' . str_pad((string)($generatedNisOffset + count($students) + 1001), 7, '0', STR_PAD_LEFT);
            }

            $className = '';
            if (!empty($overrideClass) && $overrideClass !== 'AUTO') {
                $className = $overrideClass;
            } elseif ($colMap['class'] !== -1 && isset($row[$colMap['class']]) && trim((string)$row[$colMap['class']]) !== '') {
                $className = strtoupper(preg_replace('/\s+/', ' ', trim((string)$row[$colMap['class']])));
            } elseif (!empty($detectedClass)) {
                $className = $detectedClass;
            } else {
                $className = 'X RPL 1';
            }

            $perClassCounter[$className] = ($perClassCounter[$className] ?? 0) + 1;
            $noAbsen = $perClassCounter[$className];
            if ($colMap['no_absen'] !== -1 && isset($row[$colMap['no_absen']])) {
                $rawNo = preg_replace('/[^0-9]/', '', (string)$row[$colMap['no_absen']]);
                if (!empty($rawNo) && (int)$rawNo > 0) {
                    $noAbsen = (int)$rawNo;
                }
            }

            $gender = 'L';
            if ($colMap['gender'] !== -1 && isset($row[$colMap['gender']])) {
                $rawG = strtoupper(trim((string)$row[$colMap['gender']]));
                if (str_starts_with($rawG, 'P') || str_starts_with($rawG, 'F') || str_contains($rawG, 'PEREMPUAN') || str_contains($rawG, 'WANITA')) {
                    $gender = 'P';
                } else {
                    $gender = 'L';
                }
            }

            $phone = '';
            if ($colMap['phone'] !== -1 && isset($row[$colMap['phone']])) {
                $phone = trim((string)$row[$colMap['phone']]);
            }
            if (($phone === '' || $phone === '-' || $phone === '0') && !empty($fallbackPhoneCols)) {
                foreach ($fallbackPhoneCols as $fIdx) {
                    $cand = trim((string)($row[$fIdx] ?? ''));
                    if ($cand !== '' && $cand !== '-' && $cand !== '0') {
                        $phone = $cand;
                        break;
                    }
                }
            }
            if ($phone !== '' && $phone !== '-' && $phone !== '0') {
                $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
                if (preg_match('/^8\d{8,12}$/', $digitsOnly)) {
                    $phone = '0' . $digitsOnly;
                }
            } else {
                $phone = '';
            }

            $students[] = [
                'noAbsen' => $noAbsen,
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

    public function storeSanksi(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name', '')),
            'description' => trim((string) $request->input('description', '')) ?: null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sanctions,name',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama tindakan/sanksi wajib diisi.',
            'name.unique' => 'Tindakan/sanksi tersebut sudah terdaftar.',
        ]);

        $sanction = Sanction::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? (Sanction::max('sort_order') + 1),
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : true,
        ]);

        return redirect()->back()->with('success', 'Tindakan "' . $sanction->name . '" berhasil ditambahkan!');
    }

    public function updateSanksi(Request $request, $id)
    {
        $sanction = Sanction::findOrFail($id);

        $request->merge([
            'name' => trim((string) $request->input('name', '')),
            'description' => trim((string) $request->input('description', '')) ?: null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sanctions,name,' . $sanction->id,
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama tindakan/sanksi wajib diisi.',
            'name.unique' => 'Tindakan/sanksi tersebut sudah terdaftar.',
        ]);

        $sanction->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $sanction->sort_order,
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : $sanction->is_active,
        ]);

        return redirect()->back()->with('success', 'Tindakan "' . $sanction->name . '" berhasil diperbarui!');
    }

    public function deleteSanksi($id)
    {
        $sanction = Sanction::findOrFail($id);
        $name = $sanction->name;
        $sanction->delete();

        return redirect()->back()->with('success', 'Tindakan "' . $name . '" berhasil dihapus.');
    }

    public function storeAlumni(Request $request)
    {
        $studentId = $request->input('student_id');
        $level = trim((string) $request->input('level', ''));
        $graduationYear = (int) ($request->input('graduation_year') ?: now()->year);

        if ($level !== '') {
            $roman = match (strtoupper($level)) {
                '10', 'X' => 'X',
                '11', 'XI' => 'XI',
                '12', 'XII' => 'XII',
                default => null,
            };
            $numLevel = match ($roman) {
                'X' => '10',
                'XI' => '11',
                'XII' => '12',
                default => null,
            };

            if (!$roman || !$numLevel) {
                return redirect()->back()->withErrors(['level' => 'Angkatan tidak valid.']);
            }

            $students = Student::active()
                ->where(function ($q) use ($roman, $numLevel) {
                    $q->where('level', $numLevel)
                      ->orWhere('level', $roman)
                      ->orWhere('class_name', 'like', $roman . ' %');
                })
                ->get();

            if ($students->isEmpty()) {
                return redirect()->back()->withErrors(['level' => "Tidak ada siswa aktif pada angkatan Kelas {$roman}."]);
            }

            $count = 0;
            foreach ($students as $student) {
                $lastClass = $student->class_name ?: "{$roman} {$student->major} {$student->rombel}";
                $student->update([
                    'status' => 'lulus',
                    'graduation_year' => $graduationYear,
                    'last_class_name' => $lastClass,
                ]);
                $count++;
            }

            return redirect()->back()->with('success', "Berhasil meluluskan {$count} siswa angkatan Kelas {$roman} ke Arsip Alumni.");
        }

        if ($studentId) {
            $student = Student::findOrFail($studentId);
            $lastClass = trim((string) ($request->input('last_class_name') ?: $student->class_name));
            $student->update([
                'status' => 'lulus',
                'graduation_year' => $graduationYear,
                'last_class_name' => $lastClass,
            ]);

            return redirect()->back()->with('success', 'Siswa "' . $student->name . '" berhasil diluluskan ke Arsip Alumni.');
        }

        $rawNis = trim((string) ($request->input('nisn') ?: $request->input('nis') ?: ''));
        $request->merge(['nis' => $rawNis]);

        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis',
            'name' => 'required|string|max:255',
            'last_class_name' => 'required|string|max:100',
            'graduation_year' => 'required|integer|min:2000|max:2100',
            'gender' => 'nullable|in:L,P',
            'parent_phone' => 'nullable|string|max:50',
        ], [
            'nis.required' => 'NISN wajib diisi.',
            'nis.unique' => 'NISN sudah terdaftar.',
            'name.required' => 'Nama alumni wajib diisi.',
            'last_class_name.required' => 'Kelas terakhir wajib diisi.',
        ]);

        $parts = explode(' ', trim($validated['last_class_name']));
        $majorCode = strtoupper($parts[1] ?? 'RPL');
        $rombelNum = end($parts) ?: '1';

        Student::create([
            'nis' => $validated['nis'],
            'name' => $validated['name'],
            'class_name' => $validated['last_class_name'],
            'last_class_name' => $validated['last_class_name'],
            'level' => '12',
            'major' => $majorCode,
            'rombel' => $rombelNum,
            'gender' => $validated['gender'] ?? 'L',
            'parent_phone' => $validated['parent_phone'] ?? '-',
            'status' => 'lulus',
            'graduation_year' => $validated['graduation_year'],
            'total_violation_points' => 0,
        ]);

        return redirect()->back()->with('success', 'Data alumni "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function updateAlumni(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        if ($request->boolean('restore_active')) {
            $student->update([
                'status' => 'aktif',
                'graduation_year' => null,
            ]);

            return redirect()->back()->with('success', 'Siswa "' . $student->name . '" berhasil dipulihkan ke daftar siswa aktif.');
        }

        $rawNis = trim((string) ($request->input('nisn') ?: $request->input('nis') ?: $student->nis));
        $request->merge(['nis' => $rawNis]);

        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'last_class_name' => 'required|string|max:100',
            'graduation_year' => 'required|integer|min:2000|max:2100',
        ], [
            'nis.required' => 'NISN wajib diisi.',
            'nis.unique' => 'NISN sudah digunakan oleh siswa lain.',
            'name.required' => 'Nama alumni wajib diisi.',
        ]);

        $parts = explode(' ', trim($validated['last_class_name']));
        $majorCode = strtoupper($parts[1] ?? $student->major ?? 'RPL');

        $student->update([
            'nis' => $validated['nis'],
            'name' => $validated['name'],
            'last_class_name' => $validated['last_class_name'],
            'class_name' => $validated['last_class_name'],
            'major' => $majorCode,
            'graduation_year' => $validated['graduation_year'],
        ]);

        return redirect()->back()->with('success', 'Data alumni "' . $student->name . '" berhasil diperbarui.');
    }

    public function deleteAlumni($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $student->delete();

        return redirect()->back()->with('success', 'Data arsip alumni "' . $name . '" berhasil dihapus.');
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
