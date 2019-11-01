<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $table = 'log_activity';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function log($arr)
    {
        \DB::table('log_activity')->insert(
            $arr
        );
    }
}
