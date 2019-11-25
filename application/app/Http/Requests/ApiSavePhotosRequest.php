<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiSavePhotosRequest extends FormRequest
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
            'id_aktivitas_harian'       => 'required|numeric|exists:aktivitas_harian,id',
            'ttd'                       => 'nullable|image',
            'foto.*'                    => 'nullable|image',
            'id_foto_jenis.*'           => 'nullable|numeric|exists:foto_jenis,id',
        ];
    }

    public function attributes()
    {
        return [
            'id_aktivitas_harian'      => 'ID Aktivitas harian',
            'ttd'               => 'Tanda Tangan',
            'foto.*'            => 'Foto',
            'id_foto_jenis.*'            => 'Jenis Foto',
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
