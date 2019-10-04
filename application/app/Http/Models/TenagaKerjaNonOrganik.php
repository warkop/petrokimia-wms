<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class TenagaKerjaNonOrganik extends Model
{
    use SoftDeletes;

    protected $table = 'tenaga_kerja_non_organik';
    protected $primaryKey = 'tenaga_kerja_non_organik_id';

    protected $guarded = [
        'tenaga_kerja_non_organik_id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'];

    public $timestamps  = false;

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'tenaga_kerja_non_organik_id', $condition)
    {
        $result = DB::table('tenaga_kerja_non_organik as tkno')
            ->select('tenaga_kerja_non_organik_id AS id', 'job_desk.job_desk AS pekerjaan','nama_tenaga_kerja AS nama', 'nomor_hp AS no_hp', DB::raw('TO_CHAR(tkno.start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(tkno.end_date, \'dd-mm-yyyy\') AS end_date'))
            ->leftJoin('job_desk', 'job_desk.job_desk_id', '=', 'tkno.job_desk_id')
            ->whereNull('tkno.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama_tenaga_kerja)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(job_desk)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('nomor_hp', 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(tkno.start_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(tkno.end_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
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
