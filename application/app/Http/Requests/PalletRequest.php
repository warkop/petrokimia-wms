<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PalletRequest extends FormRequest
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
            'tanggal'           => 'nullable|date_format:d-m-Y',
            'material'          => 'required',
            'jumlah'            => 'integer',
            'tipe'              => 'between:1,2',
            'jenis'             => 'between:1,4',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'tanggal'         => 'Tanggal',
            'material'        => 'Pallet',
            'jumlah'          => 'Jumlah',
            'tipe'            => 'Tipe',
            'jenis'           => 'Jenis',
        ];
    }

    public function messages()
    {
        return [
            'required'       => ':attribute wajib diisi!',
            'integer'        => ':attribute harus berupa angka!',
            'between'        => ':attribute tidak valid!',
            'date_format'    => 'Tanggal :attribute harus dengan format tanggal-bulan-tahun',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
