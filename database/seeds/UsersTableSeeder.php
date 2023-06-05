<?php

use App\User;
use App\Skill;
use App\Profile;
use App\Category;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create 1000 workers with corresponding profiles
        $workers = factory(User::class, 1000)->create(['role' => 'Worker'])->each(function ($user) use ($faker) {
            Profile::create([
                'user_id' => $user->id,
                'profile_link' => env('APP_BASE_URL') . "worker/profile/overview/" . $user->uuid,
                'background' => $faker->paragraph,
                'profile_url' => $faker->imageUrl,
                'background_url' => $faker->imageUrl,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'address' => $faker->randomElement(['Inopacan', 'Hilongos', 'Hindang', 'Matalom', 'Bato']),
                'rate' => $faker->randomFloat(2, 10, 50),
                'availability' => $faker->randomElement(['available', 'unavailable']),
                'facebook_url' => $faker->url,
                'instagram_url' => $faker->url,
                'twitter_url' => $faker->url,
            ]);

            $this->attachCategoriesToUser($user);
            $this->attachSkillsToUser($user);
        });

        // Create 1000 clients with corresponding profiles
        $clients = factory(User::class, 1000)->create(['role' => 'Client'])->each(function ($user) use ($faker) {
            Profile::create([
                'user_id' => $user->id,
                'profile_link' => env('APP_BASE_URL') . "client/profile/overview/" . $user->uuid,
                'background' => $faker->paragraph,
                'profile_url' => $faker->imageUrl,
                'background_url' => $faker->imageUrl,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'address' => $faker->randomElement(['Inopacan', 'Hilongos', 'Hindang', 'Matalom', 'Bato']),
                'facebook_url' => $faker->url,
                'instagram_url' => $faker->url,
                'twitter_url' => $faker->url,
            ]);
        });

        // Output a success message
        $this->command->info('Profiles and users seeded successfully!');
    }

    private function attachCategoriesToUser($user)
    {
        $categoryIds = Category::inRandomOrder()->limit(rand(2, 3))->pluck('id')->toArray();
        $user->categories()->attach($categoryIds);
    }

    private function attachSkillsToUser($user)
    {
        $skillIds = Skill::inRandomOrder()->limit(rand(2, 5))->pluck('id')->toArray();
        $user->skills()->attach($skillIds);
    }
}