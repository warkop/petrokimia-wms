<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RencanaHarian extends Model
{
    protected $table = 'rencana_harian';
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
        $result = DB::table('rencana_harian as rh')
            ->select('rh.id', 'id_shift', 'sk.nama', DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\') AS tanggal'))
            ->leftJoin('shift_kerja as sk', 'rh.id_shift', '=', 'sk.id');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->where('sk.nama', 'ILIKE', '%' . strtolower($search) . '%');
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
