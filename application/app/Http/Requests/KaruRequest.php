<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KaruRequest extends FormRequest
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

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'nama'              => 'required',
            'no_hp'             => 'nullable|numeric',
            'nik'               => [
                'nullable',
                Rule::unique('karu', 'nik')->ignore(request()->id)
            ],
            'start_date'        => 'nullable',
            'end_date'          => 'nullable|after:start_date',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama Pegawai wajib diisi!',
            'nik.unique' => 'NIK tidak boleh sama dengan data yang lain!',
            'no_hp.numeric' => 'Nomor HP harus berupa angka!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['nama'] = strip_tags($input['nama']);
        $input['nik'] = filter_var($input['nik'], FILTER_SANITIZE_STRING);
        $input['no_hp'] = filter_var($input['no_hp'], FILTER_SANITIZE_STRING);
        $input['nik'] = filter_var($input['nik'], FILTER_SANITIZE_STRING);
        $input['start_date'] = filter_var(
            $input['start_date'],
            FILTER_SANITIZE_STRING
        );
        $input['end_date'] = filter_var(
            $input['end_date'],
            FILTER_SANITIZE_STRING
        );

        if ($input['start_date'] != '') {
            $input['start_date']  = date('Y-m-d', strtotime($input['start_date']));
        } else {
            $input['start_date'] = null;
        }

        if ($input['end_date'] != '') {
            $input['end_date']   = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $input['end_date'] = null;
        }

        $this->replace($input);
    }
}
