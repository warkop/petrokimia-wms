<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LayoutGetDetailResource extends Resource
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
            "id"            => $this->id,
            "id_material"   => $this->id_material,
            "nama_area"     => $this->nama_area,
            "nama_material" => $this->nama_material,
            "tanggal"       => date('Y-m-d', strtotime($this->tanggal)),
            "jumlah"        => (string)round($this->jumlah),
            "kapasitas"     => $this->kapasitas,
            "status"        => $this->status
        ];
    }
}
