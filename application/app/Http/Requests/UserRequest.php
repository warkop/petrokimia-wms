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
        return $this->user()->role_id == 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id'            => 'bail|required_if:action,edit',
            'username'      => [
                'required',
                Rule::unique('users', 'username')->ignore(request()->id),
            ],
            'email'         => [
                'email',
                Rule::unique('users', 'email')->ignore(request()->id),
            ],
            'role_id'       => 'required',
            'pilih'         => 'required_if:role_id,3,4,5',
            'end_date'      => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $this->sanitize();

        return $rules;
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi!',
            'unique'        => ':attribute tidak boleh sama dengan data yang lain!',
            'date_format'   => ':attribute harus dengan format tanggal-bulan-tahun!',
            'after'         => ':attribute harus lebih besar dari :after!',
            'email'         => ':attribute tidak sesuai dengan format email!',
            'id.required_if'=> ':attribute wajib ada apabila edit data!',
            'required_if'   => ':attribute wajib diisi apabila :other dalam posisi selain administrator!',
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
            'role_id'       => 'Hak Akses',
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
