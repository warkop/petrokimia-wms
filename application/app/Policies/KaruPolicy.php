<?php

namespace App\Policies;

use App\Http\Models\Users;
use App\Http\Models\Karu;
use Illuminate\Auth\Access\HandlesAuthorization;

class KaruPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the karu.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Karu  $karu
     * @return mixed
     */
    public function view(Users $user, Karu $karu)
    {
        return $user->id === $karu->created_by;
    }

    /**
     * Determine whether the user can create karus.
     *
     * @param  \App\Http\Models\Users  $user
     * @return mixed
     */
    public function create(Users $user)
    {
        return $user->id === 1;
    }

    /**
     * Determine whether the user can update the karu.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Karu  $karu
     * @return mixed
     */
    public function update(Users $user, Karu $karu)
    {
        return $user->id === $karu->created_by;
    }

    /**
     * Determine whether the user can delete the karu.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\Karu  $karu
     * @return mixed
     */
    public function delete(Users $user, Karu $karu)
    {
        //
    }
}
