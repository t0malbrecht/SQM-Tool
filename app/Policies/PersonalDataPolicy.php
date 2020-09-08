<?php

namespace App\Policies;

use App\PersonalData;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonalDataPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\PersonalData  $personalData
     * @return mixed
     */
    public function view(User $user, PersonalData $personalData)
    {
        return auth()->user()->id == $personalData->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\PersonalData  $personalData
     * @return mixed
     */
    public function update(User $user, PersonalData $personalData)
    {
        return auth()->user()->id == $personalData->user_id;
    }


}
