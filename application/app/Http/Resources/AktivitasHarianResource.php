<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AktivitasHarianResource extends Resource
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
            'id_aktivitas' => $this->id_aktivitas,
            'id_gudang' => $this->id_gudang,
            'id_karu' => $this->id_karu,
            'id_shift' => $this->id_shift,
            'nama_aktivitas' => $this->aktivitas->nama,
            'ref_number' => $this->ref_number,
            'id_area' => $this->id_area,
            'id_alat_berat' => $this->id_alat_berat,
            'ttd' => $this->ttd,
            'sistro' => $this->sistro,
            'approve' => $this->approve,
            'kelayakan_before' => $this->kelayakan_before,
            'kelayakan_after' => $this->kelayakan_after,
            'dikembalikan' => $this->dikembalikan,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'created_by' => $this->created_by,
            'id_gudang_tujuan' => $this->id_gudang_tujuan,
            'alasan' => $this->alasan,
            'list_material' => MaterialResource::collection($this->whenLoaded('material_trans')),
        ];
    }
}
