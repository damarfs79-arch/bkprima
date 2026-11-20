<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRequest extends Model
{
    protected $fillable = [
        'parent_name',
        'student_name',
        'topic',
        'phone',
        'status',
        'contacted_at',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_name', 'name');
    }

    public function siswa()
    {
        return $this->student();
    }
}
