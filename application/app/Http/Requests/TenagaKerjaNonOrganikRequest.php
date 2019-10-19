<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenagaKerjaNonOrganikRequest extends FormRequest
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

        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'nama'              => ['required'],
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];


        return $rules;
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama Pegawai harus diisi!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['nama'] = filter_var($input['nama'], FILTER_SANITIZE_STRING);
        $input['start_date'] = filter_var($input['start_date'], 
        FILTER_SANITIZE_STRING);
        $input['end_date'] = filter_var($input['end_date'], 
        FILTER_SANITIZE_STRING);

        if ($input['start_date'] != '') {
            $input['start_date']  = date('Y-m-d', strtotime($input['start_date']));
        } else {
            $input['start_date'] = null;
        }

        if ($input['end_date'] != '') {
            $end_date   = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $input['end_date'] = null;
        }

        $this->replace($input);
    }
}
