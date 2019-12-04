<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MaterialTransResource extends Resource
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
            'id_material' => $this->id_material,
            'id_adjustment' => $this->id_adjustment,
            'tanggal' => $this->tanggal,
            'tipe' => $this->tipe,
            'text_tipe' => $this->when($this->tipe == 1, 'mengurangi', 'menambah'),
            'jumlah' => $this->jumlah,
            'alasan' => $this->alasan,
            'id_realisasi_material' => $this->id_realisasi_material,
            'id_aktivitas_harian' => $this->id_aktivitas_harian,
            'status_pallet' => $this->status_pallet,
            'status_produk' => $this->status_produk,
            'id_gudang_stok' => $this->id_gudang_stok,
            'nama_material' => $this->material->nama,
        ];
    }
}
