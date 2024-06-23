<?php

namespace Tests\Feature\Journals;

use App\Client;
use App\Journal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreClientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_the_phone_to_create_a_new_client()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('clients.create'))
            ->assertOk()
            ->assertViewIs('clients.create');
    }

    /** @test */
    public function an_unauthenticated_user_cannot_see_the_form_to_create_a_new_client()
    {
        $this->get(route('clients.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_create_a_new_client()
    {
        $user = factory(User::class)->create();
        $this->assertEmpty(Client::all());

        $this->actingAs($user)
            ->post(route('clients.store'), [
                'name' => 'John Doe',
                'email' => 'test@email.com',
                'phone' => '1234567890',
            ])
            ->assertCreated();

        $this->assertCount(1, Client::all());
        tap(Client::first(), function ($client) use ($user) {
            $this->assertEquals('John Doe', $client->name);
            $this->assertEquals('test@email.com', $client->email);
            $this->assertEquals('1234567890', $client->phone);
            $this->assertEquals($user->id, $client->user_id);
        });
    }

    public function validParams($attributes = []): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'test@email.com',
            'phone' => '1234567890',
            ...$attributes
        ];
    }

    /** @test */
    public function an_unauthenticated_user_cannot_add_a_new_client()
    {
        $this->post(route('clients.store'), $this->validParams())
            ->assertRedirect(route('login'));

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function name_is_required()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams(['name' => '']))
            ->assertSessionHasErrors('name');

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function name_must_be_a_string()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams(['name' => 123]))
            ->assertSessionHasErrors('name');

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function name_must_be_fewer_than_190_characters()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams([
                'name' => str_pad('test', 191, 'a')
            ]))
            ->assertSessionHasErrors('name');

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function either_email_or_phone_are_required()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams([
                'email' => '',
                'phone' => '',
            ]))
            ->assertSessionHasErrors(['email', 'phone']);

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function email_must_be_a_valid_email_address()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams([
                'email' => 'not-an-email',
            ]))
            ->assertSessionHasErrors('email');

        $this->assertEmpty(Client::all());
    }

    /** @test */
    public function phone_must_only_contain_numbers_spaces_and_plus_signs()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('clients.store'), $this->validParams([
                'phone' => 'not-a-phone-number',
            ]))
            ->assertSessionHasErrors('phone');

        $this->assertEmpty(Client::all());
    }
}
