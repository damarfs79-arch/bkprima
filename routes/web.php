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

// 4. Violations (Pelanggaran)
Route::get('/pelanggaran', [ViolationController::class, 'index'])->name('violation.index');
Route::get('/verifikasi', [ViolationController::class, 'create'])->name('violation.create');
Route::post('/pelanggaran', [ViolationController::class, 'store'])->name('violation.store');
Route::get('/pelanggaran/detail', [ViolationController::class, 'show'])->name('violation.detail');
Route::get('/pemanggilan', [ViolationController::class, 'summons'])->name('violation.summons');

// 5. Piket
Route::get('/piket', [PiketController::class, 'index'])->name('piket.index');

// 6. Tata Tertib & Panduan Poin
Route::get('/panduan', [RuleController::class, 'index'])->name('rules.index');

// 7. Guru BK Profile
Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profil', [ProfileController::class, 'update'])->name('profile.update');

// 8. Admin IT Panel
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/guru', [AdminController::class, 'storeGuru'])->name('admin.guru.store');
Route::put('/admin/guru/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
Route::delete('/admin/guru/{id}', [AdminController::class, 'deleteGuru'])->name('admin.guru.delete');
Route::patch('/admin/konsultasi/{id}', [AdminController::class, 'updateConsultationStatus'])->name('admin.consultation.update');
Route::delete('/admin/konsultasi/{id}', [AdminController::class, 'deleteConsultation'])->name('admin.consultation.delete');

// 9. Guest Portal (Siswa & Wali Murid)
Route::get('/tamu', [GuestController::class, 'index'])->name('guest.index');
Route::post('/tamu/konsultasi', [GuestController::class, 'storeConsultation'])->name('guest.consultation');
Route::get('/portal-tamu', function () {
    return redirect('/tamu');
});
