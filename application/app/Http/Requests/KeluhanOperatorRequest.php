<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeluhanOperatorRequest extends FormRequest
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
            'keterangan'    => 'required',
            'id_operator'   => [
                'required',
                'numeric',
                Rule::exists('tenaga_kerja_non_organik', 'id')->where(function ($query) {
                    $query->where('job_desk_id', 2);
                }),
            ],
            'id_keluhan'    => [
                'required',
                'numeric',
                'exists:keluhan,id'
            ]
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'keterangan'        => 'Keterangan',
            'id_operator'       => 'Operator',
            'id_keluhan'        => 'Keluhan',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute harus diisi!',
            'numeric'  => ':attribute tidak valid!',
            'exists'    => ':attribute yang Anda pilih tidak tersedia!',
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
