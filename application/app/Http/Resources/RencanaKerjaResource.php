<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RencanaKerjaResource extends Resource
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
            'id_shift' => $this->id_shift,
            'tanggal' => $this->tanggal,
            'status' => $this->id,
        ];
    }
}
