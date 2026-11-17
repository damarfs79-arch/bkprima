<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'sometimes|required|string|max:255',
            'student_class' => 'nullable|string|max:100',
            'category' => 'sometimes|required|string|max:100',
            'violation_name' => 'nullable|string|max:255',
            'points' => 'nullable|integer|min:0',
            'poin' => 'nullable|integer|min:0',
            'date' => 'nullable|date',
            'time' => 'nullable|string|max:50',
            'reporter' => 'nullable|string|max:255',
            'penalty_action' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'foto_bukti' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'photo_url' => 'nullable|string',
            'hapus_foto' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'student_name.required' => 'Nama siswa terlapor wajib diisi.',
            'category.required' => 'Kategori pelanggaran wajib dipilih.',
            'points.integer' => 'Poin pelanggaran harus berupa angka bulat.',
            'points.min' => 'Poin pelanggaran minimal adalah 0.',
            'poin.integer' => 'Poin pelanggaran harus berupa angka bulat.',
            'poin.min' => 'Poin pelanggaran minimal adalah 0.',
            'foto_bukti.image' => 'Berkas bukti foto harus berupa berkas gambar.',
            'foto_bukti.mimes' => 'Format foto bukti harus berupa jpg, jpeg, png, atau webp.',
            'foto_bukti.max' => 'Ukuran berkas foto bukti maksimal 2 MB (2048 KB).',
            'foto.image' => 'Berkas bukti foto harus berupa berkas gambar.',
            'foto.mimes' => 'Format foto bukti harus berupa jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran berkas foto bukti maksimal 2 MB (2048 KB).',
        ];
    }
}
