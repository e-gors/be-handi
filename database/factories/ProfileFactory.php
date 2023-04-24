<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;
use App\Profile;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'profile_url' => $faker->imageUrl,
        // 'profile_url' => Storage::url('public/avatars/default.jpg'),
        'background' => $faker->text,
        'gender' => $faker->randomElement(['Male', 'Female', 'Others', 'Better not tell']),
        'address' => $faker->address,
    ];
});
