<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak sesuai.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}
