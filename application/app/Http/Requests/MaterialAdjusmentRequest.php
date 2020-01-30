<?php

namespace App\Http\Requests;

use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

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
            'tanggal_produksi.*'=> 'required',
            'action_produk.*'   => 'required|numeric|between:1,2',
            'produk_jumlah.*'   => 'numeric',
            'pallet.*'          => 'required',
            'action_pallet.*'   => 'required|numeric|between:1,2',
            'pallet_jumlah.*'   => 'numeric',
            'tanggal'           => 'required',
        ];

        if (request()->produk_jumlah) {
            for ($i = 0; $i < count(request()->produk_jumlah); $i++) {
                $area = Area::find(request()->area[$i]);
                if (request()->action_produk[$i] == 1) {
                    $areaStok = AreaStok::where('id_area', $area->id)
                    ->where('id_material', request()->produk[$i])
                    ->where('tanggal', date('Y-m-d', strtotime(request()->tanggal_produksi[$i])))
                    ->where('status', 1)
                    ->sum('jumlah');
                    $rules['produk_jumlah.' . $i] = 'numeric|max:'. (float)$areaStok;
                } else {
                    $areaStok = AreaStok::where('id_area', $area->id)
                    ->where('status', 1)
                    ->sum('jumlah');

                    $maximum = (float) $areaStok;
                    if ($area->kapasitas != null) {
                        $maximum = abs((float) ((float) $area->kapasitas - (float) $areaStok));
                    }
                    $rules['produk_jumlah.'.$i] = 'numeric|max:'.$maximum;
                }
            }
        }

        if (request()->pallet_jumlah) {
            for ($i = 0; $i < count(request()->pallet_jumlah); $i++) {
                if (request()->action_pallet[$i] == 1) {
                    $gudangStok = GudangStok::where('id_gudang', request()->id_gudang)
                    ->where('id_material', request()->pallet[$i])
                    ->where('status', 1)
                    ->sum('jumlah');
                    $rules['pallet_jumlah.' . $i] = 'numeric|max:' . (float) $gudangStok;
                }
            }
        }

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
            'produk_jumlah.max'           => ':attribute melebihi kapasitas area atau tidak tersedia pada stok!',
            'pallet_jumlah.*.max'           => ':attribute melebihi jumlah yang tersedia di gudang!',
        ];
    }

    public function attributes()
    {
        $attributes =  [
            'produk.*'              => 'Produk',
            'area.*'                => 'Area',
            'pallet.*'              => 'Pallet',
            'tanggal_produksi.*'    => 'Tanggal Produksi',
            'action_produk.*'       => 'Jenis aksi produk',
            'action_pallet.*'       => 'Jenis aksi pallet',
            'produk_alasan.*'       => 'Alasan Produk',
            'pallet_alasan.*'       => 'Alasan Pallet',
            'tanggal'               => 'Tanggal',
        ];

        if (request()->produk) {
            for ($i = 0; $i < count(request()->produk); $i++) {
                $material = Material::find(request()->produk[$i]);
                $area = Area::find(request()->area[$i]);

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
                    $input[$key][$row] = strip_tags($nilai);
                }
            } else {
                $input[$key] = strip_tags($value);
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
