<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTrans extends Model
{
    protected $table = 'material_trans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_material',
        'id_adjustment',
        'tanggal',
        'tipe',
        'jumlah',
        'alasan',
        'id_realisasi_material',
        'id_aktivitas_harian',
        'status_pallet',
        'status_produk',
        'id_gudang_stok',
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
                        'created_by' => \Auth::id(),
                    ];
                    (new LogActivity)->log($arr);
                }
            }
        });

        static::creating(function ($table) {
            $arr = [
                'modul' => ucwords(str_replace('_', ' ', $table->table)),
                'action' => 1,
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan tipe ' . ($table->tipe==1?' mengurangi':'menambah').' yang berjumlah '.($table->jumlah),
                'created_at' => now(),
                'created_by' => \Auth::id(),
            ];
            (new LogActivity)->log($arr);
        });
    }

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
