<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RealisasiMaterialRequest extends FormRequest
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
            'tanggal'                           => 'nullable|date_format:d-m-Y',
            'list_material.*.material'          => [
                'required',
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 3);
                }),
            ],
            'list_material.*.jumlah'            => 'integer',
            'list_material.*.tipe'              => 'between:1,2',
        ];

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'tanggal'         => 'Tanggal',
            'list_material.*.material'        => 'Material',
            'list_material.*.jumlah'          => 'Jumlah',
            'list_material.*.tipe'            => 'Tipe',
        ];
    }

    public function messages()
    {
        return [
            'required'       => ':attribute wajib diisi!',
            'integer'        => ':attribute harus berupa angka!',
            'between'        => ':attribute tidak valid!',
            'exists'         => ':attribute tidak tersedia!',
            'date_format'    => 'Tanggal :attribute harus dengan format tanggal-bulan-tahun',
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

        if ($input['tanggal'] != '') {
            $input['tanggal']  = date('Y-m-d', strtotime($input['tanggal']));
        } else {
            $input['tanggal'] = null;
        }

        $this->replace($input);
    }
}
