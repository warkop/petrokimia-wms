<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiAktivitasRequest extends FormRequest
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
            'id_aktivitas'      => 'required|numeric',
            'id_gudang'         => 'required|numeric',
            'id_karu'           => 'required|numeric',
            'id_shift'          => 'required|numeric',
            // 'ref_number'        => '',
            // 'id_area'           => 'Area',
            // 'id_alat_berat'     => 'Alat Berat',
            // 'ttd'               => 'Tanda Tangan',
            // 'sistro'            => 'Sistro',
            // 'approve'           => 'Approve',
            // 'kelayakan_before'  => 'Kelayakan Before',
            // 'kelayakan_after'   => 'Kelayakan After',
            // 'dikembalikan'      => 'Dikembalikan',
        ];
        
        $this->sanitize();
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'id_aktivitas'      => 'Aktivitas',
            'id_gudang'         => 'Gudang',
            'id_karu'           => 'Karu',
            'id_shift'          => 'Shift',
            'ref_number'        => 'Nomor Referensi',
            'id_area'           => 'Area',
            'id_alat_berat'     => 'Alat Berat',
            'ttd'               => 'Tanda Tangan',
            'sistro'            => 'Sistro',
            'approve'           => 'Approve',
            'kelayakan_before'  => 'Kelayakan Before',
            'kelayakan_after'   => 'Kelayakan After',
            'dikembalikan'      => 'Dikembalikan',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'numeric'   => ':attribute harus berupa angka!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if ($input[$key] == 'file')
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
