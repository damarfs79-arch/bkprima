<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentSummon extends Model
{
    protected $table = 'parent_summons';

    protected $fillable = [
        'student_id',
        'student_name',
        'nis',
        'class_name',
        'letter_number',
        'stage',
        'case_title',
        'case_description',
        'points',
        'meeting_date',
        'meeting_time',
        'room',
        'parent_name',
        'parent_phone',
        'status',
        'counselor',
        'mediation_notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
