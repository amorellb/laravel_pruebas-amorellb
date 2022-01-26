<?php

namespace App\Policies;

use App\Models\Contacts;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactsPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->role === 'user' || $user->role === 'visitor';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Contacts $contacts
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Contacts $contacts)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user' && $user->id === $contacts->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role === $user;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Contacts $contacts
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Contacts $contacts)
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Contacts $contacts
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Contacts $contacts)
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Contacts $contacts
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Contacts $contacts)
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Contacts $contacts
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Contacts $contacts)
    {
        return $user->role === 'admin';
    }
}
