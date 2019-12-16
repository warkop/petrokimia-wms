<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public $timestamps  = false;

    public function notifiable()
    {
        return $this->morphTo();
    }
}
