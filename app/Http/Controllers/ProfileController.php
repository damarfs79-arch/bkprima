<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? [
            'name' => 'Dra. Hj. Nurul Hidayati, M.Pd',
            'nip' => '19740512 200212 2 003',
            'email' => 'nurul.hidayati@bkprima.sch.id',
            'phone' => '0812-3456-7890',
            'role' => 'Guru Bimbingan Konseling (Koordinator)',
            'school' => 'SMK PGRI 05 JEMBER',
            'classesHandled' => 'Kelas X RPL 1-3 & XI RPL 1-2',
        ];

        return Inertia::render('Profile', [
            'profileUser' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        if (Auth::check()) {
            Auth::user()->update($validated);
        }

        return back()->with('success', 'Profil Guru BK berhasil diperbarui!');
    }
}
