<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        $action = \Request::instance()->action;
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
            'upload_foto'               => 'nullable|numeric',
            'connect_sistro'            => 'nullable|numeric',
            'pengiriman'                => 'nullable|numeric',
            'fifo'                      => 'nullable|numeric',
            'kelayakan'                 => 'nullable|numeric',
            'butuh_biaya'               => 'nullable|numeric',
            'peminjaman'                => 'nullable|numeric',
            'pengaruh_tgl_produksi'     => 'nullable|numeric',
            'internal_gudang'           => 'nullable|numeric',
            'butuh_alat_berat'          => 'nullable|numeric',
            'butuh_tkbm'                => 'nullable|numeric',
            'tanda_tangan'              => 'nullable|numeric',
            'butuh_approval'            => 'nullable|numeric',
            'start_date'                => 'nullable',
            'end_date'                  => 'nullable|after:start_date',
        ];


        return $rules;
    }

    public function attributes()
    {
        return [
            'produk_stok'               =>  'Produk',
            'produk_rusak'              => 'Produk Rusak',
            'pallet_stok'               => 'Pallet Stok',
            'pallet_dipakai'            => 'Pallet Dipakai',
            'pallet_kosong'             => 'Pallet Kosong',
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
            'tanda_tangan'              => 'Tanda Tangan',
            'butuh_approval'            => 'Butuh Approval',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama Aktivitas wajib diisi!',
            'numeric' => 'Inputan :attribute tidak valid!',
            'start_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.date_format'  => 'Tanggal harus dengan format tanggal-bulan-tahun',
            'end_date.after'  => 'Tanggal End Date tidak boleh melebihi Start Date!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['nama']          = filter_var($input['nama'], FILTER_SANITIZE_STRING);
        // $input['produk_stok']   = filter_var($input['produk_stok'], FILTER_SANITIZE_STRING);
        // $input['produk_rusak']  = filter_var($input['produk_rusak'], FILTER_SANITIZE_STRING);
        // $input['pallet_stok']   = filter_var($input['pallet_stok'], FILTER_SANITIZE_STRING);
        // $input['pallet_dipakai']= filter_var($input['pallet_dipakai'], FILTER_SANITIZE_STRING);
        // $input['pallet_kosong'] = filter_var($input['pallet_kosong'], FILTER_SANITIZE_STRING);
        // $input['upload_foto']   = filter_var($input['upload_foto'], FILTER_SANITIZE_STRING);
        
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
