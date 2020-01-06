<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PemetaanSloc extends Model
{
    protected $table = 'pemetaan_sloc';
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

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = true;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr) {
                    $old = $table->getOriginal($attr) ?? 'kosong';
                    $new = $table->$attr ?? 'kosong';


                    $arr = [
                        'modul' => ucwords(str_replace('_', ' ', $table->table)),
                        'action' => 2,
                        'aktivitas' => 'Mengubah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan ID ' . $table->id . ' pada ' . $attr . ' dari ' . $old . ' menjadi ' . $new,
                        'created_at' => now(),
                        'created_by' => auth()->id(),
                    ];
                    (new LogActivity)->log($arr);
                }

                $table->updated_by = auth()->id();
            }
        });

        static::creating(function ($table) {
            $arr = [
                'modul' => ucwords(str_replace('_', ' ', $table->table)),
                'action' => 1,
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan tipe ' . ($table->tipe == 1 ? ' mengurangi' : 'menambah') . ' yang berjumlah ' . ($table->jumlah),
                'created_at' => now(),
                'created_by' => auth()->id(),
            ];
            (new LogActivity)->log($arr);

            $table->created_by = auth()->id();
        });
    }

    public function detailPemetaanSloc()
    {
        return $this->hasMany(DetailPemetaanSloc::class, 'id_pemetaan_sloc');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table($this->table)
            ->select('id', 'nama', DB::raw('TO_CHAR(created_at, \'dd-mm-yyyy\') AS created_at'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('TO_CHAR(created_at, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
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
