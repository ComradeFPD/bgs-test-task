<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Activity::class, function (Faker $faker) {
    return [
        'title' => $faker->company,
        'start_at' => $faker->dateTime
    ];
});
