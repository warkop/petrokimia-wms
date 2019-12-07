<?php

namespace App\Http\Models;

use App\Scopes\EndDateScope;
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
        'start_date',
        'end_date',
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
            if (\Auth::id() != null) {
                $table->updated_by = \Auth::id();
            }
        });

        static::creating(function ($table) {
            if (\Auth::id() != null) {
                $table->created_by = \Auth::id();
            }
            // ShiftKerja::whereBetween('mulai', ['']);
            
        });

        static::addGlobalScope(new EndDateScope);
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $user = \Auth::user();
        $result = DB::table('rencana_harian as rh')
            ->select('rh.id', 'id_shift', 'sk.nama', DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\') AS tanggal'))
            ->leftJoin('shift_kerja as sk', 'rh.id_shift', '=', 'sk.id')
            ->where('rh.created_by', $user->id);

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
