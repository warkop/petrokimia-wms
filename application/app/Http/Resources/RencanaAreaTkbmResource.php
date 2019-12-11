<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RencanaAreaTkbmResource extends Resource
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
            // 'id_rencana'    => $this->id_rencana,
            'id_area'       => $this->id_area,
            'id_tkbm'       => $this->id_tkbm,
            'nama'          => $this->nama,
            'nama_area'     => $this->nama_area,
            // 'foto'          => $this->realisasi,
        ];
    }
}
