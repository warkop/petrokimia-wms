<?php

namespace App\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Scopes\EndDateScope;

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

    protected $dates = ['start_date', 'end_date'];

    public $timestamps  = true;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $changes = $table->isDirty() ? $table->getDirty() : false;

            if ($changes) {
                foreach ($changes as $attr => $value) {
                    $old = $table->getOriginal($attr) ?? 'kosong';
                    $new = $table->$attr ?? 'kosong';


                    $arr = [
                        'modul' => ucwords(str_replace('_', ' ', $table->table)),
                        'action' => 2,
                        'aktivitas' => 'Mengubah data ' . ucwords(str_replace('_', ' ', $table->table)) . ' ' . $attr . ' dari ' . $old . ' menjadi ' . $new,
                        'created_at' => now(),
                        'created_by' => auth()->id(),
                    ];
                    (new LogActivity)->log($arr);
                }
            }

            $table->updated_by = auth()->id();
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
                'aktivitas' => 'Menambah data ' . ucwords(str_replace('_', ' ', $table->table)).' dengan username '.$table->username,
                'created_at' => now(),
                'created_by' => auth()->id(),
            ];
            (new LogActivity)->log($arr);
            
            $table->created_by = auth()->id();
            $table->created_at = now();
        });

        static::addGlobalScope(new EndDateScope);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function scopeEndDate($query)
    {
        return $query->where('end_date', null)->orWhere('end_date', '>', date('Y-m-d'));
    }

    public function scopeIsKaru($query)
    {
        return $query->where('role_id', 5);
    }

    public function scopeIsChecker($query)
    {
        return $query->where('role_id', 3);
    }

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
            ->select('u.id AS id', 'email', 'id_karu', 'id_tkbm', 'r.nama AS role_name', 'name', 'tk.nama as nama_tk', 'k.nama as nama_karu','username AS nama', DB::raw('TO_CHAR(u.start_date, \'dd-mm-yyyy\') AS start_date'), DB::raw('TO_CHAR(u.end_date, \'dd-mm-yyyy\') AS end_date'))
            ->leftJoin('role as r', 'u.role_id', '=', 'r.id')
            ->leftJoin('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'u.id_tkbm')
            ->leftJoin('karu as k', 'k.id', '=', 'u.id_karu')
            ;

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

    public function getByAccessToken($access_token=false)
    {
        if ($access_token == false) {
            return false;
        }

        $result = DB::table(DB::raw('"users" usr'))
            ->select(DB::raw('id AS id_user, name, username AS username, email, role_id AS role, api_token AS access_token, id_karu, id_tkbm'));

        $result = $result->where('api_token', $access_token);

        $result = $result->first();

        return $result;
    }

    public function karu()
    {
        return $this->belongsTo(Karu::class, 'id_karu');
    }

    public function checker()
    {
        return $this->belongsTo(TenagaKerjaNonOrganik::class, 'id_tkbm');
    }
}
