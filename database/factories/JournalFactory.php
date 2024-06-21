<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Journal;
use Faker\Generator as Faker;

$factory->define(Journal::class, function (Faker $faker) {
    return [
        'client_id' => factory(Client::class),
        'date' => $faker->date(),
        'text' => $faker->paragraph(),
    ];
});
