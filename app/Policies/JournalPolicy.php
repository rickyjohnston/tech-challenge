<?php

namespace App\Policies;

use App\Client;
use App\Journal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @param  \App\Journal  $journal
     * @param  \App\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Journal $journal, Client $client)
    {
        return $client->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Journal  $journal
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Journal $journal)
    {
        return $journal->client->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @param  \App\Journal  $journal
     * @param  \App\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Journal $journal, Client $client)
    {
        return $client->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Journal  $journal
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Journal $journal)
    {
        return $journal->client->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Journal  $journal
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Journal $journal)
    {
        return $journal->client->user_id === $user->id;
    }
}
