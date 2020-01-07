<?php

namespace App\Http\Requests;

use App\Http\Models\Gudang;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiAktivitasPenerimaanGiRequest extends FormRequest
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
        $gudang = $this->getRencana();
        $rules = [
            'id_aktivitas_harian'   => 'required|numeric|exists:aktivitas_harian,id',
            'id_gudang'         => 'nullable|numeric',
            'id_karu'           => 'nullable|numeric',
            'id_alat_berat'     => 'nullable|numeric|exists:alat_berat,id',
            'id_pindah_area'    => 'nullable|numeric',
            'id_alat_berat'     => 'nullable|numeric',
            'list_produk.*.produk'       => [
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 1);
                })
            ],
            'list_produk.*.list_area.*.id_area_stok' => [
                'required',
                Rule::exists('area', 'id')->where(function ($query) use($gudang) {
                    $query->where('id_gudang', $gudang->id);
                })
            ],
            // 'sistro'            => 'Sistro',
            // 'approve'           => 'Approve',
            // 'dikembalikan'      => 'Dikembalikan',
        ];

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        return [
            'id_aktivitas_harian'=> 'Aktivitas Harian',
            'id_gudang'         => 'Gudang',
            'id_karu'           => 'Karu',
            'id_shift'          => 'Shift',
            'ref_number'        => 'Nomor Referensi',
            'id_pindah_area'    => 'Pindah Area',
            'id_alat_berat'     => 'Alat Berat',
            'ttd'               => 'Tanda Tangan',
            'sistro'            => 'Sistro',
            'approve'           => 'Approve',
            'kelayakan_before'  => 'Kelayakan Before',
            'kelayakan_after'   => 'Kelayakan After',
            'dikembalikan'      => 'Dikembalikan',
            'id_produk.*'       => 'Produk',
            'list_produk.*.list_area.*.id_area_stok' => 'Area',
        ];
    }

    public function messages()
    {
        return [
            'required'  => ':attribute wajib diisi!',
            'exists'    => ':attribute tidak tersedia!',
            'numeric'   => ':attribute harus berupa angka!',
            'image'     => ':attribute harus berupa gambar!',
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
