<?php

namespace Tests\Feature\Clients;

use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewClientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_a_listing_of_their_clients()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('clients.index'))
            ->assertOk()
            ->assertViewIs('clients.index')
            ->assertViewHas('clients');
    }

    /** @test */
    public function an_unauthenticated_user_cannot_view_a_listing_of_clients()
    {
        $this->get(route('clients.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function a_user_can_only_view_their_own_clients()
    {
        $user = factory(User::class)->create();
        $otherUsersClient = factory(Client::class)->create();

        $response = $this->actingAs($user)
            ->get(route('clients.index'))
            ->assertOk();

        $response->original
            ->getData()['clients']
            ->assertNotContains($otherUsersClient);
    }

    /** @test */
    public function a_user_can_view_a_single_client()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('clients.show', $client))
            ->assertOk()
            ->assertViewIs('clients.show')
            ->assertViewHas('client', $client);
    }

    /** @test */
    public function a_user_cannot_view_a_client_that_does_not_belong_to_them()
    {
        $user = factory(User::class)->create();
        $otherUsersClient = factory(Client::class)->create();

        $this->actingAs($user)
            ->get(route('clients.show', $otherUsersClient))
            ->assertForbidden();
    }

    /** @test */
    public function a_guest_cannot_view_a_single_client()
    {
        $client = factory(Client::class)->create();

        $this->get(route('clients.show', $client))
            ->assertRedirect(route('login'));
    }
}
