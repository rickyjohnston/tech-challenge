<?php

namespace Tests\Feature\Bookings;

use App\Booking;
use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteBookingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_remove_bookings_associated_with_their_own_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);
        $booking = factory(Booking::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->delete(route('bookings.destroy', $booking))
            ->assertOk();
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_booking()
    {
        $client = factory(Client::class)->create();
        $booking = factory(Booking::class)->create(['client_id' => $client->id]);

        $this->delete(route('bookings.destroy', $booking))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    /** @test */
    public function a_user_can_only_delete_bookings_associated_with_their_own_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();
        $booking = factory(Booking::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->delete(route('bookings.destroy', $booking))
            ->assertForbidden();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }
}
