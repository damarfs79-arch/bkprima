<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        $identifier = $request->input('nip') ?? $request->input('email') ?? 'demo';

        // Check if user is admin
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
