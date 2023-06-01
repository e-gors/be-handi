<?php

use App\Post;
use App\User;
use App\Skill;
use App\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Retrieve users with the role "client"
        $users = User::where('role', 'client')->get();

        // Retrieve the categories
        $categories = Category::whereNull('parent_id')->get();

        // Create job posts for each client user
        foreach ($users as $user) {
            $uuid = Str::uuid();
            $category = $categories->random();

            // Retrieve the child skills based on the category name
            $categorySkills = Skill::whereHas('parent', function ($query) use ($category) {
                $query->where('name', $category->name);
            })->pluck('name')->toArray();

            if (count($categorySkills) > 0) {
                $randomSkills = $faker->randomElements($categorySkills, $faker->numberBetween(2, min(4, count($categorySkills))));
            } else {
                $randomSkills = [];
            }

            $position = Category::where('parent_id', $category->id)->inRandomOrder()->first();
            $locations = $faker->randomElements(['Inopacan', 'Hindang', 'Hilongos', 'Bato', 'Matalom'], $faker->numberBetween(1, 3));

            $descriptionParagraphs = $faker->paragraphs($faker->numberBetween(5, 20));
            $description = implode("<br><br>", $descriptionParagraphs);

            $newPost = Post::create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'title' => $faker->sentence,
                'description' => $description,
                'skills' => serialize($randomSkills),
                'category' => $category->name,
                'position' => $position->name,
                'job_type' => $faker->randomElement(['Daily Rate', 'Fixed Budget']),
                'locations' => serialize($locations),
                'post_url' => env('APP_BASE_URL') . $uuid . Carbon::now(),
                'visibility' => 'Public'
            ]);

            // Determine rate, budget, and days based on the job type
            if ($newPost->job_type === 'Daily Rate') {
                $newPost->days = $faker->randomNumber(2);
                $newPost->rate = $faker->randomFloat(2, 10, 50);
                $newPost->budget = null;
            } else {
                $newPost->days = null;
                $newPost->rate = null;
                $newPost->budget = $faker->randomFloat(2, 100, 1000);
            }

            $newPost->save();
        }
    }
}
