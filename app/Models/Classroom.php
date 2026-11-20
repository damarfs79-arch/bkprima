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
        'jurusan_id',
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
        return $this->hasMany(Student::class, 'classroom_id');
    }

    public function siswa()
    {
        return $this->students();
    }

    public function jurusan()
    {
        return $this->belongsTo(Major::class, 'jurusan_id');
    }

    public function majorRelation()
    {
        return $this->jurusan();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_snapshot', 'name');
    }

    public function absensi()
    {
        return $this->attendances();
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'class_snapshot', 'name');
    }

    public function pelanggaran()
    {
        return $this->violations();
    }
}

