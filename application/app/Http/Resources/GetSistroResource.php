<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetSistroResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->kategori == 1) {
            $text_kategori = 'Produk';
        } else if ($this->kategori == 2) {
            $text_kategori = 'Pallet';
        } else {
            $text_kategori = 'Lain-lain';
        }
        return [
            'id'                => $this->id,
            'id_material_sap'   => $this->id_material_sap,
            'kategori'          => $this->kategori,
            'text_kategori'     => $text_kategori,
            'berat'             => $this->berat,
            'koefisien_pallet'  => $this->koefisien_pallet,
            'nama'              => $this->nama,
            'booking_no'        => $this->sistro->bookingno,
            'tiket_no'          => $this->sistro->tiketno,
            'nopol'             => $this->sistro->nopol,
            'driver'            => $this->sistro->driver,
            'sistro_qty'        => $this->sistro->qty,
            'tanggal'           => $this->sistro->tanggal,
        ];
    }
}
