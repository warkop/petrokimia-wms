<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Factory;

class AktivitasRequest extends FormRequest
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

        $action = request()->action;
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }


        $rules = [
            'nama'                      => 'required',
            'produk_stok'               => 'nullable|numeric',
            'produk_rusak'              => 'nullable|numeric',
            'pallet_stok'               => 'nullable|numeric',
            'pallet_dipakai'            => 'nullable|numeric',
            'pallet_kosong'             => 'nullable|numeric',
            'pallet_rusak'              => 'nullable|numeric',
            'connect_sistro'            => 'nullable|numeric',
            'pengiriman'                => 'nullable|numeric',
            'pengaruh_tgl_produksi'     => 'nullable|numeric',
            'fifo'                      => 'nullable|numeric',
            'kelayakan'                 => 'nullable|numeric',
            'butuh_biaya'               => 'nullable|numeric',
            'peminjaman'                => 'nullable|numeric',
            'internal_gudang'           => 'nullable|numeric',
            'butuh_tkbm'                => 'nullable|numeric',
            'pengiriman_produk_rusak'   => 'nullable|numeric',
            'cancelable'                => 'nullable|numeric',
            'tanda_tangan'              => 'nullable|numeric',
            'start_date'                => 'nullable|date_format:d-m-Y',
            'end_date'                  => 'nullable|date_format:d-m-Y|after:start_date',
            'upload_foto.*'             => 'nullable|numeric',
            'alat_berat.*'              => 'nullable|numeric',
            'kode_aktivitas'            => 'nullable|size:3',
        ];

        if (request()->pallet_stok != null) {
        }
        if (request()->pallet_dipakai != null) {
            if (request()->input('pallet_kosong') != null) {
                $rules['pallet_dipakai'] = 'different:pallet_kosong';
            } else if (request()->input('pallet_rusak') != null) {
                $rules['pallet_dipakai'] = 'different:pallet_rusak';
            } else if (request()->input('pallet_stok') != null) {
                $rules['pallet_dipakai'] = 'same:pallet_stok';
            }
        }
        if (request()->pallet_kosong != null) {
            if (request()->input('pallet_dipakai') != null) {
                $rules['pallet_kosong'] = 'different:pallet_dipakai';
            } else if (request()->input('pallet_rusak') != null) {
                $rules['pallet_kosong'] = 'different:pallet_rusak';
            } else if (request()->input('pallet_stok') != null) {
                $rules['pallet_dipakai'] = 'same:pallet_stok';
            }
        }
        if (request()->pallet_rusak != null) {
            if (request()->input('pallet_kosong') != null) {
                $rules['pallet_rusak'] = 'different:pallet_kosong';
            } else if (request()->input('pallet_dipakai') != null) {
                $rules['pallet_rusak'] = 'different:pallet_kosong';
            } else if (request()->input('pallet_stok') != null) {
                $rules['pallet_dipakai'] = 'same:pallet_stok';
            }
        }

        if (request()->fifo != null) {
            $rules['pengaruh_tgl_produksi'] = 'nullable|numeric|required';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'kode_aktivitas'            => 'Kode Aktivitas',
            'nama'                      => 'Nama Aktivitas',
            'produk_stok'               => 'Produk',
            'produk_rusak'              => 'Produk Rusak',
            'pallet_stok'               => 'Pallet Stok',
            'pallet_dipakai'            => 'Pallet Dipakai',
            'pallet_kosong'             => 'Pallet Kosong',
            'pallet_rusak'              => 'Pallet Rusak',
            'upload_foto'               => 'Upload Foto',
            'connect_sistro'            => 'Connect Sistro',
            'pengiriman'                => 'Pengiriman',
            'fifo'                      => 'FIFO',
            'kelayakan'                 => 'Kelayakan',
            'butuh_biaya'               => 'Butuh Biaya',
            'peminjaman'                => 'Peminjaman',
            'pengaruh_tgl_produksi'     => 'Pengaruh Tanggal Produksi',
            'internal_gudang'           => 'Pengiriman internal Gudang',
            'butuh_alat_berat'          => 'Butuh Alat Berat',
            'butuh_tkbm'                => 'Butuh Tkbm',
            'pengiriman_produk_rusak'   => 'Pengiriman Produk Rusak',
            'cancelable'                => 'Cancelable',
            'tanda_tangan'              => 'Tanda Tangan',
            'butuh_approval'            => 'Butuh Approval',
            'anggaran_tkbm'             => 'Anggaran Tkbm',
            'upload_foto.*'             => 'Upload Foto',
            'alat_berat.*'              => 'Alat Berat',
            'anggaran.*'                => 'Anggaran',
            'pallet_dump'               => 'Pallet',
        ];
    }

    public function messages()
    {
        return [
            'required'                  => ':attribute wajib diisi!',
            'pengaruh_tgl_produksi.required'                  => ':attribute wajib dicentang apabila FIFO dicentang!',
            'max'                       => 'pilihan :attribute hanya boleh maksimal :max jenis!',
            'numeric'                   => 'Inputan :attribute tidak valid!',
            'size'                      => ':attribute harus :size karakter!',
            'start_date.date_format'    => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'      => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.after'            => 'Tanggal End Date tidak boleh melebihi Start Date!',
            'same'                      => 'Pilihan jenis :attribute harus sama dengan :other!',
            'different'                 => 'Pilihan jenis :attribute harus berbeda dengan :other!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['nama']          = strip_tags($input['nama']);
        
        $input['start_date'] = filter_var(
            $input['start_date'],
            FILTER_SANITIZE_STRING
        );
        
        $input['end_date'] = filter_var(
            $input['end_date'],
            FILTER_SANITIZE_STRING
        );

        if ($input['start_date'] != '') {
            $input['start_date']  = date('Y-m-d', strtotime($input['start_date']));
        } else {
            $input['start_date'] = null;
        }

        if ($input['end_date'] != '') {
            $input['end_date']   = date('Y-m-d', strtotime($input['end_date']));
        } else {
            $input['end_date'] = null;
        }

        $this->replace($input);
    }
}
