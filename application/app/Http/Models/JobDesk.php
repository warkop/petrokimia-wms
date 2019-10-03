<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class JobDesk extends Model
{
    use SoftDeletes;

    protected $table = 'job_desk';
    protected $primaryKey = 'job_desk_id';

    protected $guarded = [
        'job_desk_id',
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

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'job_desk_id', $condition)
    {
        $result = DB::table('job_desk')
            ->select('job_desk_id AS id', 'job_desk AS nama', DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'))
            ->whereNull('deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(job_desk)'), 'ILIKE', '%' . strtolower($search) . '%');
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
