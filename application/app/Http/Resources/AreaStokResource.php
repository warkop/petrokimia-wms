<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AreaStokResource extends Resource
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
            'id'        => $this->area->id,
            'nama'      => $this->area->nama,
            'kapasitas' => $this->area->kapasitas,
            'tanggal'   => $this->tanggal,
            'jumlah'    => $this->jumlah,
        ];
    }
}
