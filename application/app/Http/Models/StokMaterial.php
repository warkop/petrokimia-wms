<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StokMaterial extends Model
{
    protected $table = 'stok_material_gudang';
    protected $primaryKey = null;
    
    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
    
    
    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];
    
    public $timestamps  = false;
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr => $value) {
                    $old = $table->getOriginal($attr)??'kosong';
                    $new = $table->$attr??'kosong';

                    $arr = [
                        'modul' => ucwords(str_replace('_', ' ', $table->table)),
                        'action' => 2,
                        'aktivitas' => 'Mengubah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan ID ' . $table->id . ' pada ' . $attr . ' dari ' . $old . ' menjadi ' . $new,
                        'created_at' => now(),
                        'created_by' => auth()->id(),
                    ];
                    (new LogActivity)->log($arr);
                }
            }
        });

        static::creating(function ($table) {
            $arr = [
                'modul' => ucwords(str_replace('_', ' ', $table->table)),
                'action' => 1,
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan nama ' . ($table->nama),
                'created_at' => now(),
                'created_by' => auth()->id(),
            ];
            (new LogActivity)->log($arr);
        });
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'shift_kerja_id', $condition)
    {
        $result = DB::table('shift_kerja')
            ->select('shift_kerja_id AS id', 'nama_shift AS nama', DB::raw('TO_CHAR(mulai_shift, \'HH24:MI\') AS mulai'), DB::raw('TO_CHAR(start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(end_date, \'dd-mm-yyyy\') AS end_date'))
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
