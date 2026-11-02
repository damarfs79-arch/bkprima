<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $role = $request->input('role', 'guru');
        $identifier = $request->input('nip') ?? $request->input('username') ?? $request->input('email') ?? 'demo';

        // Cari user berdasarkan username, nip, atau email
        $user = User::where(function ($query) use ($identifier) {
            $query->where('username', $identifier)
                  ->orWhere('nip', $identifier)
                  ->orWhere('email', $identifier);
        })->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));

            if ($user->role === 'admin' || str_contains(strtolower($user->username ?? ''), 'admin')) {
                return redirect('/admin')->with('success', 'Selamat datang Admin di Panel IT!');
            }

            return redirect('/absensi')->with('success', 'Selamat datang ' . $user->name . ' di Portal BK Prima!');
        }

        // Check if user is admin demo
        if ($role === 'admin' || str_contains(strtolower($identifier), 'admin')) {
            return redirect('/admin')->with('success', 'Selamat datang Admin di Panel IT!');
        }

        return redirect('/absensi')->with('success', 'Selamat datang di Sistem Presensi BK Prima!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
