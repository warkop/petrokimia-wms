<?php

namespace App\Http\Resources;

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
        return [
            'id'                => $this->id,
            'id_aktivitas'      => $this->id_aktivitas,
            'kode_aktivitas'    => $this->aktivitas->kode_aktivitas,
            'nama'              => $this->aktivitas->nama,
            'asal_gudang'       => $this->gudang->nama,
            'gudang_tujuan'     => $this->gudangTujuan->nama,
        ];
    }
}
