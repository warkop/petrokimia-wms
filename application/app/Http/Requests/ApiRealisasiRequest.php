<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiRealisasiRequest extends FormRequest
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
            'id_rencana'            => 'required|exists:rencana_harian,id',
            'housekeeper.*'         => [
                'numeric',
                Rule::exists('tenaga_kerja_non_organik', 'id')->where(function ($query) {
                    $query->where('job_desk_id', 4);
                })
            ],
            'area_housekeeper.*.*'  => 'exists:area,id',
            'foto.*.*.*'            => 'image',
        ];

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_rencana'            => 'ID Rencana',
            'material'              => 'Material',
            'material_kurang'       => 'Mengurangi Material',
            'material_tambah'       => 'Menambah Material',
            'housekeeper.*'         => 'Housekeeper',
            'area_housekeeper.*.*'  => 'Area Housekeeper',
            'foto.*.*.*'            => 'Foto',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute tidak valid!',
            'exists'    => ':attribute tidak tersedia!',
            'image'     => ':attribute harus berupa gambar!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                foreach ($input[$key] as $row1 => $data1) {
                    if (is_array($input[$key][$row1])) {
                        foreach ($input[$key] as $row2 => $data2) {
                            if ($input[$key] == 'file')
                                $input[$key][$row1][$row2] = filter_var($data2, FILTER_SANITIZE_STRING);
                        }
                    } else {
                        $input[$key][$row1] = filter_var($data1, FILTER_SANITIZE_STRING);
                    }
                }
            } else {
                $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }

        $this->replace($input);
    }
}
