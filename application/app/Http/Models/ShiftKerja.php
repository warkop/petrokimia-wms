<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class ShiftKerja extends Model
{
    use SoftDeletes;

    protected $table = 'shift_kerja';
    protected $primaryKey = 'shift_kerja_id';

    protected $guarded = [
        'shift_kerja_id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $dateFormat = 'd-m-Y';

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'];

    public $timestamps  = false;

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'shift_kerja_id', $condition)
    {
        $result = DB::table('shift_kerja')
            ->select('shift_kerja_id AS id', 'nama_shift AS nama', 'mulai_shift AS mulai', DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'))
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
