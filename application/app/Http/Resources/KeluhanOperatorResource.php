<?php

namespace App\Http\Resources;

use App\Http\Models\Keluhan;
use App\Http\Models\TenagaKerjaNonOrganik;
use Illuminate\Http\Resources\Json\Resource;

class KeluhanOperatorResource extends Resource
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
            'id'            => $this->id,
            'keterangan'    => $this->keterangan,
            'operator'      => $this->nama_operator,
            'keluhan'       => $this->nama_keluhan,
            'created_at'    => $this->created_at,
            'created_by'    => $this->created_by,
        ];
    }
}
