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
                'studentCount' => $count,
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

        // 5. Auto-seed default majors jika kosong
        if (Major::count() === 0) {
            $defaultMajors = [
                ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak / PPLG', 'description' => 'Pengembangan software, web, mobile, dan game'],
                ['code' => 'TSM', 'name' => 'Teknik Sepeda Motor', 'description' => 'Teknik otomotif kendaraan roda dua dan mekanik'],
                ['code' => 'DKV', 'name' => 'Desain Komunikasi Visual', 'description' => 'Kreatif grafis, animasi, video editing, dan media digital'],
                ['code' => 'TKJ', 'name' => 'Teknik Komputer & Jaringan', 'description' => 'Infrastruktur jaringan, routing, server, dan cyber security'],
                ['code' => 'AKL', 'name' => 'Akuntansi & Keuangan Lembaga', 'description' => 'Pembukuan keuangan, perpajakan, dan perbankan'],
                ['code' => 'OTKP', 'name' => 'Otomatisasi & Tata Kelola Perkantoran', 'description' => 'Manajemen perkantoran, administrasi bisnis, dan korespondensi'],
            ];
            foreach ($defaultMajors as $dm) {
                Major::create($dm);
            }
        }

        $jurusanList = Major::orderBy('code')->get();

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
            'consultations' => $consultationRequests,
            'unreadConsultationsCount' => $unreadConsultationsCount,
            'initialGuruBkList' => $guruBkList,
            'initialKelasList' => $kelasList,
            'initialSiswaList' => $siswaList,
            'initialJurusanList' => $jurusanList,
        ]);
    }

    public function storeGuru(Request $request)
    {
        $rawUsername = trim((string)$request->input('username', ''));
        if (empty($rawUsername) || $rawUsername === '-') {
            $baseUser = preg_replace('/[^a-z0-9]/', '', strtolower($request->input('name', 'guru_bk')));
            if (empty($baseUser)) $baseUser = 'guru_bk';
            $rawUsername = $baseUser;
            $counter = 1;
            while (User::where('username', $rawUsername)->exists()) {
                $rawUsername = $baseUser . $counter++;
            }
        } else {
            $rawUsername = strtolower($rawUsername);
        }

        $request->merge([
            'username' => $rawUsername,
            'password' => !empty($request->input('password')) ? $request->input('password') : '123456',
            'jabatan' => !empty(trim((string)$request->input('jabatan', ''))) ? trim($request->input('jabatan')) : 'Guru BK',
            'nip' => !empty(trim((string)$request->input('nip', ''))) ? trim($request->input('nip')) : '-',
            'nuptk' => !empty(trim((string)$request->input('nuptk', ''))) ? trim($request->input('nuptk')) : '-',
            'wa' => !empty(trim((string)$request->input('wa', ''))) ? trim($request->input('wa')) : '-',
            'email' => !empty(trim((string)$request->input('email', ''))) && $request->input('email') !== '-' ? trim($request->input('email')) : ($rawUsername . '@guru.smk.belajar.id'),
            'rombelText' => !empty(trim((string)$request->input('rombelText', ''))) ? trim($request->input('rombelText')) : '-',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ]);

        $username = $validated['username'];
        $email = $validated['email'];

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'nip' => $validated['nip'] ?? '-',
            'nuptk' => $validated['nuptk'] ?? '-',
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

        $rawUsername = trim((string)$request->input('username', ''));
        if (empty($rawUsername) || $rawUsername === '-') {
            $rawUsername = $user->username;
        } else {
            $rawUsername = strtolower($rawUsername);
        }

        $request->merge([
            'username' => $rawUsername,
            'jabatan' => !empty(trim((string)$request->input('jabatan', ''))) ? trim($request->input('jabatan')) : ($user->jabatan ?: 'Guru BK'),
            'nip' => !empty(trim((string)$request->input('nip', ''))) ? trim($request->input('nip')) : '-',
            'nuptk' => !empty(trim((string)$request->input('nuptk', ''))) ? trim($request->input('nuptk')) : '-',
            'wa' => !empty(trim((string)$request->input('wa', ''))) ? trim($request->input('wa')) : '-',
            'email' => !empty(trim((string)$request->input('email', ''))) && $request->input('email') !== '-' ? trim($request->input('email')) : $user->email,
            'rombelText' => !empty(trim((string)$request->input('rombelText', ''))) ? trim($request->input('rombelText')) : '-',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'wa' => 'nullable|string|max:30',
            'rombelText' => 'nullable|string',
        ]);

        $username = $validated['username'];
        $email = $validated['email'];

        $updateData = [
            'name' => $validated['name'],
            'username' => $username,
            'email' => $email,
            'nip' => $validated['nip'] ?? '-',
            'nuptk' => $validated['nuptk'] ?? '-',
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
        $majorCode = strtoupper(trim($validated['jurusan']));

        // Otomatis pastikan jurusan terdaftar jika pengguna mengetik jurusan baru
        Major::firstOrCreate(
            ['code' => $majorCode],
            ['name' => $majorCode, 'description' => 'Program Keahlian ' . $majorCode]
        );

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
                'major' => $majorCode,
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
        $majorCode = strtoupper(trim($validated['jurusan']));

        Major::firstOrCreate(
            ['code' => $majorCode],
            ['name' => $majorCode, 'description' => 'Program Keahlian ' . $majorCode]
        );

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

        // Sinkronisasi data siswa jika nama rombel kelas diperbarui
        if ($oldName !== $className) {
            Student::where('class_name', $oldName)->update([
                'class_name' => $className,
                'level' => $validated['level'],
                'major' => $validated['jurusan'],
            ]);
        }

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
        $request->validate([
            'nisn' => 'required|string|max:50|unique:students,nisn',
            'name' => 'required|string|max:255',
            'className' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'parentWa' => 'nullable|string|max:50',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
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

        $student = Student::create([
            'nisn' => trim($request->input('nisn')),
            'name' => trim($request->input('name')),
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'gender' => $request->input('gender'),
            'parent_phone' => $request->input('parentWa') ?: null,
            'parent_name' => 'Orang Tua ' . trim($request->input('name')),
            'total_violation_points' => 0,
            'status' => 'Aman',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'student' => $student]);
        }

        return redirect()->back()->with('success', "Data siswa {$student->name} berhasil ditambahkan!");
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nisn' => 'required|string|max:50|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:255',
            'className' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'parentWa' => 'nullable|string|max:50',
            'poin' => 'nullable|integer|min:0',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah digunakan oleh siswa lain.',
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

        $student->update([
            'nisn' => trim($request->input('nisn')),
            'name' => trim($request->input('name')),
            'class_name' => $className,
            'level' => $level,
            'major' => $major,
            'gender' => $request->input('gender'),
            'parent_phone' => $request->input('parentWa') ?: null,
            'total_violation_points' => $request->has('poin') ? (int)$request->input('poin') : $student->total_violation_points,
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

            $nisn = !empty($item['nisn']) ? trim((string)$item['nisn']) : 'NISN' . rand(10000000, 99999999);
            
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

            Student::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'name' => trim($item['name']),
                    'class_name' => $className,
                    'level' => $level,
                    'major' => $major,
                    'gender' => in_array(strtoupper($item['gender'] ?? 'L'), ['L', 'P']) ? strtoupper($item['gender']) : 'L',
                    'parent_phone' => $parentPhone,
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

        // Jika kode jurusan diubah, perbarui juga rombel kelas yang terikat
        if ($oldCode !== $major->code) {
            Classroom::where('major', $oldCode)->update(['major' => $major->code]);
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

        // Cek apakah masih digunakan oleh rombel kelas aktif
        $usedCount = Classroom::where('major', $code)->count();
        if ($usedCount > 0) {
            return redirect()->back()->with('error', "Jurusan {$code} tidak dapat dihapus karena masih digunakan oleh {$usedCount} rombel kelas.");
        }

        $major->delete();

        return redirect()->back()->with('success', 'Jurusan ' . $code . ' berhasil dihapus.');
    }
}
