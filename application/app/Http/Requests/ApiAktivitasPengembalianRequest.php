<?php

namespace App\Http\Requests;

use App\Http\Models\Aktivitas;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ApiAktivitasPengembalianRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        // Get all the errors thrown
        $errors = collect($validator->errors());
        // Manipulate however you want. I'm just getting the first one here,
        // but you can use whatever logic fits your needs.
        $error  = $errors->unique()->first();
        $mess = [];
        foreach ($errors->unique() as $key => $value) {
            foreach ($value as $row) {
                array_push($mess, $row);
            }
        }
        $responses['message'] = "The given data was invalid.";
        $responses['errors'] = $mess;
        $responses['code'] = 422;

        // Either throw the exception, or return it any other way.
        throw new HttpResponseException(response(
            $responses,
            422
        ));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $my_auth = request()->get('my_auth');
        $res_user = Users::findOrFail($my_auth->id_user);
        return $res_user->role_id == 5;
    }

    public function getGudang()
    {
        $my_auth = request()->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);

        $gudang = Gudang::where('id_karu', $user->id_karu)->first();

        return $gudang;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = request();

        $gudang = $this->getGudang();

        $materialTrans = MaterialTrans::where('id_aktivitas_harian', $request->id_aktivitas_harian)->get();

        $rules = [
            'id_aktivitas_harian'      => [
                'required',
                Rule::exists('aktivitas_harian', 'id')->where(function ($query) {
                    $query->whereNull('dikembalikan');
                }),
            ],
            'id_gudang_tujuan'  => 'nullable|exists:gudang,id',
            'id_alat_berat'     => 'nullable|exists:alat_berat,id',
            'list_produk.*.produk'       => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 1);
                })
            ],
            'list_produk.*.status_produk'    => 'between:1,2',
            'list_produk.*.list_area.*.tipe' => 'between:1,2',
            'list_produk.*.list_area.*.list_jumlah.*.tanggal' => 'date_format:d-m-Y',
            'list_pallet.*.pallet' => [
                Rule::exists('material_trans', 'id_material')->where(function ($query) {
                    $query->where('id_aktivitas_harian', request()->id_aktivitas_harian);
                    ;
                })
            ],
            'list_pallet.*.tipe'    => 'between:1,2',
            'list_pallet.*.status_pallet'  => 'between:1,4',
        ];


        if ($request->list_produk) {
            for ($i = 0; $i < count($request->list_produk); $i++) {
                $list_area = $request->list_produk[$i]['list_area'];
                for ($j = 0; $j < count($list_area); $j++) {
                    $list_jumlah = $list_area[$j]['list_jumlah'];
                    for ($k = 0; $k < count($list_jumlah); $k++) {
                            $area_stok = AreaStok::where('id_area', $list_area[$j]['id_area_stok'])
                                ->where('id_material', $request->list_produk[$i]['produk'])
                                ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                ->where('status', $request->list_produk[$i]['status_produk'])
                                ->orderBy('tanggal', 'asc')
                                ->first();
                            
                            $materialTrans = MaterialTrans::where('id_aktivitas_harian', $request->id_aktivitas_harian)
                            ->where('id_material', $request->list_produk[$i]['produk'])
                            ->get();

                            $area = Area::find($list_area[$j]['id_area_stok']);
                            if (!empty($area_stok)) {
                                if ($list_area[$j]['tipe'] == 1) {
                                    $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                        'min:0',
                                        'max:' . $area_stok->jumlah,
                                        'numeric'
                                    ];
                                } else {
                                    $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                        'min:0',
                                        'max:' . abs($area->kapasitas - $area_stok->jumlah),
                                        'numeric'
                                    ];
                                }
                            } else {
                                $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                    'min:0',
                                    'max:' . $area->kapasitas,
                                    'numeric'
                                ];
                            }
                    }
                }
            }
        }

        if ($request->list_pallet) {
            for ($i = 0; $i < count($request->list_pallet); $i++) {
                $materialTrans = MaterialTrans::where('id_aktivitas_harian', $request->id_aktivitas_harian)
                    ->where('id_material', $request->list_pallet[$i]['pallet'])
                    ->where('status_pallet', $request->list_pallet[$i]['status_pallet'])
                    ->first();
                if ($materialTrans) {
                    $sejumlah = $materialTrans->jumlah;
                    
                    if ($request->list_pallet[$i]['tipe'] == 1) {
                        $gudangStok = GudangStok::where('id_material', $request->list_pallet[$i]['pallet'])
                            ->where('status', $request->list_pallet[$i]['status_pallet'])
                            ->where('id_gudang', $gudang->id)->first();
                        $rules['list_pallet.' . $i . '.jumlah'] = [
                            'max:' . $gudangStok->jumlah,
                            'numeric'
                        ];
                    } else {
                        $rules['list_pallet.' . $i . '.jumlah'] = [
                            'min:' . $sejumlah,
                            'max:' . $sejumlah,
                            'numeric'
                        ];
                    }
                }

            }
        }
        return $rules;
    }

    public function attributes()
    {
        $request = request();
        $attributes = [
            'id_aktivitas'              => 'Aktivitas',
            'id_gudang'                 => 'Gudang',
            'id_gudang_tujuan'          => 'Gudang Tujuan',
            'id_yayasan'                => 'Yayasan',
            'ref_number'                => 'Nomor Referensi',
            'id_pindah_area'            => 'Pindah Area',
            'id_alat_berat'             => 'Alat Berat',
            'ttd'                       => 'Tanda Tangan',
            'sistro'                    => 'Sistro',
            'approve'                   => 'Approve',
            'kelayakan_before'          => 'Kelayakan Before',
            'kelayakan_after'           => 'Kelayakan After',
            'dikembalikan'              => 'Dikembalikan',
            'alasan'                    => 'Alasan',
            'so'                        => 'SO',
            'list_produk.*.produk'      => 'Produk',
            'list_pallet.*.pallet'      => 'Pallet',
            'list_pallet.*.status_pallet'      => 'Status Pallet',
        ];

        if ($request->list_produk) {
            for ($i = 0; $i < count($request->list_produk); $i++) {
                $material = Material::find($request->list_produk[$i]['produk']);
                $list_area = $request->list_produk[$i]['list_area'];
                for ($j = 0; $j < count($list_area); $j++) {
                    $list_jumlah = $list_area[$j]['list_jumlah'];
                    for ($k = 0; $k < count($list_jumlah); $k++) {
                        $attributes['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = 'Jumlah Produk ' . $material->nama;
                    }
                }
            }
        }

        if ($request->list_pallet) {
            for ($i = 0; $i < count($request->list_pallet); $i++) {
                $material = Material::find($request->list_pallet[$i]['pallet']);
                $attributes['list_pallet.' . $i . '.jumlah'] =  'Jumlah Pallet ' . $material->nama;
            }
        }

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi!',
            'numeric'       => ':attribute harus berupa angka!',
            'image'         => ':attribute harus berupa gambar!',
            'exists'        => ':attribute yang dipilih tidak ditemukan!',
            'between'       => ':attribute tidak valid!',
            'list_produk.*.list_area.*.list_jumlah.*.jumlah.max'            => ':attribute melebihi kapasitas di area yaitu :max ton!',
            'list_pallet.*.jumlah.max'                                      => ':attribute melebihi kapasitas di gudang yaitu :max pcs!',
            'min'           => ':attribute harus minimal :min pcs!',
            'date_format'   => ':attribute tanggal harus dengan format tanggal-bulan-tahun, contoh: 13-05-2018',
        ];
    }
}
