<?php

namespace Database\Seeders;

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Super Admin', 'Admin', 'Worker', 'Client', 'Technical Support'];
        foreach ($roles as $role) {
            $data = Role::where('name', $role)->first();
            if (empty($data)) {
                Role::updateOrCreate([
                    'name' => $role
                ]);
            }
        }
    }
}
