<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'rule_id',
        'title',
        'type',
        'points_deducted',
        'date',
        'officer',
        'notes',
        'evidence_photo_url',
    ];

    protected $casts = [
        'points_deducted' => 'integer',
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}

