<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ReportAktivitasHarianResource extends Resource
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
            'nama_aktivitas'    => $this->aktivitas->nama,
            'nama_gudang'       => $this->gudang->nama,
            'nama_checker'      => $this->checker->nama,
            'produk'            => $this->produk,
        ];
    }
}
