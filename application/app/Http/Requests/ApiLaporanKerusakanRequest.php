<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiLaporanKerusakanRequest extends FormRequest
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
        $rules = [
            'id_kerusakan'      => 'required|numeric',
            'id_alat_berat'     => 'required|numeric',
            'id_shift'          => 'required|numeric',
            'jenis'             => 'between:1,2',
            'jam_rusak'         => 'date_format:d-m-Y H:i:s',
            'foto.*'            => 'image',
        ];

        $this->sanitize();
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'id_kerusakan'      => 'Kerusakan',
            'id_alat_berat'     => 'Alat Berat',
            'id_shift'          => 'Shift',
            'keterangan'        => 'Keterangan',
            'jenis'             => 'Jenis',
            'jam_rusak'         => 'Jam Rusak',
            'foto.*'            => 'Foto',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute harus berupa angka!',
            'image '    => ':attribute harus berupa gambar!',
            'between '  => ':attribute harus tidak valid!',
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
