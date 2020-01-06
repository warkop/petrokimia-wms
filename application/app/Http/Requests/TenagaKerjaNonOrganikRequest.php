<?php

namespace App\Http\Requests;

use App\Http\Models\TenagaKerjaNonOrganik;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        $action = request()->action;
        $model = new TenagaKerjaNonOrganik;
        if ($action == 'edit') {
            $rules['id'] = 'required';
            $model = TenagaKerjaNonOrganik::find(request()->id);
        }

        $rules = [
            'nama'              => 'required',
            'nik'               => [
                'nullable',
                Rule::unique('tenaga_kerja_non_organik')->ignore($model->id)
            ],
            'nomor_hp'          => 'nullable|numeric',
            'nomor_bpjs'        => 'nullable|numeric',
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
            'nomor_hp.numeric' => 'Nomor HP harus berupa angka!',
            'nomor_bpjs.numeric' => 'Nomor BPJS harus berupa angka!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.after'  => 'End Date harus lebih besar dari Start Date!',
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
