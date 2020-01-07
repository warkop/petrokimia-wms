<?php

namespace App\Policies;

use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use Illuminate\Auth\Access\HandlesAuthorization;

class RencanaKerjaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the rencanaHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\RencanaHarian  $rencanaHarian
     * @return mixed
     */
    public function view(Users $user, RencanaHarian $rencanaHarian)
    {
        //
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
        //
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
