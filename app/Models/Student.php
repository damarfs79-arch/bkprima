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
}
