<?php

namespace App\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Users extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $guarded = [
        'user_id',
    ];

    protected $hidden = [
        'password', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
    ];

    protected $dateFormat = 'd-m-Y';

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public static function get_auth($username = false)
    {
        if ($username == false) {
            return false;
        }

        $result = DB::table("user")
            ->select(DB::raw('user_id AS id_user, name AS nama, password AS password, username AS username, role_id AS role, token AS token_permission'))
            ->whereNull('deleted_at');

        $result = $result->where('username', $username)->first();

        return $result;
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'user_id', $condition)
    {
        $result = DB::table('users as u')
            ->select('user_id AS id', 'email', 'role_name', 'username AS nama', DB::raw('TO_CHAR(u.start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(u.end_date, \'dd-mm-yyyy\') AS end_date'))
            ->leftJoin('role as r', 'u.role_id', '=', 'r.role_id')
            ->whereNull('u.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(username)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(email)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(role_name)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('TO_CHAR(u.start_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(DB::raw('TO_CHAR(u.end_date, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
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
