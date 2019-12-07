<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LogActivityResource extends Resource
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
            'id'        => $this->id,
            'nama_user' => $this->users->username,
            'aktivitas' => $this->aktivitas,
            'waktu'     => date('d-m-Y (H:i:s)', strtotime($this->created_at)),
        ];
    }
}
