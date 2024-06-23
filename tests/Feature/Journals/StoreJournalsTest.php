<?php

namespace Tests\Feature\Journals;

use App\Client;
use App\Journal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreJournalsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_create_a_new_journal_entry()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);
        $this->assertEmpty(Journal::all());

        $this->actingAs($user)
            ->post(route('journals.store', $client), [
                'text' => 'This is a journal entry',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertCreated();

        $this->assertCount(1, Journal::all());
        tap(Journal::first(), function ($journal) use ($client) {
            $this->assertEquals('This is a journal entry', $journal->text);
            $this->assertEquals(now()->format('Y-m-d'), $journal->date->format('Y-m-d'));
            $this->assertEquals($client->id, $journal->client_id);
        });
    }

    public function validParams($attributes = []): array
    {
        return [
            'text' => 'This is a journal entry',
            'date' => now()->format('Y-m-d'),
            ...$attributes
        ];
    }

    /** @test */
    public function an_unauthenticated_user_cannot_add_a_new_journal_entry()
    {
        $client = factory(Client::class)->create();

        $this->post(route('journals.store', $client), $this->validParams())
            ->assertRedirect(route('login'));

        $this->assertEmpty(Journal::all());
    }

    /** @test */
    public function a_user_can_only_add_journals_associated_with_their_clients()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();

        $this->actingAs($user)
            ->post(route('journals.store', $client), $this->validParams())
            ->assertForbidden();
    }

    /** @test */
    public function text_is_required()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('journals.store', $client), $this->validParams(['text' => '']))
            ->assertSessionHasErrors('text');

        $this->assertEmpty(Journal::all());
    }

    /** @test */
    public function text_must_be_a_string()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('journals.store', $client), $this->validParams(['text' => 123]))
            ->assertSessionHasErrors('text');

        $this->assertEmpty(Journal::all());
    }

    /** @test */
    public function date_is_required()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('journals.store', $client), $this->validParams(['date' => '']))
            ->assertSessionHasErrors('date');

        $this->assertEmpty(Journal::all());
    }

    /** @test */
    public function date_must_be_a_date()
    {
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('journals.store', $client), $this->validParams(['date' => 'not-a-date']))
            ->assertSessionHasErrors('date');

        $this->assertEmpty(Journal::all());
    }
}
