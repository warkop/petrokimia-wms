<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiSaveUploadBaRequest extends FormRequest
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
        return [
            'berkas'                    => 'nullable|image',
        ];
    }

    public function attributes()
    {
        return [
            'berkas'                    => 'Berkas',
        ];
    }

    public function messages()
    {
        return [
            'image'     => ':attribute harus berupa gambar!',
        ];
    }
}
