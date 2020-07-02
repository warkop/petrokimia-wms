<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LayoutGetDataResource extends Resource
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
            'id' => $this->id,
            'nama_area' => $this->nama_area,
            'nama_gudang' => $this->nama_gudang,
            'tipe_gudang' => $this->tipe_gudang,
            'kapasitas' => $this->kapasitas,
            'tipe_area' => $this->tipe_area,
            'total' => (string)round($this->total, 3),
            'text_tipe_gudang' => $this->text_tipe_gudang,
            'text_tipe_area' => $this->text_tipe_area
        ];
    }
}
