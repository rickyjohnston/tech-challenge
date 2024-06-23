<?php

namespace Tests\Feature\Journals;

use App\Client;
use App\Journal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteJournalsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_delete_their_own_journals()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);
        $journal = factory(Journal::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->delete(route('journals.destroy', [$client, $journal]))
            ->assertOk();

        $this->assertDatabaseMissing('journals', ['id' => $journal->id]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_journal()
    {
        $client = factory(Client::class)->create();
        $journal = factory(Journal::class)->create(['client_id' => $client->id]);

        $this->delete(route('journals.destroy', [$client, $journal]))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('journals', ['id' => $journal->id]);
    }

    /** @test */
    public function a_user_can_only_delete_journals_associated_with_their_own_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();
        $journal = factory(Journal::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->delete(route('journals.destroy', [$client, $journal]))
            ->assertForbidden();

        $this->assertDatabaseHas('journals', ['id' => $journal->id]);
    }
}
