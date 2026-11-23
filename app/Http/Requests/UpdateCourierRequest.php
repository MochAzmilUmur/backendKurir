<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dataKurir = $this->route('dataKurir');

        return [
            'nama' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('couriers', 'email')->ignore($dataKurir)],
            'telepon' => ['required', 'string', 'min:10', 'max:20', Rule::unique('couriers', 'telepon')->ignore($dataKurir)],
            'level' => ['required', 'integer', 'between:1,5'],
            'jenis_kendaraan' => ['nullable', 'string', 'max:50', Rule::in('motor', 'mobil', 'van', 'truk')],
            'plat_nomor' => ['nullable', 'string', 'max:15'],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }
}
