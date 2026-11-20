<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'jabatan', 'phone', 'avatar', 'rombel'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }
        return url('storage/' . ltrim($this->avatar, '/'));
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'guru_bk_id');
    }

    public function kelas()
    {
        return $this->classrooms();
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            Classroom::class,
            'guru_bk_id',
            'class_name',
            'id',
            'name'
        );
    }

    public function siswa()
    {
        return $this->students();
    }
}
