<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlatBerat extends Model
{
    protected $table = 'alat_berat';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        // 'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $table->updated_by = auth()->id();
            $table->updated_at = now();
        });

        static::creating(function ($table) {
            $table->created_by = auth()->id();
            $table->created_at = now();
        });
    }
    
    public function kategori()
    {
        return $this->belongsTo('App\Http\Models\KategoriAlatBerat', 'id_kategori');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition, $id_kategori)
    {
        $result = DB::table('alat_berat')
            ->select('id AS id', 'nomor_lambung', 'nomor_polisi', 'status')
            ->where('id_kategori', $id_kategori);

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

    public function getWithRelation($id='')
    {
        $query = DB::table($this->table)
        ->select($this->table.'.id','nama', 'nomor_lambung', 'nomor_polisi')
        ->leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
        ->whereNull('abk.end_date');

        if (!empty($id)) {
            $query = $query->where('id', $id);
            return $query->first();
        } else {
            return $query->get();
        }
    }
}
