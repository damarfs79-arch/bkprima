<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $nisn = $request->query('nisn');
        $studentData = null;

        if ($nisn) {
            $studentData = Student::with(['violations', 'attendances'])->where('nisn', $nisn)->first();
        }

        return Inertia::render('GuestPortal', [
            'searchedStudent' => $studentData,
            'nisnQuery' => $nisn,
        ]);
    }
}
