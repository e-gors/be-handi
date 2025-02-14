<?php

namespace Database\Seeders;

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
        $role = Role::where('name', 'Super Admin')->first();

        $exist = User::where('email', 'admin@gmail.com')->first();
        if (empty($exist)) {
            User::updateOrCreate([
                'uuid' => Str::uuid(),
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'role' => $role->name,
                'password' => Hash::make('trabahante'),
                'email_verified_at' => now()
            ]);
        }
    }
}
