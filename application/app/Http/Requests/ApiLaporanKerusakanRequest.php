<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'id_alat_berat'     => 'required|numeric|exists:alat_berat,id',
            'id_operator'     => [
                'required',
                'numeric',
                Rule::exists('tenaga_kerja_non_organik', 'id')->where(function ($query) {
                    $query->where('job_desk_id', 2);
                }),
            ],
            'jam_rusak'         => 'date_format:d-m-Y H:i:s',
            'foto.*'            => 'nullable|image',
        ];

        $this->sanitize();
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'id_kerusakan'      => 'Kerusakan',
            'id_operator'       => 'Operator',
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
            'image'     => ':attribute harus berupa gambar!',
            'between'   => ':attribute harus tidak valid!',
            'exists'    => ':attribute yang Anda pilih tidak tersedia!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if ($input[$key] == 'file')
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        if ($input['jam_rusak'] != '') {
            $input['jam_rusak']   = date('Y-m-d H:i:s', strtotime($input['jam_rusak']));
        } else {
            $input['jam_rusak'] = null;
        }


        $this->replace($input);
    }
}
