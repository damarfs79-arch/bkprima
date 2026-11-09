<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_name',
        'student_class',
        'category',
        'violation_name',
        'points',
        'date',
        'time',
        'reporter',
        'penalty_action',
        'status',
        'notes',
        'photo_url',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
