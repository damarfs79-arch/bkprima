<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Attendance;
use App\Models\Violation;

class PiketController extends Controller
{
    public function index()
    {
        $todayLateCount = Attendance::where('date', now()->toDateString())->where('status', 'Terlambat')->count();
        $todayViolationCount = Violation::where('date', now()->toDateString())->count();

        return Inertia::render('Piket', [
            'todayLateCount' => $todayLateCount,
            'todayViolationCount' => $todayViolationCount,
            'shift' => 'Pagi (06:30 - 13:00)',
        ]);
    }
}
