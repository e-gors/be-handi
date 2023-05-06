<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Construction and Building Trades',
                'children' => [
                    'Carpenter',
                    'Bricklayer',
                    'Electrician',
                    'Plumber',
                    'Painter',
                    'Roofing and Siding Installer',
                    'Welder',
                ],
            ],
            [
                'name' => 'Manufacturing and Production',
                'children' => [
                    'Machinist',
                    'Assembler',
                    'Packaging Operator',
                    'Quality Control Inspector',
                    'Production Supervisor',
                    'Forklift Operator',
                    'Assembly Line Worker',
                ],
            ],
            [
                'name' => 'Transportation and Warehousing',
                'children' => [
                    'Truck Driver',
                    'Delivery Driver',
                    'Warehouse Worker',
                    'Material Handler',
                    'Freight Handler',
                    'Shipping and Receiving Clerk',
                ],
            ],
            [
                'name' => 'Mechanics and Repair Technicians',
                'children' => [
                    'Automotive Technician',
                    'Diesel Mechanic',
                    'HVAC Technician',
                    'Appliance Repair Technician',
                    'Aircraft Mechanic',
                    'Industrial Machinery Mechanic',
                    'Small Engine Mechanic',
                ],
            ],
            [
                'name' => 'Agriculture, Forestry, and Fishing',
                'children' => [
                    'Farm Laborer',
                    'Forestry Technician',
                    'Fisherman',
                    'Three Trimmer',
                    'Irrigation Technician',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $parentCategory = Category::updateOrCreate([
                'name' => $category['name'],
            ]);

            foreach ($category['children'] as $childCategory) {
                Category::updateOrCreate([
                    'name' => $childCategory,
                    'parent_id' => $parentCategory->id,
                ]);
            }
        }
    }
}
