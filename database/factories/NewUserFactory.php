<?php

namespace Database\Factories;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewUserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;

        return [
            'uuid' => Str::uuid(),
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'username' => $faker->unique()->userName,
            'password' => Hash::make('password'),
            'role' => $faker->randomElement(['Client', 'Worker']),
            'contact_number' => $faker->phoneNumber,
        ];
    }
}
