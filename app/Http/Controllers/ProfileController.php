<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $rombel = [];
        if ($user && $user->rombel) {
            $decoded = json_decode($user->rombel, true);
            if (is_array($decoded)) {
                $rombel = $decoded;
            } else {
                $rombel = array_values(array_filter(array_map('trim', explode(',', $user->rombel))));
            }
        }

        return Inertia::render('Profile', [
            'profileUser' => $user ? [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'avatar'     => $user->avatar,
                'avatar_url' => $user->avatar_url,
                'jabatan'    => $user->jabatan,
                'rombel'     => $rombel,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . ($user ? $user->id : 'NULL'),
            'phone'   => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'rombel'  => 'nullable|array',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($user) {
            $user->name    = $validated['name'];
            $user->email   = $validated['email'];
            $user->phone   = $validated['phone'] ?? $user->phone;
            $user->jabatan = $validated['jabatan'] ?? $user->jabatan;
            $user->rombel  = isset($validated['rombel'])
                ? json_encode($validated['rombel'])
                : $user->rombel;

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save();
        }

        return back()->with('success', 'Profil Guru BK berhasil diperbarui!');
    }

    public function requestOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi login tidak valid.'], 401);
        }

        if (empty($user->phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp belum terdaftar di profil akun Anda. Silakan isi dan simpan nomor WhatsApp terlebih dahulu.'
            ], 422);
        }

        $cacheKey = "otp_change_pw_{$user->id}";
        $rateKey = "otp_rate_limit_{$user->id}";

        // Rate limiting: jeda minimal 60 detik antar permintaan
        if (Cache::has($rateKey)) {
            $remaining = Cache::get($rateKey) - now()->timestamp;
            if ($remaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Mohon tunggu {$remaining} detik sebelum meminta kode OTP kembali."
                ], 429);
            }
        }

        // Generate 6 digit angka acak
        $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan ke Cache selama 5 menit
        Cache::put($cacheKey, [
            'code' => $otp,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5)->timestamp,
        ], now()->addMinutes(5));

        // Set cooldown throttle 60 detik
        Cache::put($rateKey, now()->addSeconds(60)->timestamp, 60);

        // Format nomor telepon
        $rawPhone = preg_replace('/[^0-9]/', '', $user->phone);
        if (str_starts_with($rawPhone, '0')) {
            $rawPhone = '62' . substr($rawPhone, 1);
        }

        $phoneLen = strlen($user->phone);
        $maskedPhone = $phoneLen > 7
            ? substr($user->phone, 0, 4) . '****' . substr($user->phone, -3)
            : $user->phone;

        $messageText = "[BK PRIMA - SMK PGRI 05 JEMBER]\nKode Keamanan (OTP) pembaruan kata sandi akun Guru BK Anda: *{$otp}*.\n\nKode berlaku selama 5 menit. Jangan berikan kode ini kepada pihak mana pun demi keamanan akun.";

        // Dispatch jika gateway WA terkonfigurasi di env
        $gatewayUrl = env('WA_GATEWAY_URL');
        $gatewayToken = env('WA_GATEWAY_TOKEN');
        if (!empty($gatewayUrl) && !empty($gatewayToken)) {
            try {
                Http::timeout(5)->withHeaders([
                    'Authorization' => $gatewayToken
                ])->post($gatewayUrl, [
                    'target' => $rawPhone,
                    'message' => $messageText,
                ]);
            } catch (\Exception $e) {
                Log::warning("Gagal mengirim OTP via Gateway: " . $e->getMessage());
            }
        }

        Log::info("OTP Keamanan Guru BK [{$user->name}]: {$otp} dikirim ke {$rawPhone}");

        $directWaUrl = "https://wa.me/{$rawPhone}?text=" . rawurlencode($messageText);

        return response()->json([
            'success' => true,
            'message' => "Kode OTP 6 digit berhasil dibuat untuk nomor WhatsApp {$maskedPhone}.",
            'maskedPhone' => $maskedPhone,
            'expiresIn' => 300,
            'cooldown' => 60,
            'directWaUrl' => $directWaUrl,
            'otpPreview' => app()->environment('local', 'testing') ? $otp : null,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
            'otp'              => 'required|string|size:6',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak sesuai.',
            'otp.required'              => 'Kode verifikasi OTP WhatsApp wajib diisi.',
            'otp.size'                  => 'Kode OTP harus tepat 6 digit angka.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $cacheKey = "otp_change_pw_{$user->id}";
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            throw ValidationException::withMessages([
                'otp' => ['Kode OTP belum diminta atau sudah kedaluwarsa. Silakan klik Minta Kode OTP terlebih dahulu.'],
            ]);
        }

        if ($cached['attempts'] >= 5) {
            Cache::forget($cacheKey);
            throw ValidationException::withMessages([
                'otp' => ['Terlalu banyak percobaan salah. Silakan minta kode OTP baru demi keamanan.'],
            ]);
        }

        if ($cached['code'] !== trim($request->otp)) {
            $cached['attempts']++;
            Cache::put($cacheKey, $cached, now()->addSeconds(max(10, $cached['expires_at'] - now()->timestamp)));
            $remaining = 5 - $cached['attempts'];
            throw ValidationException::withMessages([
                'otp' => ["Kode OTP tidak sesuai. Sisa kesempatan: {$remaining} kali."],
            ]);
        }

        // Hapus OTP dari cache setelah berhasil diverifikasi (one-time use)
        Cache::forget($cacheKey);

        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Kata sandi berhasil diperbarui!']);
        }

        return back()->with('success', 'Kata sandi akun Guru BK berhasil diperbarui dengan verifikasi OTP!');
    }
}
