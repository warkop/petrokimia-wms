<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GudangRequest extends FormRequest
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
            'id_sloc'           => Rule::unique('gudang')->ignore(request()->id),
            'nama'              => 'required',
            'tipe_gudang'       => 'required|numeric|digits_between:1,2',
            // 'id_karu'           => [Rule::unique('gudang')->ignore(request()->id),],
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_sloc'       => 'ID Sloc',
            'nama'          => 'Nama',
            'tipe_gudang'   => 'Tipe Gudang',
            'id_karu'       => 'Karu',
            'end_date'      => 'End Date',
        ];
    }

    public function messages()
    {
        return [
            'required'          => ':attribute wajib diisi!',
            'unique'            => ':attribute sudah terdaftar pada gudang lain',
            'digits_between'    => ':attribute tidak valid!',
            'date_format'       => 'Tanggal :attribute harus dengan format tanggal-bulan-tahun',
            'after'             => 'Tanggal :attribute tidak boleh melebihi Start Date!',
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
