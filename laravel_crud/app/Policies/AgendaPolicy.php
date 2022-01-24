<?php

namespace App\Policies;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AgendaPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->role === 'super') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->role === 'admin' || $user->role === 'user' || $user->role === 'visitor';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param \App\Models\Agenda $agenda
     * @return Response|bool
     */
    public function view(User $user, Agenda $agenda)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user' && $user->id === $agenda->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user';
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param \App\Models\Agenda $agenda
     * @return Response|bool
     */
    public function update(User $user, Agenda $agenda)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user' && $user->id === $agenda->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param \App\Models\Agenda $agenda
     * @return Response|bool
     */
    public function delete(User $user, Agenda $agenda)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user' && $user->id === $agenda->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param \App\Models\Agenda $agenda
     * @return Response|bool
     */
    public function restore(User $user, Agenda $agenda)
    {
        if ($user->role === 'admin') {
            return true;
        } else {
            return $user->role === 'user' && $user->id === $agenda->user_id;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param \App\Models\Agenda $agenda
     * @return Response|bool
     */
    public function forceDelete(User $user, Agenda $agenda)
    {
        return $user->role === 'admin';
    }
}
