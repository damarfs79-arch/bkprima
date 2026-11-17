<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'foto_bukti',
        'photo_url',
    ];

    protected $casts = [
        'points' => 'integer',
        'date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'foto_url',
        'poin',
    ];

    protected static function booted()
    {
        // Hapus file fisik bukti foto di storage saat data pelanggaran dihapus
        static::deleting(function ($violation) {
            $violation->deleteAttachedPhoto();
        });
    }

    public function deleteAttachedPhoto(): void
    {
        if (!empty($this->foto_bukti) && Storage::disk('public')->exists($this->foto_bukti)) {
            Storage::disk('public')->delete($this->foto_bukti);
        }

        if (!empty($this->photo_url) && !str_starts_with($this->photo_url, 'data:') && !str_starts_with($this->photo_url, 'http')) {
            $cleaned = ltrim(str_replace('/storage/', '', $this->photo_url), '/');
            if (Storage::disk('public')->exists($cleaned)) {
                Storage::disk('public')->delete($cleaned);
            }
        }
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!empty($this->foto_bukti)) {
            return Storage::disk('public')->url($this->foto_bukti);
        }

        if (!empty($this->photo_url)) {
            if (str_starts_with($this->photo_url, 'data:') || str_starts_with($this->photo_url, 'http')) {
                return $this->photo_url;
            }
            $cleaned = ltrim(str_replace('/storage/', '', $this->photo_url), '/');
            return Storage::disk('public')->url($cleaned);
        }

        return null;
    }

    public function getPoinAttribute(): ?int
    {
        return $this->points;
    }

    public function setPoinAttribute($value): void
    {
        $this->attributes['points'] = $value !== '' && $value !== null ? (int)$value : null;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function siswa()
    {
        return $this->student();
    }
}
