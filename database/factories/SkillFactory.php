<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Skill;
use Faker\Generator as Faker;

$factory->define(Skill::class, function (Faker $faker) {

   $skillNames = ['PHP', 'JavaScript', 'CSS', 'HTML', 'Python', 'Ruby'];

    return [
        'skills' => $faker->randomElements($skillNames, $count = 3),
    ];
});
