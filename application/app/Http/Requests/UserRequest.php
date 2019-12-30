<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'username'      => [
                'required',
                Rule::unique('users', 'username')->ignore(\Request::instance()->id),
            ],
            'email'         => [
                'email',
                Rule::unique('users', 'email')->ignore(\Request::instance()->id),
            ],
            'role_id'       => [
                'required',
            ],
            'pilih'         => [
                'required_if:role_id,2,3,4,5',
            ],
            'start_date'    => 'nullable|date_format:d-m-Y',
            'end_date'      => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $this->sanitize();

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi!',
            'unique' => ':attribute tidak boleh sama dengan data yang lain!',
            'date_format' => ':attribute harus dengan format tanggal-bulan-tahun!',
            'after' => ':attribute harus lebih besar dari Start Date!',
        ];
    }

    public function attributes()
    {
        return [
            'id'            => 'ID',
            'username'      => 'Username',
            'email'         => 'Email',
            'start_date'    => 'Start Date',
            'end_date'      => 'End Date',
            'pilih'         => 'Pegawai',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        if ($input['end_date'] != '') {
            $input['end_date']   = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $input['end_date'] = null;
        }

        $this->replace($input);
    }
}
