<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class HistoryMaterialAreaResource extends Resource
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
            'id_area'   => $this->id_area,
            'nama'      => $this->nama_area,
            'nama_barang' => $this->nama_barang,
            'tipe'      => $this->tipe,
            'status_produk'      => $this->status_produk,
            'status_pallet'      => $this->status_pallet,
            'tanggal'   => [
                'date' => $this->tanggal],
            'jumlah'    => $this->jumlah,
        ];
    }
}
