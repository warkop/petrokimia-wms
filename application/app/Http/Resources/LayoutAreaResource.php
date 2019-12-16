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
        // if ($this->sum($this->areaStok->jumlah) == $this->kapasitas) {
        //     $warna = 'red';
        // } else if ($this->sum($this->areaStok->jumlah) == $this->kapasitas* 70/100) {
        //     $warna = 'yellow';
        // } else {
        //     $warna = 'green';
        // }

        return [
            'id'        => $this->id,
            'id_gudang' => $this->id_gudang,
            'nama'      => $this->nama,
            'kapasitas' => $this->kapasitas,
            'tipe'      => $this->tipe,
            // 'warna'     => $warna,
            'koordinat' => json_decode($this->koordinat),
        ];
    }
}
