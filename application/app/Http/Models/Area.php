<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
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

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table($this->table)
            ->select('id AS id', 'nama AS nama', 'id_sloc', 'id_plant', DB::raw('
            (IF tipe_gudang = 1  THEN
                \'Internal\';
            ELSE
                \'Eksternal\'
            END IF) AS tipe_gudang'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('id_sloc', 'ILIKE', '%' . $search . '%');
                $where->orWhere('id_plant', 'ILIKE', '%' . $search . '%');
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
