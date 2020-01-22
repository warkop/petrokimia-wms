<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MaterialAdjustment extends Model
{
    protected $table = 'material_adjustment';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr) {
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

            $table->updated_by = auth()->id();
            $table->updated_at = now();
        });

        static::creating(function ($table) {
            $arr = [
                'modul' => ucwords(str_replace('_', ' ', $table->table)),
                'action' => 1,
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan tanggal ' . date('d-m-Y', strtotime($table->tanggal)),
                'created_at' => now(),
                'created_by' => auth()->id(),
            ];
            (new LogActivity)->log($arr);

            $table->created_by = auth()->id();
            $table->created_at = now();
        });
    }

    public function materialTrans()
    {
        return $this->hasMany(MaterialTrans::class, 'id_adjustment', 'id');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition, $id_gudang)
    {
        $result = DB::table($this->table)
            ->select('id', 'foto', DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\') AS tanggal', 'alasan'))
            ->where('id_gudang', $id_gudang);

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->orWhere(DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
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
