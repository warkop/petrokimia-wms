<?php

namespace App\Policies;

use App\Http\Models\Users;
use App\Http\Models\Gudang;
use Illuminate\Auth\Access\HandlesAuthorization;

class GudangPolicy
{
    use HandlesAuthorization;


    public function viewPenyangga(Users $user)
    {
        return $user->role_id === 1 || $user->role_id === 6;
    }

    /**
     * Determine whether the user can view the gudang.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Gudang  $gudang
     * @return mixed
     */
    public function view(Users $user, Gudang $gudang)
    {
        //
    }

    /**
     * Determine whether the user can create gudangs.
     *
     * @param  \App\Http\Models\Users  $user
     * @return mixed
     */
    public function create(Users $user)
    {
        //
    }

    /**
     * Determine whether the user can update the gudang.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Gudang  $gudang
     * @return mixed
     */
    public function update(Users $user, Gudang $gudang)
    {
        //
    }

    /**
     * Determine whether the user can delete the gudang.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Gudang  $gudang
     * @return mixed
     */
    public function delete(Users $user, Gudang $gudang)
    {
        //
    }
}
