<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NewSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $skillNames = ['PHP', 'JavaScript', 'CSS', 'HTML', 'Python', 'Ruby'];
        $faker = Factory::Create();

        return [
            'skills' => $faker->randomElements($skillNames, $count = 3),
        ];
    }
}
