<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'jurusan_id');
    }

    public function kelas()
    {
        return $this->classrooms();
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'major', 'code');
    }

    public function siswa()
    {
        return $this->students();
    }
}

