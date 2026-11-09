<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'name',
        'class_name',
        'level',
        'major',
        'gender',
        'phone',
        'parent_name',
        'parent_phone',
        'total_violation_points',
        'status',
    ];

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_name', 'name');
    }

    public function pointDeductions()
    {
        return $this->hasMany(PointDeduction::class);
    }

    public function recalculatePoints()
    {
        $totalViolations = $this->violations()->sum('points');
        $totalDeductions = $this->pointDeductions()->sum('points_deducted');
        $net = max(0, $totalViolations - $totalDeductions);

        $status = 'Aman';
        if ($net >= 76) {
            $status = 'Kritis (SP-3)';
        } elseif ($net >= 51) {
            $status = 'Panggilan Ortu (SP-2)';
        } elseif ($net >= 26) {
            $status = 'Peringatan (SP-1)';
        }

        $this->update([
            'total_violation_points' => $net,
            'status' => $status,
        ]);

        return $net;
    }
}
