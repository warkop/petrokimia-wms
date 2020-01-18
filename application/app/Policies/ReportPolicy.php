<?php

namespace App\Policies;

use App\Http\Models\Users;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
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

    public function index(Users $user)
    {
        return $user->role_id === 5 || $user->role_id === 1;
    }
}
