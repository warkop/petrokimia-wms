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
        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'tanggal'           => 'nullable|date_format:d-m-Y',
            'material'          => 'required',
            'jumlah'            => 'numeric',
            'tipe'              => 'between:1,2',
            'jenis'             => 'between:1,4',
        ];

        $this->sanitize();

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
            'numeric'        => ':attribute harus berupa angka!',
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

        if ($input['tanggal'] != '') {
            $input['tanggal']  = date('Y-m-d', strtotime($input['tanggal']));
        } else {
            $input['tanggal'] = null;
        }

        $this->replace($input);
    }
}
