<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Violation;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'totalStudents' => Student::count(),
            'totalViolations' => Violation::count(),
            'activeUsers' => User::count(),
            'systemHealth' => '99.9%',
        ];

        return Inertia::render('AdminDashboard', [
            'stats' => $stats,
        ]);
    }
}
