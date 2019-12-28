<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PemetaanSlocRequest extends FormRequest
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
        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'id_sloc.*'          => [
                'required',
            ],
            'nama'            => [
                'required',
                Rule::unique('pemetaan_sloc', 'nama')->ignore(\Request::instance()->id)
            ],
        ];

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_sloc.*'         => 'ID Sloc',
            'nama'              => 'Nama',
        ];
    }

    public function message()
    {
        return [
            'required'       => ':attribute wajib diisi!',
            'integer'        => ':attribute harus berupa angka!',
            'between'        => ':attribute tidak valid!',
            'exists'         => ':attribute tidak tersedia!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                foreach ($input[$key] as $row => $nilai) {
                    $input[$key][$row] = filter_var($nilai, FILTER_SANITIZE_STRING);
                }
            } else {
                $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }

        $this->replace($input);
    }
}
