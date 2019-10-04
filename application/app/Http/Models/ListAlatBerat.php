<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class ListAlatBerat extends Model
{
    use SoftDeletes;

    protected $table = 'list_alat_berat';
    protected $primaryKey = 'list_alat_berat_id';

    protected $guarded = [
        'list_alat_berat_id',
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

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'alat_berat_id', $condition, $id_kategori)
    {
        $result = DB::table('list_alat_berat')
            ->select('list_alat_berat_id AS id', 'nomor_lambung', 'nomor_polisi', 'status')
            ->whereNull('deleted_at')
            ->where('kategori_alat_berat_id', $id_kategori);

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(nomor_polisi)'), 'ILIKE', '%' . strtolower($search) . '%');
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
