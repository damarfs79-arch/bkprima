<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'code',
        'name',
        'points',
        'sanction',
        'description',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function deductions()
    {
        return $this->hasMany(PointDeduction::class);
    }

    public function pointDeductions()
    {
        return $this->deductions();
    }

    public function pemutihan()
    {
        return $this->deductions();
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, PointDeduction::class, 'rule_id', 'id', 'id', 'student_id');
    }

    public function siswa()
    {
        return $this->students();
    }
}

