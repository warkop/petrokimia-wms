<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class KeluhanGetOperatorResource extends Resource
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
            'id'            => $this->tkbm->id,
            'job_desk_id'   => $this->tkbm->job_desk_id,
            'nama'          => $this->tkbm->nama,
            'nomor_hp'      => $this->tkbm->nomor_hp,
            'nomor_bpjs'    => $this->tkbm->nomor_bpjs,
            'start_date'    => date('Y-m-d', strtotime($this->tkbm->start_date)),
            'end_date'      => $this->tkbm->end_date,
            'nik'           => $this->tkbm->nik,
        ];
    }
}
