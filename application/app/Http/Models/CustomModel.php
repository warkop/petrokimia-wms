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
            $table->updated_by = \Auth::id();
            $table->updated_at = now();

            if ($table->start_date == null) {
                $table->start_date = now();
            }
        });

        static::creating(function ($table) {
            $table->created_by = \Auth::id();
            $table->start_date = now();
            $table->created_at = now();
        });

        static::addGlobalScope(new EndDateScope);
    }
}
