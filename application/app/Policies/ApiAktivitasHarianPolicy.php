<?php

namespace App\Policies;

use App\Http\Models\AktivitasHarian;
use App\Http\Models\Users;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiAktivitasHarianPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    
    public function view(Users $user, AktivitasHarian $aktivitasHarian)
    {
        return auth()->user()->role_id === 5 || auth()->user()->role_id === 3;
    }
}
