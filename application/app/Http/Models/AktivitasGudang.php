<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasGudang extends Model
{
    protected $table = 'aktivitas_gudang';
    protected $primaryKey = null;
    
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
    
    public $timestamps  = false;
    public $incrementing = false;

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
                        'aktivitas' => 'Mengubah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' pada ' . $attr . ' dari ' . $old . ' menjadi ' . $new,
                        'created_at' => now(),
                        'created_by' => \Auth::id(),
                    ];
                    (new LogActivity)->log($arr);
                }
            }
        });

        static::creating(function ($table) {
            
            $table->created_by = \Auth::id();
            $table->created_at = now();
            // $arr = [
            //     'modul' => ucwords(str_replace('_', ' ', $table->table)),
            //     'action' => 1,
            //     'aktivitas' => 'Mendaftarkan aktivitas ' . $table->aktivitas->nama . ' pada gudang ' . $table->gudang->nama,
            //     'created_at' => now(),
            //     'created_by' => \Auth::id(),
            // ];
            // (new LogActivity)->log($arr);
        });
    }

    public function aktivitas()
    {
        return $this->belongsTo(Aktivitas::class, 'id_aktivitas');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }
}
