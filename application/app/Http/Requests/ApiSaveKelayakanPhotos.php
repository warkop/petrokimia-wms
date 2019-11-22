<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiSaveKelayakanPhotos extends FormRequest
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
