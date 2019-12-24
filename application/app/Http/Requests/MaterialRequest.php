<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
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
            'id_material_sap'   => [
                'required_if:kategori,<>,1',
                Rule::unique('material', 'id_material_sap')->ignore(\Request::instance()->id)
            ],
            'nama'              => 'required',
            'kategori'          => 'integer|between:1,3',
            'start_date'        => 'nullable',
            'end_date'          => 'nullable|after:start_date',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'id_material_sap.required_if' => 'ID Material SAP harus diisi!',
            'id_material_sap.integer' => 'ID Material SAP harus berupa angka!',
            'id_material_sap.unique' => 'ID Material SAP sudah ada pada data lain!',
            'nama.unique' => 'Nama Material sudah ada!',
            'nama.required' => 'Nama Material wajib diisi!',
            'kategori.integer' => 'Kategori yang dimasukkan tidak valid!',
            'kategori.between' => 'Kategori yang dimasukkan tidak valid!',
            'end_date.after'  => 'Tanggal harus lebih kecil dari start date!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['nama'] = filter_var($input['nama'], FILTER_SANITIZE_STRING);
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
