<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_snapshot',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($attendance) {
            if (empty($attendance->class_snapshot) && $attendance->student_id) {
                $student = Student::find($attendance->student_id);
                if ($student) {
                    $attendance->class_snapshot = $student->class_name;
                }
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function siswa()
    {
        return $this->student();
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_snapshot', 'name');
    }

    public function kelas()
    {
        return $this->classroom();
    }
}
