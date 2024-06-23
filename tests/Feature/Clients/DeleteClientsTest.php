<?php

namespace Tests\Feature\Clients;

use App\Client;
use App\Journal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteClientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_delete_their_own_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('clients.destroy', $client))
            ->assertOk();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_client()
    {
        $client = factory(Client::class)->create();

        $this->delete(route('clients.destroy', $client))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    /** @test */
    public function a_user_can_only_delete_their_own_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();

        $this->actingAs($user)
            ->delete(route('clients.destroy', $client))
            ->assertForbidden();

        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }
}
