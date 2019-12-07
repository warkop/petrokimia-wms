<?php

namespace App\Policies;

use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use Illuminate\Auth\Access\HandlesAuthorization;

class RencanaHarianPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the rencanaHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\RencanaHarian  $rencanaHarian
     * @return mixed
     */
    public function view(Users $user)
    {
        return $user->role_id === 5;
    }

    /**
     * Determine whether the user can create rencanaHarians.
     *
     * @param  \App\Http\Models\Users  $user
     * @return mixed
     */
    public function create(Users $user)
    {
        //
    }

    /**
     * Determine whether the user can update the rencanaHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\RencanaHarian  $rencanaHarian
     * @return mixed
     */
    public function update(Users $user, RencanaHarian $rencanaHarian)
    {
        return $user->role_id === 5 && $rencanaHarian->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the rencanaHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\RencanaHarian  $rencanaHarian
     * @return mixed
     */
    public function delete(Users $user, RencanaHarian $rencanaHarian)
    {
        //
    }
}
