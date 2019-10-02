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
        'password', 'from_date', 'end_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
    ];

    protected $dates = ['deleted_at'];

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
}
