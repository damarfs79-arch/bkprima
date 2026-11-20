<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'rombel_id',
        'nis',
        'nisn',
        'name',
        'class_name',
        'rombel',
        'level',
        'major',
        'gender',
        'phone',
        'parent_name',
        'parent_phone',
        'total_violation_points',
        'status',
        'graduation_year',
        'last_class_name',
        'academic_year',
    ];

    protected $appends = [
        'nisn',
        'rombel_id',
    ];

    public function getRombelIdAttribute()
    {
        return $this->classroom_id;
    }

    public function setRombelIdAttribute($value)
    {
        $this->attributes['classroom_id'] = $value;
    }

    public function getNisnAttribute()
    {
        return $this->attributes['nis'] ?? ($this->attributes['nisn'] ?? null);
    }

    public function setNisnAttribute($value)
    {
        $this->attributes['nis'] = $value;
        if (array_key_exists('nisn', $this->attributes)) {
            $this->attributes['nisn'] = $value;
        }
    }

    public function setNisAttribute($value)
    {
        $this->attributes['nis'] = $value;
        if (array_key_exists('nisn', $this->attributes)) {
            $this->attributes['nisn'] = $value;
        }
    }

    /**
     * Scope for active enrolled students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope for graduated students (alumni).
     */
    public function scopeGraduated($query)
    {
        return $query->where('status', 'lulus');
    }

    public function getTingkatAttribute()
    {
        return $this->level;
    }

    public function getViolationStatusAttribute()
    {
        $net = (int) $this->total_violation_points;
        if ($net >= 76) return 'Kritis (SP-3)';
        if ($net >= 51) return 'Panggilan Ortu (SP-2)';
        if ($net >= 26) return 'Peringatan (SP-1)';
        return 'Aman';
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function pelanggaran()
    {
        return $this->violations();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function absensi()
    {
        return $this->attendances();
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id')->withDefault(function () {
            return Classroom::where('name', $this->class_name)->first();
        });
    }

    public function rombelData()
    {
        return $this->classroom();
    }

    public function kelas()
    {
        return $this->classroom();
    }

    public function jurusan()
    {
        return $this->belongsTo(Major::class, 'major', 'code');
    }

    public function majorRelation()
    {
        return $this->jurusan();
    }

    public function pointDeductions()
    {
        return $this->hasMany(PointDeduction::class);
    }

    public function deductions()
    {
        return $this->pointDeductions();
    }

    public function pemutihan()
    {
        return $this->pointDeductions();
    }

    public function consultations()
    {
        return $this->hasMany(ConsultationRequest::class, 'student_name', 'name');
    }

    public function summons()
    {
        return $this->hasMany(ParentSummon::class, 'student_id');
    }

    public function recalculatePoints()
    {
        $totalViolations = (int) $this->violations()->whereNotNull('points')->sum('points');
        $totalDeductions = (int) $this->pointDeductions()->sum('points_deducted');
        $net = max(0, $totalViolations - $totalDeductions);

        $this->update([
            'total_violation_points' => $net,
        ]);

        return $net;
    }
}
