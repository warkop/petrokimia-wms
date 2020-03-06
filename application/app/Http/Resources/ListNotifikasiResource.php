<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ListNotifikasiResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Carbon::setLocale('id');

        return [
            'id_aktivitas_harian'   => $this->id,
            'id_aktivitas'          => $this->id_aktivitas,
            'kode_aktivitas'        => $this->aktivitas->kode_aktivitas,
            'nama'                  => $this->aktivitas->nama,
            'asal_gudang'           => $this->gudang->nama,
            'gudang_tujuan'         => $this->gudangTujuan->nama,
            'waktu'                 => $this->created_at,
            'created_at'            => [
                'date' => date('Y-m-d H:i:s', strtotime(
                    $this->created_at
                )),
                'timezone_type' => 3,
                'timezone' => 'Asia/Jakarta'
            ],
        ];
    }
}
