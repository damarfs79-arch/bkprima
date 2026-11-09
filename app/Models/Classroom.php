<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'major',
        'wali_kelas',
        'guru_bk_id',
        'guru_bk_name',
    ];

    public function guruBk()
    {
        return $this->belongsTo(User::class, 'guru_bk_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_name', 'name');
    }
}

