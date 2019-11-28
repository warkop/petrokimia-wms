<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RealisasiMaterialRequest extends FormRequest
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
        $rules = [
            'id_material.*' => 'required',
            'material_tambah.*' => 'numeric',
            'material_kurang.*' => 'numeric',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_material'       => 'Material',
            'material_tambah'   => 'Kolom Material Bertambah',
            'material_kurang'   => 'Kolom Material Berkurang',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute harus berupa angka!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        if ($input['tanggal'] != '') {
            $input['tanggal']   = date('Y-m-d', strtotime($input['tanggal']));
        } else {
            $input['tanggal'] = null;
        }

        $this->replace($input);
    }
}
