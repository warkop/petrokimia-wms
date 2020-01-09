<?php

namespace App\Http\Requests;

use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Material;
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
        

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'produk.*'          => 'required',
            'area.*'            => 'required',
            'pallet.*'          => 'required',
            'action_produk.*'   => 'required|numeric|between:1,2',
            'action_pallet.*'   => 'required|numeric|between:1,2',
            'produk_jumlah.*'   => 'numeric',
            'pallet_jumlah.*'   => 'numeric',
            'tanggal'           => 'required',
        ];

        for ($i = 0; $i < count(request()->produk_jumlah); $i++) {
            $area = Area::find(request()->area[$i]);
            $areaStok = AreaStok::where('id_area', $area->id)->sum('jumlah');
            if (request()->action_produk[$i] == 1) {
                $rules['produk_jumlah.'.$i] = 'numeric|max:'. abs((float)((float)$area->kapasitas - (float)$areaStok));
            } else {
                $rules['produk_jumlah.' . $i] = 'numeric';
            }
        }

        // for ($i = 0; $i < count(request()->produk_jumlah); $i++) {
        //     $area = Area::find(request()->area[$i]);
        //     $areaStok = GudangStok::where('id_area', $area->id)->sum('jumlah');
        //     if (request()->action_produk[$i] == 1) {
        //         $rules['produk_jumlah.' . $i] = 'numeric|max:' . (float) ((float) $area->kapasitas - (float) $areaStok);
        //     } else {
        //         $rules['produk_jumlah.' . $i] = 'numeric';
        //     }
        // }

        $this->sanitize();

        return $rules;
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi!',
            'numeric'       => ':attribute harus berupa angka!',
            'between'       => ':attribute yang dimasukkan tidak valid!',
            'date_format'   => ':attribute harus dengan format tanggal-bulan-tahun!',
            'max'           => ':attribute harus kurang dari :max!',
        ];
    }

    public function attributes()
    {
        $attributes =  [
            'produk.*'              => 'Produk',
            'area.*'                => 'Area',
            'pallet.*'              => 'Pallet',
            'action_produk.*'       => 'Jenis aksi produk',
            'action_pallet.*'       => 'Jenis aksi pallet',
            'produk_alasan.*'       => 'Alasan Produk',
            'pallet_alasan.*'       => 'Alasan Pallet',
            'tanggal'               => 'Tanggal',
        ];

        if (request()->produk) {
            for ($i = 0; $i < count(request()->produk); $i++) {
                $material = Material::find(request()->produk[0]);
                $area = Area::find(request()->area[0]);
                $attributes['produk_jumlah.' . $i] =  'Jumlah Produk <strong>' . $material->nama. '</strong> pada area <strong>'. $area->nama. '</strong>';
            }
        }

        if (request()->pallet) {
            for ($i = 0; $i < count(request()->pallet); $i++) {
                $material = Material::find(request()->pallet[0]);
                $attributes['pallet_jumlah.' . $i] =  'Jumlah Pallet <strong>' . $material->nama. '</strong>';
            }
        }

        return $attributes;
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

        if ($input['tanggal'] != '') {
            $input['tanggal']   = date('Y-m-d', strtotime($input['tanggal']));
        } else {
            $input['tanggal'] = null;
        }

        $this->replace($input);
    }
}
