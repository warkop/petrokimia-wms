<?php

namespace App\Http\Requests;

use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\Material;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class RealisasiMaterialRequest extends FormRequest
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
        return true;
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
        $gudang = $this->getRencana();

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $rules = [
            'list_material.*.material'          => [
                'required',
                Rule::exists('material', 'id')->where(function ($query) {
                    $query->where('kategori', 3);
                }),
            ],
            // 'list_material.*.jumlah'            => 'numeric|digits_between:1,10',
            'list_material.*.tipe'              => 'between:1,2',
        ];
        
        $i=0;
        foreach (request()->list_material as $key) {
            if ($key['tipe'] == 1) {
                $gudangStok = GudangStok::where('id_material', $key['material'])->where('id_gudang', $gudang->id)->where('status', 1)->first();
                $rules['list_material.' . $i . '.jumlah'] = 'numeric|digits_between:1,10|max:'. $gudangStok->jumlah;
            } else {
                $rules['list_material.' . $i . '.jumlah'] = 'numeric|digits_between:1,10';
            }
            $i++;
        }

        $this->sanitize();

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'list_material.*.material'        => 'Material',
            'list_material.*.tipe'            => 'Tipe',
        ];

        if (request()->list_material) {
            for ($i = 0; $i < count(request()->list_material); $i++) {
                $material = Material::find(request()->list_material[$i]['material']);
                $attributes['list_material.' . $i . '.jumlah'] =  'Jumlah ' . $material->nama;
            }
        }

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'       => ':attribute wajib diisi!',
            'numeric'        => ':attribute harus berupa angka!',
            'between'        => ':attribute tidak valid!',
            'exists'         => ':attribute tidak tersedia!',
            'max'            => ':attribute tidak boleh dari :max!',
            'digits_between' => ':attribute harus antara :min sampai :max digits!',
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
