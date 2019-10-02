<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class JenisFoto extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_foto';
    protected $primaryKey = 'jenis_foto_id';

    protected $guarded = [
        'jenis_foto_id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps  = false;

    public function jsonGrid($start=0, $length=10, $search = '', $count = false, $sort='asc', $field='jenis_foto_id', $condition)
    {
        $result = DB::table('jenis_foto')
            ->select('jenis_foto_id AS id', 'nama_jenis_foto AS nama', DB::raw('TO_CHAR(from_date, \'dd-mm-yyyy\') AS from_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'))
            ->whereNull('deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('nama_jenis', 'ILIKE', '%' . $search . '%');
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
