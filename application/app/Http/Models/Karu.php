<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Karu extends Model
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

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table('karu')
            ->select('id AS id', 'nama AS nama', 'no_hp', DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('no_hp', 'ILIKE', '%' . $search . '%');
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
