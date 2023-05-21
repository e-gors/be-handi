<?php

use App\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = ['Hilongos', 'Hindang', 'Matalom', 'Bato', 'Inopacan'];
        foreach ($locations as $location) {
            $data = Location::where('name', $location)->first();
            if (empty($data)) {
                Location::updateOrCreate([
                    'name' => $location
                ]);
            }
        }
    }
}
