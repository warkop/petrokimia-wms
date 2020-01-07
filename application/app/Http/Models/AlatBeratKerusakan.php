<?php

namespace App\Http\Models;

use App\Scopes\EndDateScope;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class AlatBeratKerusakan extends Model
{
    protected $table = 'alat_berat_kerusakan';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($table) {
            $table->updated_by = auth()->id();
            $table->updated_at = date('Y-m-d H:i:s');
        });

        static::creating(function ($table) {
            $table->created_by = auth()->id();
            $table->created_at = date('Y-m-d H:i:s');
            $table->start_date = date('Y-m-d H:i:s');
        });

        static::addGlobalScope(new EndDateScope);
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table('alat_berat_kerusakan')
            ->select('id AS id', 'nama AS nama', DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
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
