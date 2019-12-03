<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AlatBeratResource extends Resource
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
            // 'data'  => $this->collection,
            'id'                => $this->id,
            'nomor_lambung'     => $this->nomor_lambung,
            'kategori'          => $this->kategori->nama,
        ];
    }
}
