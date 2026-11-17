<?php

namespace App\Http\Requests;

use App\Models\Postingan;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePostinganRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pembuat postingan harus sudah punya profil
        return $this->user()?->profil !== null;
    }

    public function rules(): array
    {
        return [
            'nama_lomba' => ['required', 'string', 'max:100'],
            'waktu_perlombaan' => ['required', 'date'],
            'jumlah_anggota' => ['required', 'integer', 'between:1,10'],
            'tag_skill' => ['required', 'string'],
            'deskripsi' => ['required', 'string', 'max:500'],
            ...Postingan::aturanFoto($this),
        ];
    }

    // Nama field sesuai label di form UI01
    public function attributes(): array
    {
        return [
            'nama_lomba' => 'Nama Lomba',
            'waktu_perlombaan' => 'Tanggal',
            'jumlah_anggota' => 'Jumlah anggota',
            'tag_skill' => 'Skill Needed',
            'deskripsi' => 'Katakan sesuatu',
            'foto' => 'Foto',
        ];
    }

    public function messages(): array
    {
        return Postingan::pesanFoto($this);
    }

    // PCT-PC01: peringatan "Gagal: Field berikut belum diisi - ..."
    protected function failedValidation(Validator $validator): void
    {
        $fieldKosong = [];

        foreach ($validator->failed() as $field => $aturan) {
            if (array_key_exists('Required', $aturan)) {
                $fieldKosong[] = $this->attributes()[$field];
            }
        }

        if (!empty($fieldKosong)) {
            session()->flash(
                'pesan_peringatan',
                'Gagal: Field berikut belum diisi - ' . implode(', ', $fieldKosong)
            );
        }

        parent::failedValidation($validator);
    }
}
