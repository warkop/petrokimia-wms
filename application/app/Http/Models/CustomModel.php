<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\EndDateScope;

class CustomModel extends Model
{
    

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr => $value) {
                    // $old = $table->getOriginal($attr)??'kosong';
                    // $new = $table->$attr??'kosong';
                    $old = (new CustomModel)->specialColumn($attr, $table->getOriginal($attr));
                    $new = (new CustomModel)->specialColumn($attr, $table->$attr);
                    $text = '';

                    if (!is_array($new))
                        $text = 'Mengubah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' dengan ID ' . $table->id . ' pada ' . $attr . ' dari ' . $old . ' menjadi ' . $new;

                    $arr = [
                        'modul' => ucwords(str_replace('_', ' ', $table->table)),
                        'action' => 2,
                        'aktivitas' => $text,
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

    public function specialColumn($column, $value)
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
            case 'id_gudang':
                if ($value == '') {
                    return 'kosong';
                }

                $res = Gudang::withoutGlobalScopes()->find($value);
                return $res->nama;
                break;
            case 'id_material':
                if ($value == '') {
                    return 'kosong';
                }

                $res = Material::withoutGlobalScopes()->find($value);
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
}
