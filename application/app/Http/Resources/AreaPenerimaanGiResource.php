<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AreaPenerimaanGiResource extends Resource
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
            'id'            => $this->areaStok->id,
            'nama_area'     => $this->areaStok->area->nama,
            // 'nama_material' => $this->areaStok->material->nama,
            // 'id_material'   => $this->areaStok->id_material,
            'id_area'       => $this->areaStok->id_area,
            'tanggal'       => $this->areaStok->tanggal,
            'jumlah'        => $this->areaStok->jumlah,
            'kapasitas'     => $this->areaStok->area->kapasitas,
        ];
    }
}
