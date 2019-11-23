<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRencanaKerjaRequest extends FormRequest
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
        
        $rules = [
            'id_shift'                      => 'required|between:1,3',
            'alat_berat.*.id_alat_berat'    => [
                'numeric',
            ],
            'admin_loket.*.id_tkbm'         => [
                'numeric',
            ],
            'operator.*.id_tkbm'            => [
                'numeric',
            ],
            'checker.*.id_tkbm'             => [
                'numeric',
            ],
            'housekeeper.*.id_tkbm'         => [
                'numeric',
            ],
            'housekeeper.*.area.*.id_area'  => [
                'numeric',
            ],
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_shift'                      => 'Shift',
            'alat_berat.*.id_alat_berat'    => 'Alat Berat',
            'admin_loket.*.id_tkbm'         => 'Admin Loket',
            'operator.*.id_tkbm'            => 'Operator',
            'checker.*.id_tkbm'             => 'Checker',
            'housekeeper.*.id_tkbm'         => 'Housekeeper',
            'housekeeper.*.area.*.id_area'  => 'Area',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute tidak valid!',
            'image'    => ':attribute harus berupa gambar!',
            'between'  => ':attribute tidak valid!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                foreach ($input[$key] as $row1 => $data1) {
                    if (is_array($input[$key][$row1])) {
                        foreach ($input[$key] as $row2 => $data2) {
                            $input[$key][$row1][$row2] = filter_var($data2, FILTER_SANITIZE_STRING);
                        }
                    } else {
                        $input[$key][$row1] = filter_var($data1, FILTER_SANITIZE_STRING);
                    }
                }
            } else {
                $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }

        $this->replace($input);
    }
}
