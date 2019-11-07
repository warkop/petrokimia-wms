<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\EndDateScope;

class CustomModel extends Model
{
    private function specialColumn($column, $value)
    {
        switch ($column) {
            case 'tipe_gudang':
                if ($value == 1) {
                    return 'Internal';
                } else if ($value == 2) {
                    return 'Eksternal';
                } else {
                    return 'kosong';
                }
                break;
            case 'id_karu':
                if ($value == '') {
                    return 'kosong';
                }
                
                $res = Karu::withoutGlobalScopes()->find($value);
                return $res->nama;
                break;
            default:
                return $value;
                break;
        }
    }

    public function bar()
    {
        return get_class($this);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr => $value) {
                    // $old = $table->getOriginal($attr)??'kosong';
                    // $new = $table->$attr??'kosong';
                    $temp = new CustomModel;
                    $old = $temp->specialColumn($attr, $table->getOriginal($attr));
                    $new = $temp->specialColumn($attr, $table->$attr);

                    $arr = [
                        'modul' => ucwords(str_replace('_', ' ', $table->table)),
                        'action' => 2,
                        'aktivitas' => 'Mengubah data '.ucwords(str_replace('_', ' ', $table->table)).' dengan ID '.$table->id.' pada '.$attr.' dari '.$old.' menjadi '.$new,
                        'created_at' => now(),
                        'created_by' => \Auth::id(),
                    ];
                    (new LogActivity)->log($arr);
                }
            }

            $table->updated_by = \Auth::id();
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
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)).' dengan nama '.($table->nama),
                'created_at' => now(),
                'created_by' => \Auth::id(),
            ];
            (new LogActivity)->log($arr);

            $table->created_by = \Auth::id();
            $table->created_at = now();
        });

        static::addGlobalScope(new EndDateScope);
    }
}
