<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRealisasiRequest extends FormRequest
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
        $rules = [
            'id_rencana'         => 'required',
            'material.*'         => 'nullable|numeric',
            'material_kurang.*'  => 'nullable|numeric',
            'material_tambah.*'  => 'nullable|numeric',
            'housekeeper.*'         => 'nullable|numeric',
            'area_housekeeper.*.*'   => 'nullable|numeric',
        ];

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_rencana'        => 'ID Rencana',
            'material'          => 'Material',
            'material_kurang'   => 'Mengurangi Material',
            'material_tambah'   => 'Menambah Material',
            'housekeeper.*'       => 'Housekeeper',
            'area_housekeeper.*.*'  => 'Area Housekeeper',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute tidak valid!',
            'image'     => ':attribute harus berupa gambar!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
