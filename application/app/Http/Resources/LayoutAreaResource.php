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
        if ($this->terpakai == $this->kapasitas) {
            $warna = '#fd397a';
        } else if ($this->terpakai >= $this->kapasitas* 70/100 && $this->terpakai != $this->kapasitas) {
            $warna = '#fbaa00';
        } else {
            $warna = '#08976d';
        }

        return [
            'id'        => $this->id,
            'id_gudang' => $this->id_gudang,
            'nama'      => $this->nama,
            'kapasitas' => $this->kapasitas,
            'tipe'      => $this->tipe,
            'terpakai'  => $this->terpakai,
            'warna'     => $warna,
            'koordinat' => json_decode($this->koordinat),
        ];
    }
}
