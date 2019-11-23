<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
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
        
        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'nama'              => [
                'required',
                Rule::unique('area', 'nama')->ignore(\Request::instance()->id)
            ],
            'kapasitas'         => 'numeric|between:0,9999.9999',
            'tipe'              => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'nama'                  => 'Nama area',
            'kapasitas'             => 'Kapasitas',
            'tipe'                  => 'Tipe Area',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute harus berupa angka!',
            'between'   => ':attribute harus dimulai dari angka 0 sampai 9999.9999',
            'unique'    => ':attribute sudah ada!',
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