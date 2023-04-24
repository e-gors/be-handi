<?php

use App\User;
use App\Skill;
use App\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class, 20)->create();

        // Create 20 users with corresponding profiles
        $profiles = $users->map(function ($user) {
            return factory(Profile::class)->create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);
            return factory(Skill::class)->create([
                'user_id' => $user->id,
            ]);
        });

        // Output a success message
        $this->command->info('Profiles and users seeded successfully!');
    }
}
