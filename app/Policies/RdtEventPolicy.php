<?php

namespace App\Policies;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RdtEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Entities\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('list-events');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Entities\User  $user
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return mixed
     */
    public function view(User $user, RdtEvent $rdtEvent)
    {
        return $user->hasPermission('view-events');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Entities\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-events');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Entities\User  $user
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return mixed
     */
    public function update(User $user, RdtEvent $rdtEvent)
    {
        return $user->hasPermission('edit-events');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Entities\User  $user
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return mixed
     */
    public function delete(User $user, RdtEvent $rdtEvent)
    {
        return $user->hasPermission('delete-events');
    }
}
