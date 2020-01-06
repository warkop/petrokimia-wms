<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTrans extends Model
{
    protected $table = 'material_trans';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public $timestamps  = false;

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id');
    }
    public function adjustment()
    {
        return $this->belongsTo(MaterialAdjustment::class, 'id_adjustment', 'id');
    }
    public function realisasiMaterial()
    {
        return $this->belongsTo(RealisasiMaterial::class, 'id_realisasi_material', 'id');
    }
    public function aktivitasHarian()
    {
        return $this->belongsTo(AktivitasHarian::class, 'id_aktivitas_harian', 'id');
    }
    public function gudangStok()
    {
        return $this->belongsTo(GudangStok::class, 'id_gudang_stok', 'id');
    }
}
