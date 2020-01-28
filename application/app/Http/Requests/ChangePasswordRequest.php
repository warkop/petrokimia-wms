<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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

        return [
            'id_user'                   => 'required',
            'old_password'              => 'required',
            'new_password'              => 'required|different:old_password|same:new_password_confirmation',
            'new_password_confirmation '=> 'same:new_password|different:old_password',
        ];
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi!',
            'different'     => ':attribute harus berbeda dengan :other!',
            'same'          => ':attribute harus sama dengan :other!',
        ];
    }

    public function attributes()
    {
        return [
            'id_user'                       => 'ID',
            'old_password'                  => 'Password Lama',
            'new_password'                  => 'Password Baru',
            'new_password_confirmation'     => 'Konfirmasi Password Baru',
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
