<?php

namespace App\Http\Requests;

use App\Http\Models\Aktivitas;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\Material;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ApiAktivitasRequest extends FormRequest
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
        return $res_user->role_id == 3 || $res_user->role_id == 5;
    }

    public function getRencana()
    {
        $my_auth = request()->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);

        if ($user->role_id == 3) {
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm', $user->id_tkbm)
                ->orderBy('rencana_harian.id', 'desc')
                ->take(1)->first();
    
            if (!empty($rencana_tkbm)) {
                $rencana_harian = RencanaHarian::withoutGlobalScopes()->findOrFail($rencana_tkbm->id_rencana);
                $gudang = Gudang::findOrFail($rencana_harian->id_gudang);
                if (!empty($gudang)) {
                    return $gudang;
                }
            }
        } else if ($user->role_id == 5) {
            $karu = Karu::find($user->id_karu);
            $gudang = Gudang::find($karu->id_gudang);
            if (!empty($gudang)) {
                return $gudang;
            }
        }

        return 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $this->sanitize();
        $request = request();

        $gudang = $this->getRencana();
        


        $rules = [
            'id_aktivitas'      => 'required|exists:aktivitas,id',
            'id_gudang_tujuan'  => 'nullable|exists:gudang,id|not_in:'.$gudang->id,
            'id_alat_berat'     => 'nullable|exists:alat_berat,id',
            'id_tkbm'           => [
                'nullable',
                Rule::exists('tenaga_kerja_non_organik', 'id')->where(function($query) {
                    $query->where('job_desk_id', 2);
                }),
            ],
            'list_produk.*.produk'       => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 1);
                }),
            ],
            'list_produk.*.status_produk'    => 'between:1,2',
            'list_produk.*.list_area.*.tipe' => 'between:1,2',
            // 'list_produk.*.list_area.*.list_jumlah.*.tanggal' => 'date_format:d-m-Y',
            'list_produk.*.list_area.*.list_jumlah.*.jumlah'  => 'numeric',
            'list_pallet.*.pallet' => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 2);
                }),
            ],
            'list_pallet.*.tipe'    => 'between:1,2',
            'list_pallet.*.status_pallet'  => 'between:1,4',
        ];

        $aktivitas = Aktivitas::findOrFail($request->id_aktivitas);
        if ($aktivitas->fifo != null) {
            $rules['alasan'] = [
                'nullable',
                // 'required',
            ];
        }

        if ($aktivitas->internal_gudang != null) {
            $rules['id_gudang_tujuan'] = [
                'nullable',
                'exists:gudang,id',
                'required',
            ];
        }

        if ($aktivitas->so != null) {
            $rules['so'] = [
                'required',
            ];
        }

        if ($aktivitas->penyusutan != null) {
            $rules['id_yayasan'] = [
                'exists:yayasan,id',
                'required',
            ];
        }
        $tempJumlahProduk = [];

        if ($request->list_produk) {
            for ($i = 0; $i < count($request->list_produk); $i++) {
                $list_area = $request->list_produk[$i]['list_area'];
                for ($j = 0; $j < count($list_area); $j++) {
                    $list_jumlah = $list_area[$j]['list_jumlah'];
                    for ($k = 0; $k < count($list_jumlah); $k++) {
                        if ($list_jumlah[$k]['tanggal'] != null) {
                            $area_stok = AreaStok::where('id_area', $list_area[$j]['id_area_stok'])
                                ->where('id_material', $request->list_produk[$i]['produk'])
                                ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                ->where('status', $request->list_produk[$i]['status_produk'])
                                ->orderBy('tanggal', 'asc')
                                ->first();
                        } else {
                            $area_stok = AreaStok::where('id_area', $list_area[$j]['id_area_stok'])
                                ->where('id_material', $request->list_produk[$i]['produk'])
                                ->whereNull('tanggal')
                                ->where('status', $request->list_produk[$i]['status_produk'])
                                ->first();
                        }
                        if ($list_area[$j]['tipe'] == 1) {
                            if ($list_jumlah[$k]['tanggal'] != null) {
                                if (isset(${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk'] . '_' . date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']))})) {
                                    ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk'] . '_' . date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']))} = ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk'] . '_' . date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']))} - $list_jumlah[$k]['jumlah'];
                                } else {
                                    if (!empty($area_stok)) {
                                        ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk'] . '_' . date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']))} = $area_stok->jumlah;
                                    }
                                }
                            } else {
                                if (isset(${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk']})) {
                                    ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk']} = ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk']} - $list_jumlah[$k]['jumlah'];
                                } else {
                                    if (!empty($area_stok)) {
                                        ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk']} = $area_stok->jumlah;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        

        if ($request->list_produk) {
            for ($i = 0; $i < count($request->list_produk); $i++) {
                $list_area = $request->list_produk[$i]['list_area'];
                for ($j = 0; $j < count($list_area); $j++) {
                    $list_jumlah = $list_area[$j]['list_jumlah'];
                    for ($k = 0; $k < count($list_jumlah); $k++) {
                        if ($aktivitas->pengaruh_tgl_produksi != null) {
                            if ($list_jumlah[$k]['tanggal'] != null) {
                                $area_stok = AreaStok::where('id_area', $list_area[$j]['id_area_stok'])
                                ->where('id_material', $request->list_produk[$i]['produk'])
                                ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                ->where('status', $request->list_produk[$i]['status_produk'])
                                ->orderBy('tanggal', 'asc')
                                ->first();
                            } else {
                                $area_stok = AreaStok::where('id_area', $list_area[$j]['id_area_stok'])
                                    ->where('id_material', $request->list_produk[$i]['produk'])
                                    ->where('status', $request->list_produk[$i]['status_produk'])
                                    ->first();
                            }
                            $area = Area::find($list_area[$j]['id_area_stok']);
                            if (!empty($area_stok)) {
                                if ($list_area[$j]['tipe'] == 1) {
                                    if ($list_jumlah[$k]['tanggal'] != null) {
                                        $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                            'min:0',
                                            'max:' . ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk'] . '_' . date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']))},
                                            'numeric'
                                        ];
                                    } else {
                                        $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                            'min:0',
                                            'max:' . ${'tempJumlahProduk_' . $list_area[$j]['id_area_stok'] . '_' . $request->list_produk[$i]['produk']},
                                            'numeric'
                                        ];
                                    }
                                } else {
                                    $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                        'min:0',
                                        'numeric'
                                    ];
                                }
                            } else {
                                $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                    'min:0',
                                    'numeric'
                                ];

                                
                            }
                        } else {
                            $area = Area::find($list_area[$j]['id_area_stok']);
                            $rules['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = [
                                'min:0',
                                'max:' . $area->kapasitas,
                                'numeric',
                                // Rule::unique('area_stok')->where(function ($query) use ($list_area, $list_jumlah, $request, $i, $j, $k) {
                                //     return $query
                                //         ->where('id_area', $list_area[$j]['id_area_stok'])
                                //         ->where('id_material', $request->list_produk[$i]['produk'])
                                //         ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])));
                                // }),
                            ];
                        }
                    }
                }
            }
        }
        
        if ($request->list_pallet) {
            for ($i = 0; $i < count($request->list_pallet); $i++) {
                $gudangStok = GudangStok::where('id_material', $request->list_pallet[$i]['pallet'])
                ->where('status', $request->list_pallet[$i]['status_pallet'])
                ->where('id_gudang', $gudang->id)->first();
                if (!empty($gudangStok)) {
                    $max = $gudangStok->jumlah;
                } else {
                    $max = 0;
                }

                if ($request->list_pallet[$i]['tipe'] == 1) {
                    $rules['list_pallet.' . $i . '.jumlah'] = [
                        'min:0', 'max:' . $max, 'numeric'
                    ];
                } else {
                    $rules['list_pallet.' . $i . '.jumlah'] = [
                        'min:0', 'numeric'
                    ];
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
                        $attributes['list_produk.' . $i . '.list_area.' . $j . '.list_jumlah.' . $k . '.jumlah'] = 'Jumlah Produk '. $material->nama;
                    }
                }
            }
        }

        if ($request->list_pallet) {
            for ($i = 0; $i < count($request->list_pallet); $i++) {
                $material = Material::find($request->list_pallet[$i]['pallet']);
                $attributes['list_pallet.' . $i . '.jumlah'] =  'Jumlah Pallet '.$material->nama;
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
            'list_produk.*.list_area.*.list_jumlah.*.jumlah.max'            => ':attribute melebihi kapasitas yang tersedia!',
            'list_pallet.*.jumlah.max'                                      => ':attribute melebihi kapasitas di gudang yaitu :max pcs!',
            'min'           => ':attribute harus minimal :max pcs!',
            'date_format'   => ':attribute tanggal harus dengan format tanggal-bulan-tahun, contoh: 13-05-2018',
            'not_in'        => ':attribute tidak boleh sama dengan gudang Anda sendiri',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                foreach ($input[$key] as $row1 => $data1) {
                    if (is_array($input[$key][$row1])) {
                        foreach ($input[$key][$row1] as $row2 => $data2) {
                            $input[$key][$row1][$row2] = filter_var($data2, FILTER_SANITIZE_STRING);
                            if (is_array($input[$key][$row1][$row2])) {
                                foreach ($input[$key][$row1][$row2] as $row3 => $data3) {
                                    if (is_array($input[$key][$row1][$row2])) {
                                        foreach ($input[$key][$row1][$row2] as $row3 => $data3) {
                                        }
                                    } else {
                                        $input[$key][$row1][$row2][$row3] = filter_var($data3, FILTER_SANITIZE_STRING);
                                    }
                                }
                            } else {
                                $input[$key][$row1][$row2] = filter_var($data2, FILTER_SANITIZE_STRING);
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
