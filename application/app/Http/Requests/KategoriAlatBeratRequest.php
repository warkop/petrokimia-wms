<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriAlatBeratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'nama'    => [
                'required',
                Rule::unique('alat_berat_kat', 'nama')->ignore(request()->id, 'id')
            ],
            'start_date'                  => 'nullable|date_format:d-m-Y',
            'end_date'                    => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama Kategori Alat Berat harus diisi!',
            'nama.unique' => 'Nama Kategori Alat sudah ada!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        
        foreach ($input as $key => $value) {
            $input[$key] = strip_tags($value);
        }

        $this->replace($input);
    }
}
