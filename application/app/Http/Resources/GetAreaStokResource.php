<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetAreaStokResource extends Resource
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
            'tanggal'   => $this->tanggal,
            'status'    => $this->status,
            'tipe'      => $this->tipe,
            'jumlah'    => $this->jumlah,
        ];
    }
}
