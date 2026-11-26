<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting by key with fallback default.
     */
    public static function get(string $key, $default = null): ?string
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting !== null ? (string)$setting->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set/update a setting by key.
     */
    public static function set(string $key, ?string $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all app settings as key-value array.
     */
    public static function getAll(): array
    {
        $defaults = [
            'kepala_sekolah' => 'Saiful Anwar, S.Pd.',
            'nip_kepala_sekolah' => '19780707 200902 1 002',
            'semester' => 'Semester Genap 24/25',
            'tahun_ajaran' => '2024/2025',
            'school_name' => 'SMK PGRI 05 JEMBER',
            'school_address' => 'Jl. Krakatau No. 60, Kencong, Kab. Jember - Jawa Timur',
        ];

        try {
            $dbSettings = static::pluck('value', 'key')->toArray();
            return array_merge($defaults, $dbSettings);
        } catch (\Throwable $e) {
            return $defaults;
        }
    }
}
