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
                    $old = $table->getOriginal($attr)??'kosong';
                    $new = $table->$attr??'kosong';
                    

                    $arr = [
                        'modul' => $table->table,
                        'action' => 2,
                        'aktivitas' => 'Mengubah '.$table->table.' '.$attr.' dari '.$old.' menjadi '.$new,
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
                'modul' => $table->table,
                'action' => 1,
                'aktivitas' => 'Menambah data ' . $table->table,
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
