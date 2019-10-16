<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AlatBerat extends Model
{
    protected $table = 'alat_berat';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;

    public function getWithRelation($id='')
    {
        $query = DB::table($this->table)
        ->select($this->table.'.id','nama', 'anggaran', 'nomor_lambung', 'nomor_polisi')
        ->leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
        ->whereNull('abk.end_date');

        if (!empty($id)) {
            $query = $query->where('id', $id);
            return $query->first();
        } else {
            return $query->get();
        }
    }
}
