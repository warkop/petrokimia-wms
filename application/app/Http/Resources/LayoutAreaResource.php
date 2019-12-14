<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LayoutAreaResource extends Resource
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
            'tipe'      => $this->tipe,
            'koordinat' => json_decode($this->koordinat),
        ];
    }
}
