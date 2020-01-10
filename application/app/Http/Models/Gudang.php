<?php

namespace App\Http\Models;

use App\Scopes\EndDateScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class Gudang extends Model
{
    use Notifiable;

    protected $table = 'gudang';
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
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr => $value) {
                    $old = (new CustomModel)->specialColumn($attr, $table->getOriginal($attr));
                    $new = (new CustomModel)->specialColumn($attr, $table->$attr);

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

        static::saving(function ($table) {
            if ($table->start_date == null) {
                $table->start_date = now();
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

            $table->created_by = auth()->id();
            $table->created_at = now();
        });

        static::addGlobalScope(new EndDateScope);
    }

    public function karu()
    {
        return $this->belongsTo(Karu::class, 'id_karu');
    }

    public function scopeGp($query)
    {
        return $query->where('tipe_gudang', 2);
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $user = auth()->user();
        $result = DB::table($this->table)
            ->select('id AS id', 'nama AS nama', 'tipe_gudang', 'id_sloc', 'id_plant', DB::raw('
            (SELECT
                sum(stok_min)
            FROM
                stok_material_gudang
            WHERE
                stok_material_gudang.id_gudang = gudang.id
            )as jumlah'));

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('id_sloc', 'ILIKE', '%' . $search . '%');
                $where->orWhere('id_plant', 'ILIKE', '%' . $search . '%');
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
