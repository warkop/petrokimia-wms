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

    public $timestamps  = false;

    public function log($arr)
    {
        \DB::table('log_activity')->insert(
            $arr
        );
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'created_by', 'id');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = LogActivity::
            select(
                'log_activity.id',
                'modul',
                'aktivitas',
                'log_activity.created_at',
                'username'
            )
            ->join('users', 'users.id', '=', 'log_activity.created_by');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(aktivitas)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('TO_CHAR(log_activity.created_at, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
