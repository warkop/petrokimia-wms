<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // $this->sanitize();
        $request = \Request::instance();
        $rules = [
            'id_aktivitas'      => 'required|exists:aktivitas,id',
            'id_gudang_tujuan'  => 'nullable|exists:gudang,id',
            'id_alat_berat'     => 'nullable|exists:alat_berat,id',
            'alasan'            => [
                'nullable',
            ],
            'list_produk.*.produk'       => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 1);
                })
            ],
            'list_produk.*.status_produk'    => 'between:1,2',
            'list_produk.*.list_area.*.tipe' => 'between:1,2',
            'list_produk.*.list_area.*.list_jumlah.*.tanggal' => 'date_format:d-m-Y',
            'list_produk.*.list_area.*.list_jumlah.*.jumlah'  => 'numeric',
            'list_pallet.*.pallet' => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 2);
                })
            ],
            'list_pallet.*.tipe'    => 'between:1,2',
            'list_pallet.*.jumlah'  => 'numeric',
            'list_pallet.*.status_pallet'  => 'between:1,4',
        ];
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'id_aktivitas'              => 'Aktivitas',
            'id_gudang'                 => 'Gudang',
            'ref_number'                => 'Nomor Referensi',
            'id_pindah_area'            => 'Pindah Area',
            'id_alat_berat'             => 'Alat Berat',
            'ttd'                       => 'Tanda Tangan',
            'sistro'                    => 'Sistro',
            'approve'                   => 'Approve',
            'kelayakan_before'          => 'Kelayakan Before',
            'kelayakan_after'           => 'Kelayakan After',
            'dikembalikan'              => 'Dikembalikan',
            'list_produk.*.produk'      => 'Produk',
            'list_produk.*.pallet'      => 'Pallet',
        ];
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi!',
            'numeric'       => ':attribute harus berupa angka!',
            'image'         => ':attribute harus berupa gambar!',
            'exists'        => ':attribute tidak tersedia!',
            'between'       => ':attribute tidak valid!',
            'date_format'   => ':attribute tanggal harus dengan format tanggal-bulan-tahun, contoh: 13-05-2018',
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
                            if (is_array($input[$key][$row1][$row2])) {
                                foreach ($input[$key][$row1] as $row3 => $data3) {
                                    $input[$key][$row1][$row2][$row3] = filter_var($data3, FILTER_SANITIZE_STRING);
                                }
                            }
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
