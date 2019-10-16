<?php

namespace App\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Users extends Authenticatable
{
    use Notifiable;

    protected $connection = 'pgsql';
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

    // protected $dateFormat = 'U';
    protected $casts = [
        'start_date' => 'date',
    ];

    protected $dates = ['start_date', 'end_date'];

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
            ->select(DB::raw('id AS id_user, name AS nama, password AS password, username AS username, role_id AS role, token AS token_permission'));

        $result = $result->where('username', $username)->first();

        return $result;
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table('users as u')
            ->select('u.id AS id', 'email', 'id_karu', 'id_tkbm', 'r.nama AS role_name', 'name','username AS nama', DB::raw('TO_CHAR(u.start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(u.end_date, \'dd-mm-yyyy\') AS end_date'))
            ->leftJoin('role as r', 'u.role_id', '=', 'r.id');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(username)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(email)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(r.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
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
