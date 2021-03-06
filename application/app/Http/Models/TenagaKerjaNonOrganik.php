<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;

class TenagaKerjaNonOrganik extends CustomModel
{
    protected $table = 'tenaga_kerja_non_organik';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
        'start_date',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at',];

    public $timestamps  = true;

    public function jobDesk()
    {
        return $this->hasOne('App\Http\Models\JobDesk');
    }

    public function scopeHouseKeeper($query)
    {
        return $query->where('job_desk_id', 4);
    }

    public function scopeChecker($query)
    {
        return $query->where('job_desk_id', 3);
    }
    
    public function scopeOperatorAlatBerat($query)
    {
        return $query->where('job_desk_id', 2);
    }
    
    public function scopeAdminLoket($query)
    {
        return $query->where('job_desk_id', 1);
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table('tenaga_kerja_non_organik as tkno')
            ->select('tkno.id AS id', 'jd.nama AS pekerjaan','tkno.nama AS nama', 'nomor_hp AS no_hp', DB::raw('TO_CHAR(tkno.start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(tkno.end_date, \'dd-mm-yyyy\') AS end_date'))
            ->leftJoin('job_desk as jd', 'jd.id', '=', 'tkno.job_desk_id');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(tkno.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(jd.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
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
