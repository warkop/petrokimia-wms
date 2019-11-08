<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KerusakanRequest extends FormRequest
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
            'nama'              => 'required',
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];


        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'nama'              => 'Nama',
            'end_date'          => 'End Date',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama Kategori Alat Berat harus diisi!',
            'nama.unique' => 'Nama Kategori Alat sudah ada!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        if ($input['end_date'] != '') {
            $input['end_date']   = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $input['end_date'] = null;
        }

        $this->replace($input);
    }
}
