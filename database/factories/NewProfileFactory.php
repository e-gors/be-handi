<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NewProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;

        return [
            'profile_url' => $faker->imageUrl,
            // 'profile_url' => Storage::url('public/avatars/default.jpg'),
            'background' => $faker->text,
            'gender' => $faker->randomElement(['Male', 'Female', 'Others', 'Better not tell']),
            'address' => $faker->address,
        ];
    }
}
