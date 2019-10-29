<?php

namespace App\Http\Models;

use App\Scopes\EndDateScope;
use Illuminate\Database\Eloquent\Model;
use DB;

class Material extends Model
{
    protected $table = 'material';
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

    public $timestamps  = true;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $table->updated_by = \Auth::id();
            $table->updated_at = now();
        });

        static::creating(function ($table) {
            $table->created_by = \Auth::id();
            $table->created_at = now();
        });

        static::addGlobalScope(new EndDateScope);
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table('material')
            ->select(
                'id AS id', 
                'id_material_sap', 
                'nama AS nama', 
                'kategori',
                'berat',
                'koefisien_pallet',
                DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), 
                DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'
            ));

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
