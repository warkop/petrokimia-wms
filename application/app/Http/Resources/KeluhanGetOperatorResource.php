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
            'start_date'    => [
                'date' => date('Y-m-d H:i:s', strtotime($this->tkbm->start_date)),
                'timezone_type' => 3,
                'timezone' => "Asia/Jakarta"
            ],
            'end_date'      => [
                'date' =>  date('Y-m-d H:i:s', strtotime(
                    $this->tkbm->end_date)),
                'timezone_type' => 3,
                'timezone' => "Asia/Jakarta"
            ],
            'nik'           => $this->tkbm->nik,
        ];
    }
}
