<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'nip'     => $user->nip,
                'nuptk'   => $user->nuptk,
                'jabatan' => $user->jabatan,
                'rombel'  => $rombel,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone'   => 'nullable|string|max:50',
            'nuptk'   => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'rombel'  => 'nullable|array',
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            $user->name    = $validated['name'];
            $user->email   = $validated['email'];
            $user->phone   = $validated['phone'] ?? $user->phone;
            $user->nuptk   = $validated['nuptk'] ?? $user->nuptk;
            $user->jabatan = $validated['jabatan'] ?? $user->jabatan;
            $user->rombel  = isset($validated['rombel'])
                ? json_encode($validated['rombel'])
                : $user->rombel;
            $user->save();
        }

        return back()->with('success', 'Profil Guru BK berhasil diperbarui!');
    }
}
