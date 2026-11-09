<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AuthController;

// 1. Splash Page
Route::get('/', function () {
    return Inertia::render('Splash');
})->name('splash');

// 2. Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Attendance (Absensi)
Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/absensi/bulanan', [AttendanceController::class, 'monthly'])->name('attendance.monthly');
Route::get('/statistik', [AttendanceController::class, 'stats'])->name('attendance.stats');
Route::post('/absensi/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');
Route::post('/absensi/status', [AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');
Route::post('/siswa', [AttendanceController::class, 'storeStudent'])->name('students.store');
Route::put('/siswa/{id}', [AttendanceController::class, 'updateStudent'])->name('students.update');
Route::delete('/siswa/{id}', [AttendanceController::class, 'deleteStudent'])->name('students.delete');

// 4. Violations (Pelanggaran)
Route::get('/pelanggaran', [ViolationController::class, 'index'])->name('violation.index');
Route::get('/verifikasi', [ViolationController::class, 'create'])->name('violation.create');
Route::post('/pelanggaran', [ViolationController::class, 'store'])->name('violation.store');
Route::get('/pelanggaran/detail', [ViolationController::class, 'show'])->name('violation.detail');
Route::get('/pemanggilan', [ViolationController::class, 'summons'])->name('violation.summons');

// 5. Piket
Route::get('/piket', [PiketController::class, 'index'])->name('piket.index');

// 6. Tata Tertib & Panduan Poin (Master Data & Pemutihan)
Route::get('/panduan', [RuleController::class, 'index'])->name('rules.index');
Route::post('/panduan/aturan', [RuleController::class, 'storeRule'])->name('rules.store');
Route::put('/panduan/aturan/{id}', [RuleController::class, 'updateRule'])->name('rules.update');
Route::delete('/panduan/aturan/{id}', [RuleController::class, 'deleteRule'])->name('rules.delete');
Route::post('/panduan/pemutihan', [RuleController::class, 'storeDeduction'])->name('rules.deduction.store');
Route::delete('/panduan/pemutihan/{id}', [RuleController::class, 'deleteDeduction'])->name('rules.deduction.delete');

// 7. Guru BK Profile
Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profil', [ProfileController::class, 'update'])->name('profile.update');

// 8. Admin IT Panel
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/guru', [AdminController::class, 'storeGuru'])->name('admin.guru.store');
Route::put('/admin/guru/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
Route::delete('/admin/guru/{id}', [AdminController::class, 'deleteGuru'])->name('admin.guru.delete');
Route::post('/admin/kelas', [AdminController::class, 'storeKelas'])->name('admin.kelas.store');
Route::put('/admin/kelas/{id}', [AdminController::class, 'updateKelas'])->name('admin.kelas.update');
Route::delete('/admin/kelas/{id}', [AdminController::class, 'deleteKelas'])->name('admin.kelas.delete');
Route::post('/admin/siswa/import', [AdminController::class, 'importStudents'])->name('admin.siswa.import');
Route::patch('/admin/konsultasi/{id}', [AdminController::class, 'updateConsultationStatus'])->name('admin.consultation.update');
Route::delete('/admin/konsultasi/{id}', [AdminController::class, 'deleteConsultation'])->name('admin.consultation.delete');

// 9. Guest Portal (Siswa & Wali Murid)
Route::get('/tamu', [GuestController::class, 'index'])->name('guest.index');
Route::post('/tamu/konsultasi', [GuestController::class, 'storeConsultation'])->name('guest.consultation');
Route::get('/portal-tamu', function () {
    return redirect('/tamu');
});
