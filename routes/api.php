<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Public Health & Authentication
Route::get('/health', [ApiController::class, 'health']);
Route::post('/login', [ApiController::class, 'login']);

// Protected or Public BK Prima API Endpoints
Route::get('/absensi', [ApiController::class, 'getAttendance']);
Route::post('/absensi/sync', [ApiController::class, 'syncAttendance']);
Route::post('/absensi/status', [ApiController::class, 'updateAttendanceStatus']);
Route::post('/absensi/bulk', [ApiController::class, 'bulkAttendance']);

Route::get('/pelanggaran', [ApiController::class, 'getViolations']);
Route::post('/pelanggaran', [ApiController::class, 'storeViolation']);

Route::get('/aturan', [ApiController::class, 'getRules']);
Route::post('/aturan', [ApiController::class, 'storeRule']);
Route::get('/sanksi', [ApiController::class, 'getSanctions']);

Route::get('/statistik', [ApiController::class, 'getStats']);
Route::get('/stats', [ApiController::class, 'getStats']);
Route::get('/statistik-kehadiran', [ApiController::class, 'getAttendanceStatsFromAdmin']);

Route::get('/profil', [ApiController::class, 'getProfile']);
Route::get('/profile', [ApiController::class, 'getProfile']);
Route::match(['post', 'put'], '/profil', [ApiController::class, 'updateProfile']);
Route::match(['post', 'put'], '/profile', [ApiController::class, 'updateProfile']);
Route::match(['post', 'put'], '/profil/password', [ApiController::class, 'changePassword']);
Route::match(['post', 'put'], '/profile/password', [ApiController::class, 'changePassword']);

Route::post('/logout', [ApiController::class, 'logout']);
