<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RencanaHarianRequest extends FormRequest
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
        $id_shift = \Request::instance()->shift;
        $rules = [
            'id_shift'          => [
                'required'
            ],
            'alat_berat'        => 'required',
            'op_alat_berat'     => 'required',
            'admin_loket'       => 'required',
            'checker'           => 'required',
            'area'              => 'required|array',
            'area.*.*'          => 'required',
            'housekeeper'       => 'required|array',
            'housekeeper.*'     => 'required',
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        foreach (\Request::instance()->housekeeper as $key => $val) {
            $rules['area.' . $key . '.*'] = 'required';
        }

        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'id_shift.required'         => 'Shift Kerja wajib diisi!',
            'id_shift.exists'           => 'Shift Kerja tidak tersedia!',
            'alat_berat.required'       => 'Alat berat wajib diisi!',
            'op_alat_berat.required'    => 'Operator alat berat wajib diisi!',
            'admin_loket.required'      => 'Admin loket wajib diisi!',
            'checker.required'          => 'Checker wajib diisi!',
            'area.*.*.required'         => 'Area wajib diisi!',
            'housekeeper.*.required'    => 'Housekeeper wajib diisi!',
            'start_date.date_format'    => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'      => 'Tanggal harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function filters()
    {
        // return [
        //     'email' => 'trim|lowercase',
        //     'name' => 'trim|capitalize|escape'
        // ];
    }
}
