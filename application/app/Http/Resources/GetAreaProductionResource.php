<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetAreaProductionResource extends Resource
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
            'id'        => $this->id,
            'nama'      => $this->nama,
            'kapasitas' => $this->kapasitas,
            'tipe'      => $this->tipe,
            'tanggal'   => $this->tanggal,
            'jumlah'    => $this->jumlah,
        ];
    }
}
