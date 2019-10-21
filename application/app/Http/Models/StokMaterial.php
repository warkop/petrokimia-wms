<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class StokMaterial extends Model
{
    protected $table = 'stok_material_gudang';
    protected $primaryKey = null;
    
    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
    
    
    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];
    
    public $timestamps  = false;
    public $incrementing = false;

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'shift_kerja_id', $condition)
    {
        $result = DB::table('shift_kerja')
            ->select('shift_kerja_id AS id', 'nama_shift AS nama', DB::raw('TO_CHAR(mulai_shift, \'HH24:MI\') AS mulai'), DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'))
            ->whereNull('deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama_shift)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
