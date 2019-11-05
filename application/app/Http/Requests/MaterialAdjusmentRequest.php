<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialAdjusmentRequest extends FormRequest
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

    public function rules()
    {
        

        $action = \Request::instance()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            // 'produk'            => 'required|array',
            'produk.*'          => 'required',
            // 'pallet'            => 'required|array',
            'pallet.*'          => 'required',
            // 'action_produk'     => 'array',
            'action_produk.*'   => 'numeric|between:1,2',
            // 'action_pallet'     => 'array',
            'action_pallet.*'   => 'numeric|between:1,2',
            // 'produk_jumlah'     => 'array',
            'produk_jumlah.*'   => 'numeric',
            // 'pallet_jumlah'     => 'array',
            'pallet_jumlah.*'   => 'numeric',
            'tanggal'           => 'required|date_format:d-m-Y',
        ];

        $this->sanitize();

        return $rules;
    }

    public function messages()
    {
        return [
            'required'      => ':attribute harus diisi!',
            'numeric'       => ':attribute harus berupa angka!',
            'between'       => ':attribute yang dimasukkan tidak valid!',
            'date_format'   => ':attribute harus dengan format tanggal-bulan-tahun!',
        ];
    }

    public function attributes()
    {
        return [
            'produk.*'              => 'Produk',
            'pallet.*'              => 'Pallet',
            'action_produk.*'       => 'Jenis aksi produk',
            'action_pallet.*'       => 'Jenis aksi pallet',
            'produk_jumlah.*'       => 'Jumlah Produk',
            'pallet_jumlah.*'       => 'Jumlah Pallet',
            'tanggal'             => 'Tanggal',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                foreach ($input[$key] as $row => $nilai) {
                    $input[$key][$row] = filter_var($nilai, FILTER_SANITIZE_STRING);
                }
            } else {
                $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }

        $this->replace($input);
    }
}
