<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiSaveKelayakanPhotos extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        // Get all the errors thrown
        $errors = collect($validator->errors());
        // Manipulate however you want. I'm just getting the first one here,
        // but you can use whatever logic fits your needs.
        $error  = $errors->unique()->first();
        $mess = [];
        foreach ($errors->unique() as $key => $value) {
            foreach ($value as $row) {
                array_push($mess, $row);
            }
        }
        $responses['message'] = "The given data was invalid.";
        $responses['errors'] = $mess;
        $responses['code'] = 422;

        // Either throw the exception, or return it any other way.
        throw new HttpResponseException(response(
            $responses,
            422
        ));
    }
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
            'id_aktivitas_harian'       => 'required|numeric',
            'jenis.*'                   => 'required|numeric',
            'foto.*'                    => 'nullable|image',
        ];
    }

    public function attributes()
    {
        return [
            'id_aktivitas_harian'       => 'ID Aktivitas harian',
            'jenis.*'                   => 'Jenis Kelayakan',
            'foto.*'                    => 'Foto',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute harus berupa angka!',
            'image'     => ':attribute harus berupa gambar!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if ($input[$key] == 'file')
                $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
