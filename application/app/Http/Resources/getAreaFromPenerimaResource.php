<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class getAreaFromPenerimaResource extends Resource
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
            'id_gudang' => $this->id_gudang,
            'nama'      => $this->nama,
            'kapasitas' => $this->kapasitas,
            'terpakai'  => $this->jumlah,
            'tipe'      => $this->tipe,
            'kode_area' => $this->kode_area,
        ];
    }
}
