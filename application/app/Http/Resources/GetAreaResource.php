<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetAreaResource extends Resource
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
            'id'            => $this->id,
            'nama'          => $this->nama,
            'kapasitas'     => $this->kapasitas,
            'tanggal'       => $this->tanggal,
            'tipe'          => $this->tipe,
            'id_material'   => $this->id_material,
            'jumlah'        => round($this->jumlah, 2),
            'jumlah_area'   => round($this->jumlah_area, 2),
        ];
    }
}
