<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function defaultNames(): array
    {
        return [
            'Pembinaan Lisan & Pengarahan BK',
            'Piket Kebersihan Lingkungan Sekolah',
            'Teguran Tertulis & Surat Perjanjian',
            'Pemanggilan Orang Tua (SP-1)',
            'Pemanggilan Orang Tua (SP-2)',
            'Skorsing Sementara (SP-3)',
        ];
    }
}
