<?php

namespace App\Http\Requests;

use App\Http\Models\Aktivitas;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
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
        $my_auth = request()->get('my_auth');
        $res_user = Users::findOrFail($my_auth->id_user);
        return $res_user->role_id == 3;
    }

    public function getRencana()
    {
        $my_auth = request()->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);

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
            'list_produk.*.list_area.*.list_jumlah.*.jumlah'  => 'numeric',
            'list_pallet.*.pallet' => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 2);
                })
            ],
            'list_pallet.*.tipe'    => 'between:1,2',
            'list_pallet.*.status_pallet'  => 'between:1,4',
        ];

        $aktivitas = Aktivitas::findOrFail($request->id_aktivitas);
        if ($aktivitas->fifo != null) {
            $rules['alasan'] = [
                'nullable',
                'required',
            ];
        }

        if ($aktivitas->internal_gudang != null) {
            $rules['id_gudang_tujuan'] = [
                'nullable',
                'exists:gudang,id',
                'required',
            ];
        }

        for ($i = 0; $i < count($request->list_pallet); $i++) {
            $gudangStok = GudangStok::where('id_material', $request->list_pallet[0]['pallet'])
            ->where('status', $request->list_pallet[0]['status_pallet'])
            ->where('id_gudang', $gudang->id)->first();
            if (!empty($gudangStok)) {
                $max = $gudangStok->jumlah;
            } else {
                $max = 0;
            }

            foreach ($request->list_pallet as $key) {
                if ($key['tipe'] == 1) {
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
            'list_produk.*.produk'      => 'Produk',
            'list_pallet.*.pallet'      => 'Pallet',
            'list_pallet.*.status_pallet'      => 'Status Pallet',
        ];

        for ($i = 0; $i < count($request->list_pallet); $i++) {
            $material = Material::find($request->list_pallet[0]['pallet']);
            $attributes['list_pallet.' . $i . '.jumlah'] =  'Jumlah Pallet '.$material->nama;
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
            'max'           => ':attribute melebihi kapasitas di gudang yaitu :max pcs!',
            'min'           => ':attribute harus minimal :max pcs!',
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
