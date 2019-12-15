<?php

namespace App\Policies;

use App\Http\Models\Users;
use App\Http\Models\AktivitasHarian;
use Illuminate\Auth\Access\HandlesAuthorization;

class AktivitasHarianPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the aktivitasHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\App\Http\Models\AktivitasHarian  $aktivitasHarian
     * @return mixed
     */
    public function view(Users $user, AktivitasHarian $aktivitasHarian)
    {
        return auth()->user()->role_id === 3;
    }

    public function reply(User $user)
    {
        if (auth()->user()->role_id < 5) {
            $this->deny('Sorry, your level is not high enough to do that!');
        }
        return true;
    }

    public function store(Users $user, Aktivitas $aktivitas)
    {

        if ($aktivitas->peminjaman != null && $user->role_id === 5) {
            return true;
        }

        if ($user->role === 3)
            return true;
    }

    /**
     * Determine whether the user can create aktivitasHarians.
     *
     * @param  \App\Http\Models\Users  $user
     * @return mixed
     */
    public function create(Users $user)
    {
        //
    }

    /**
     * Determine whether the user can update the aktivitasHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\App\Http\Models\AktivitasHarian  $aktivitasHarian
     * @return mixed
     */
    public function update(Users $user, AktivitasHarian $aktivitasHarian)
    {
        //
    }

    /**
     * Determine whether the user can delete the aktivitasHarian.
     *
     * @param  \App\Http\Models\Users  $user
     * @param  \App\App\Http\Models\AktivitasHarian  $aktivitasHarian
     * @return mixed
     */
    public function delete(Users $user, AktivitasHarian $aktivitasHarian)
    {
        //
    }
}
