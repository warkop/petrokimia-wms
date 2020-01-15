<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;

class Karu extends CustomModel
{
    protected $table = 'karu';
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

    public function jsonGrid($start = 0, $length = 10, $search = '', $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table($this->table)
            ->select('id AS id', 'nama AS nama', 'nik', 'no_hp', DB::raw('(select nama from gudang where id = id_gudang) as nama_gudang'),DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('no_hp', 'ILIKE', '%' . $search . '%');
                $where->orWhere('nik', 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
            });
        }

        $count      = $result->count();
        $result     = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();

        return ['result' => $result, 'count' => $count];
    }
}
