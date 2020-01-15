<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisFotoRequest extends FormRequest
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
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'nama'            => 'Nama',
            'start_date'      => 'Start Date',
            'end_date'        => 'End Date',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi!',
            'start_date.date_format'  => 'Tanggal :attribute harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal :attribute harus dengan format tanggal-bulan-tahun',
            'after'  => 'Tanggal :attribute tidak boleh melebihi Start Date!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = strip_tags($value);
        }

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
