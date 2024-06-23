<?php

namespace Tests\Feature\Journals;

use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewJournalsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_retrieve_a_listing_of_associated_journals()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('journals.index', $client))
            ->assertOk();
    }

    /** @test */
    public function an_unauthenticated_user_cannot_retrieve_a_listing_of_associated_journals()
    {
        $client = factory(Client::class)->create();

        $this->get(route('journals.index', $client))
            ->assertRedirect(route('login'));
}

    /** @test */
    public function a_user_can_only_retrieve_a_listing_of_journals_associated_with_their_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();

        $this->actingAs($user)
            ->get(route('journals.index', $client))
            ->assertForbidden();
    }
}
