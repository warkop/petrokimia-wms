<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TestResource extends Resource
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
            'id'                => $this->id,
            'nomor_lambung'     => $this->nomor_lambung,
            'kategori'          => AlatBeratKatResource::collection($this->whenLoaded('alat_berat_kat')),
        ];
    }
}
