<?php

use App\Role;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::where('name', 'Super Admin')->first();

        $exist = User::where('email', 'admin@handi.com')->first();
        if (empty($exist)) {
            User::updateOrCreate([
                'uuid' => Str::uuid(),
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@handi.com',
                'username' => 'admin',
                'role' => $admin->name,
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
        }
    }
}
