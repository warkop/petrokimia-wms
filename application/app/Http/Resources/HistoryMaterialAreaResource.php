<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class HistoryMaterialAreaResource extends Resource
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
            'id_area'   => $this->areaStok->id_area,
            'nama'      => $this->areaStok->material->nama,
            'tanggal'   => $this->areaStok->tanggal,
            'jumlah'    => $this->jumlah,
        ];
    }
}
