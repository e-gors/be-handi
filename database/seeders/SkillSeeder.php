<?php

namespace Database\Seeders;

use App\Skill;
use App\Category;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            [
                'name' => 'Construction and Building Trades',
                'children' => [
                    'Building codes knowledge',
                    'Tool familiarity',
                    'Spatial awareness',
                    'Communication',
                    'Teamwork'
                ],
            ],
            [
                'name' => 'Manufacturing and Production',
                'children' => [
                    'Understanding of processes',
                    'Technical reading',
                    'Hand-eye coordination',
                    'Problem-solving',
                    'Troubleshooting'
                ],
            ],
            [
                'name' => 'Transportation and Warehousing',
                'children' => [
                    'Driving proficiency',
                    'Vehicle operation',
                    'GPS navigation',
                    'Math skills',
                    'Inventory management',
                ],
            ],
            [
                'name' => 'Mechanics and Repair Technicians',
                'children' => [
                    'Mechanical systems knowledge',
                    'Tool proficiency',
                    'Quality control',
                    'Diagnostic software proficiency',
                    'Customer service'
                ],
            ],
            [
                'name' => 'Agriculture, Forestry, and Fishing',
                'children' => [
                    'Techniques & equipment knowledge',
                    'Machinery operation',
                    'Physical stamina',
                    'Attention to detail',
                    'Math/budgeting skills',
                ],
            ],
        ];


        foreach ($skills as $skill) {
            $parentSkill = Skill::updateOrCreate([
                'name' => $skill['name'],
            ]);

            foreach ($skill['children'] as $childSkill) {
                Skill::updateOrCreate([
                    'name' => $childSkill,
                    'parent_id' => $parentSkill->id,
                ]);
            }
        }
    }
}
