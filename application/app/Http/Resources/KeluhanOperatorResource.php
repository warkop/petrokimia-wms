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
        // $tenaga_kerja = TenagaKerjaNonOrganik::find($this->id_operator);
        // $keluhan = Keluhan::find($this->id_keluhan);
        return [
            'id'            => $this->id,
            'keterangan'    => $this->keterangan,
            'id_operator'   => $this->id_operator,
            // 'operator'      => $tenaga_kerja->nama,
            'id_keluhan'    => $this->id_keluhan,
            // 'keluhan'       => $keluhan->nama,
            'created_at'    => $this->created_at,
            'created_by'    => $this->created_by,
        ];
    }
}
